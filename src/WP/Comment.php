<?php

namespace UnderScorer\ORM\WP;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\HasOne;
use UnderScorer\ORM\Eloquent\Model;

/**
 * Class Comment
 * @package UnderScorer\ORM\WP
 *
 * @property int    comment_ID
 * @property int    comment_post_ID
 * @property int    comment_author
 * @property string comment_author_email
 * @property string comment_author_url
 * @property string comment_author_IP
 * @property Carbon comment_date
 * @property Carbon comment_date_gmt
 * @property string comment_content
 * @property int    comment_karma
 * @property string comment_approved
 * @property string comment_agent
 * @property string comment_type
 * @property int    comment_parent
 * @property int    user_id
 */
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
        return $this->hasOne( Post::class, 'comment_post_ID' );
    }

    /**
     * User relation to a comment
     *
     * @return HasOne
     */
    public function user() {
        return $this->hasOne( User::class, 'user_id' );
    }

}
