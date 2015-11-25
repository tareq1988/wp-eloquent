<?php

namespace WeDevs\ORM\WP;


use WeDevs\ORM\Eloquent\Model;

/**
 * Class Post
 *
 * @package WeDevs\ORM\WP
 */
class Post extends Model
{

    protected $post_type = null;
    protected $primaryKey = 'ID';

    const CREATED_AT = 'post_date';
    const UPDATED_AT = 'post_modified';

    /**
     * Filter by post type
     *
     * @param $query
     * @param string $type
     *
     * @return mixed
     */
    public function scopeType($query, $type = 'post')
    {
        return $query->where('post_type', '=', $type);
    }

    /**
     * Filter by post status
     *
     * @param $query
     * @param string $status
     *
     * @return mixed
     */
    public function scopeStatus($query, $status = 'publish')
    {
        return $query->where('post_status', '=', $status);
    }

    /**
     * Filter by post author
     *
     * @param $query
     * @param null $author
     *
     * @return mixed
     */
    public function scopeAuthor($query, $author = null)
    {
        if ($author) {
            return $query->where('post_author', '=', $author);
        }
    }

    /**
     * Get comments from the post
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany('WeDevs\ORM\WP\Comment', 'comment_post_ID');
    }

    /**
     * Get meta fields from the post
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function meta()
    {
        return $this->hasMany('WeDevs\ORM\WP\PostMeta', 'post_id');
    }
}