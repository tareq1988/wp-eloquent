<?php

namespace WeDevs\ORM\WP;


use WeDevs\ORM\Eloquent\Model;

class UserMeta extends Model
{
    protected static $useOneTableForMultisite = true;
    protected $primaryKey = 'umeta_id';

    public $timestamps    = false;

    public function getTable()
    {
        return $this->getConnection()->db->prefix . 'usermeta';
    }
}