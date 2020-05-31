<?php

namespace AmphiBee\Eloquent\Model;

use AmphiBee\Eloquent\Traits\HasMeta;

/**
 * Class Comment
 *
 * @package AmphiBee\Eloquent\Model
 */
class Comment extends BaseModel
{
    use HasMeta;

    /** @var string */
    protected $table      = 'comments';
    
    /** @var string */
    protected $primaryKey = 'comment_ID';
    
    /** @var array */
    protected $fillable   = [];
    
    /** @var boolean */
    public $timestamps    = false;

    /** @var string */
    public const CREATED_AT = 'comment_date';

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function meta()
    {
        return $this->hasMany(Comment\Meta::class, 'comment_id')
                    ->select(['comment_id', 'meta_key', 'meta_value']);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function user()
    {
        return $this->hasOne(User::class, 'ID', 'user_id');
    }
}
