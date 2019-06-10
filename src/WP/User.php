<?php

namespace WeDevs\ORM\WP;


use WeDevs\ORM\Eloquent\Model;
use WPK\Core\Models\WP\WithMeta;

/**
 * Class User
 * @package WeDevs\ORM\WP
 */
class User extends Model {

    use WithMeta;

    /**
     * @var string
     */
    protected $primaryKey = 'ID';

    /**
     * @var bool
     */
    protected $timestamp = false;

    /**
     * @var string
     */
    protected $metaRelation = UserMeta::class;

    /**
     * @var string
     */
    protected $metaForeignKey = 'user_id';

}
