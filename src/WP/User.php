<?php

namespace UnderScorer\ORM\WP;

use UnderScorer\ORM\Eloquent\Model;

/**
 * Class User
 * @package UnderScorer\ORM\WP
 *
 * @property string user_login
 * @property string user_pass
 * @property string user_nicename
 * @property string user_email
 * @property string user_url
 * @property string user_registered
 * @property string user_activation_key
 * @property string user_status
 * @property string display_name
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

    protected $dates = [
        'user_registered',
    ];

}
