<?php

namespace App\Repositories;

use App\Models\Note;
use App\Models\Tag;

class NoteRepository extends BaseRepository
{
    public function __construct(Note $model)
    {
        parent::__construct($model);
    }

    /**
     * @param \App\Models\Tag|null $tag
     *
     * @return void
     */
    public function logicWhenTagShouldRemoved(?Tag $tag) : void
    {
        if ($tag && $tag->notes->isEmpty()){
            $tag->delete();
        }
    }
}
