<?php

namespace App\Gates;

use App\Models\Note;
use App\Models\Tag;
use Error;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;

class NoteGate
{
    public function isOwner(Authenticatable $user) : bool
    {
        Tag::whereIn();
        try {
            return !$user->getCoursesIds()->intersect(app()->request->get('courseId'))->isEmpty();
        } catch (Exception|Error) {
            return false;
        }
    }
}
