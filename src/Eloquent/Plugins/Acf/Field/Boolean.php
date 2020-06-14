<?php

namespace AmphiBee\Eloquent\Plugins\Acf\Field;

use AmphiBee\Eloquent\Plugins\Acf\FieldInterface;

/**
 * Class Boolean.
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class Boolean extends Text implements FieldInterface
{
    /**
     * @return bool
     */
    public function get()
    {
        return (bool) parent::get();
    }
}
