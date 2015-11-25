<?php

namespace WeDevs\ORM\WP;


use WeDevs\ORM\Eloquent\Model;

class Comment extends Model
{
    protected $primaryKey = 'comment_ID';

    /**
     * Post relation for a comment
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function post()
    {
        return $this->hasOne('WeDevs\ORM\WP\Post');
    }
}