<?php

namespace AmphiBee\Eloquent\Model\User;

use AmphiBee\Eloquent\Model\BaseMeta;

/**
 * Class Meta
 *
 * @package AmphiBee\Eloquent\Model\User
 */
class Meta extends BaseMeta
{
    /** @var string */
    protected $table   = 'usermeta';
    
    /** @var string */
    protected $primaryKey = 'umeta_id';

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function user()
    {
        return $this->belongsTo(\AmphiBee\Eloquent\Model\User::class);
    }
}
