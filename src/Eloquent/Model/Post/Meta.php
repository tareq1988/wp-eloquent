<?php

namespace AmphiBee\Eloquent\Model\Post;

use AmphiBee\Eloquent\Model\BaseMeta;

/**
 * Class Meta
 *
 * @package AmphiBee\Eloquent\Model\Post
 */
class Meta extends BaseMeta
{
    /** @var string */
    protected $table   = 'postmeta';

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function post()
    {
        return $this->belongsTo(\AmphiBee\Eloquent\Model\Post::class);
    }
}
