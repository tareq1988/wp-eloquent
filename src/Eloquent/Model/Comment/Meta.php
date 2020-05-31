<?php

namespace AmphiBee\Eloquent\Model\Comment;

use AmphiBee\Eloquent\Model\BaseMeta;

/**
 * Class Comment
 *
 * @package AmphiBee\Eloquent\Model\Comment
 */
class Meta extends BaseMeta
{
    /** @var string */
    protected $table   = 'commentmeta';

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function comment()
    {
        return $this->belongsTo(\AmphiBee\Eloquent\Model\Comment::class);
    }
}
