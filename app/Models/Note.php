<?php

namespace App\Models;

use App\Models\Traits\AttributesModifier;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string          $title
 * @property string          $content
 * @property string|null     $media
 * @property string          $user_role
 * @property int             $user_id
 * @property int|null        $tag_id
 * @property \App\Models\Tag $tag
 */
class Note extends Model
{
    use HasFactory,
        AttributesModifier;

    const NOTE_MEDIA_STORAGE = 'storage/media/note/';

    protected $with = [
        'tag',
    ];

    protected $fillable = [
        'title',
        'content',
        'user_role',
        'user_id',
        'tag_id',
        'media',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }

    /**
     * added absolute url for media files
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function media(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => asset(self::NOTE_MEDIA_STORAGE.$value),
        );
    }
}
