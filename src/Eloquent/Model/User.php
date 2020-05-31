<?php

namespace AmphiBee\Eloquent\Model;

use AmphiBee\Eloquent\Traits\HasMeta;
use AmphiBee\Eloquent\Traits\HasRoles;

/**
 * Class User
 *
 * @package AmphiBee\Eloquent\Model
 */
class User extends BaseModel
{
    use HasMeta;
    use HasRoles;

    /** @var string */
    protected $table      = 'users';

    /** @var string */
    protected $primaryKey = 'ID';

    /** @var string */
    public const CREATED_AT = 'user_registered';

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function posts()
    {
        return $this->hasMany(Post::class, 'post_author')
                    ->where('post_status', 'publish')
                    ->where('post_type', 'post');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function comments()
    {
        return $this->hasMany(Comment::class, 'user_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function meta()
    {
        return $this->hasMany(User\Meta::class, 'user_id')
                    ->select(['user_id', 'meta_key', 'meta_value']);
    }
}
