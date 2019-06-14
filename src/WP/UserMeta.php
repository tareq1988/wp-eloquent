<?php

namespace UnderScorer\ORM\WP;

use UnderScorer\ORM\Eloquent\Model;

class UserMeta extends Model {

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var string
     */
    protected $primaryKey = 'meta_id';


    /**
     * @return string
     */
    public function getTable() {
        return $this->getConnection()->db->prefix . 'usermeta';
    }

}
