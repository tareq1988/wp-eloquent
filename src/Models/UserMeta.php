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
     * @return string
     */
    public function getTable()
    {
        return $this->getConnection()->db->prefix . 'usermeta';
    }

}
