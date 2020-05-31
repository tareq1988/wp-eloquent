<?php

namespace AmphiBee\Eloquent\Model;

use AmphiBee\Eloquent\Core\Helpers;

/**
 * Class Option
 *
 * @package AmphiBee\Eloquent\Model
 */
class Option extends BaseModel
{
    
    /** @var string */
    protected $table      = 'options';
    
    /** @var string */
    protected $primaryKey = 'option_id';

    public static function getValue($key = '')
    {
        $value = '';

        if ($key) {
            $value = self::where('option_name', '=', $key)->value('option_value');
        }

        if (Helpers::isSerialized($value)) {
            $value = unserialize($value);
        }

        return $value;
    }
}
