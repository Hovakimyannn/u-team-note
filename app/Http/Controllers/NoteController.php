<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\InteractsWithTags;
use App\Models\Note;
use App\Models\Tag;
use App\Repositories\NoteRepository;
use App\Services\ImageAdapter\ImageAdapter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NoteController extends Controller
{
    use InteractsWithTags;

    public function __construct(
        protected NoteRepository $noteRepository,
        protected ImageAdapter $imageAdapter
    ) {
        $this->imageAdapter->supportHeight = 800;
        $this->imageAdapter->supportWidth = 600;
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @param int|null                 $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request, int $id = null): JsonResponse
    {
        $from = $request->from ?? 0;
        $offset = $request->offset ?? 10;

        $notes = $this->noteRepository->paginateBy(
            [
                [
                    'tag_id',
                    $id
                ]
            ],
            $from,
            $offset
        );

        $nextUrl = sprintf(
            '/api/note?from=%d&offset=%d',
            $from + $offset,
            10
        );

        if ($notes->count() != $offset) {
            $nextUrl = null;
        }

        return new JsonResponse([
            'notes'   => $notes,
            'nextUrl' => $nextUrl
        ], JsonResponse::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        return new JsonResponse($this->noteRepository->find($id), JsonResponse::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $this->validate($request, [
            'title'   => ['required', 'string', 'min:3', 'max:100'],
            'content' => ['required', 'string', 'min:3', 'max:32767'],
            'media'   => ['mimes:jpg,jpeg,png'],
            'tag'     => ['string'],
        ]);

        if ($file = $request->file('media')) {
            $image = $this->imageAdapter->make($file);
            $this->imageAdapter->resize($image, $image->width(), $image->height());
            $filename = hash('sha256', $image->filename).'.'.$file->extension();
            $image->save(storage_path('/app/media/note/'.$filename));
        }

        if ($requestTag = $request->get('tag')) {
            $this->checkDbAndSaveNonExistentTags($requestTag);
            $tag = Tag::where('name', $requestTag)->firstOrFail();
        }

        $note = new Note();
        $note->title = $request->get('title');
        $note->content = $request->get('content');
        $note->media = $filename ?? null;
        $note->user_role = $request->user()->getRole();
        $note->user_id = $request->user()->getId();
        $note->tag_id = isset($tag) ? $tag->id : null;

        $note->save();
        $note->tag()->associate($tag ?? null);
        $note->refresh()->load('tag');

        return new JsonResponse($note, JsonResponse::HTTP_CREATED);
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $this->validate($request, [
            'title'   => ['string', 'min:3', 'max:100'],
            'content' => ['string', 'min:3', 'max:3000'],
            'media'   => ['mimes:jpg,jpeg,png'],
            'tag'     => ['string'],
        ]);

        /** @var Note $note */
        $note = $this->noteRepository->find($id);

        if ($file = $request->file('media')) {
            if ($note->media) {
                Storage::disk('note')->delete($note->media);
            }

            $filename = $file->store('/', 'note');
        }

        if ($requestTag = $request->get('tag')) {
            $this->checkDbAndSaveNonExistentTags($requestTag);
            $tag = Tag::where('name', $requestTag)->firstOrFail();
        }

        if ($note->tag_id) {
            $noteTag = $note->tag;
        }

        $note->title = $request->get('title', $note->title);
        $note->content = $request->get('content', $note->content);
        $note->media = $filename ?? null;
        $note->user_role = $request->user()->getRole();
        $note->user_id = $request->user()->getId();
        $note->tag_id = $requestTag && isset($tag) ? $tag->id : null;
        $note->save();
        $note->tag()->associate($tag ?? null);
        $note->refresh()->load('tag');

        if (isset($noteTag)) {
            $this->noteRepository->logicWhenTagShouldRemoved($noteTag);
        }

        return new JsonResponse($note, JsonResponse::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        /** @var Note $note */
        $note = $this->noteRepository->find($id);

        if ($note->media) {
            Storage::disk('note')->delete($note->media);
        }

        $note->delete();

        $this->noteRepository->logicWhenTagShouldRemoved($note->tag);

        return new JsonResponse('deleted', JsonResponse::HTTP_NO_CONTENT);
    }
}
