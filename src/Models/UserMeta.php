<?php

namespace UnderScorer\ORM\Models;

/**
 * Class UserMeta
 * @package UnderScorer\ORM\WP
 *
 * @property string meta_key
 * @property mixed  meta_value
 *
 */
class UserMeta extends PostMeta
{

    /**
     * @var string
     */
    protected $primaryKey = 'umeta_id';

    /**
     * @var array
     */
    protected $fillable = [
        'meta_key',
        'meta_value',
        'user_id'
    ];

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->getConnection()->db->usermeta;
    }

}
