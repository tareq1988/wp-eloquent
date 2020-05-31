<?php

namespace AmphiBee\Eloquent\Model;

use AmphiBee\Eloquent\Traits\HasWordPressDbConn;
use As247\WpEloquent\Database\Eloquent\Model as EloquentModel;

/**
 * Class BaseModel
 *
 * @package AmphiBee\Eloquent\Model
 */
class BaseModel extends EloquentModel
{
    use HasWordPressDbConn;

    /** @var string */
    public $timestamps = false;
}
