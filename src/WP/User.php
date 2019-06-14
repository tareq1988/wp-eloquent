<?php

namespace UnderScorer\ORM\WP;

use UnderScorer\ORM\Eloquent\Model;

/**
 * Class User
 * @package UnderScorer\ORM\WP
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
