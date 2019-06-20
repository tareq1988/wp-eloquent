<?php

namespace UnderScorer\ORM\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
 * @property User   user
 * @property Post   post
 */
class Comment extends Model
{

    /**
     * @var string
     */
    const CREATED_AT = 'comment_date';

    /**
     * @var array
     */
    protected $fillable = [
        'comment_parent',
        'comment_type',
        'comment_agent',
        'comment_approved',
        'comment_karma',
        'comment_content',
        'comment_author_IP',
        'comment_author_url',
        'comment_author_email',
        'comment_post_ID',
        'comment_author',
    ];

    /**
     * @var string
     */
    protected $primaryKey = 'comment_ID';

    /**
     * @var array
     */
    protected $dates = [
        'comment_date',
        'comment_date_gmt',
    ];

    /**
     * Post relation for a comment
     *
     * @return BelongsTo
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo( Post::class, 'comment_post_ID' );
    }

    /**
     * User relation to a comment
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo( User::class, 'user_id' );
    }

    /**
     * Disable updated_at feature
     *
     * @param $value
     */
    public function setUpdatedAtAttribute($value)
    {

    }

}
