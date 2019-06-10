<?php

namespace WeDevs\ORM\WP;

use Illuminate\Database\Eloquent\Relations\HasOne;
use WeDevs\ORM\Eloquent\Model;

class Comment extends Model {

    /**
     * @var string
     */
    protected $primaryKey = 'comment_ID';

    /**
     * Post relation for a comment
     *
     * @return HasOne
     */
    public function post() {
        return $this->hasOne( Post::class );
    }

}
