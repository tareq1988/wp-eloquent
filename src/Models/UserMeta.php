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
     * @return string
     */
    public function getTable()
    {
        return $this->getConnection()->db->usermeta;
    }

}
