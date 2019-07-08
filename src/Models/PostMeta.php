<?php

namespace UnderScorer\ORM\Models;

use UnderScorer\ORM\Contracts\MetaInterface;
use UnderScorer\ORM\Eloquent\Model;

/**
 * Class PostMeta
 * @package UnderScorer\ORM\WP
 *
 * @property string meta_key
 * @property mixed  meta_value
 */
class PostMeta extends Meta
{

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $fillable = [
        'meta_key',
        'meta_value',
        'post_id'
    ];

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->getConnection()->db->postmeta;
    }
}
