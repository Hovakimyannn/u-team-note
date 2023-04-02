<?php

namespace App\Http\Controllers\Traits;

use App\Models\Tag;
use Illuminate\Support\Collection;

trait InteractsWithTags
{
    /**
     * @param string $tagName
     *
     * @return void
     */
    public function checkDbAndSaveNonExistentTags(string $tagName): void
    {
        $tagFromDb = Tag::where('name', $tagName)->value('name');

        if ($tagName === $tagFromDb) {
            return;
        }

        $tag = new Tag();
        $tag->name = $tagName;
        $tag->save();
    }
}