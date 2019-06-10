<?php

namespace WeDevs\ORM\WP;


use Illuminate\Database\Eloquent\Relations\HasMany;
use WeDevs\ORM\Eloquent\Model;

class User extends Model {

    /**
     * @var string
     */
    protected $primaryKey = 'ID';

    /**
     * @var bool
     */
    protected $timestamp = false;

    /**
     * @return HasMany
     */
    public function meta() {
        return $this->hasMany( UserMeta::class, 'user_id' );
    }

}
