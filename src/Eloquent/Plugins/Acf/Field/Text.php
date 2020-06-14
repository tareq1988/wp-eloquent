<?php

namespace AmphiBee\Eloquent\Plugins\Acf\Field;

use AmphiBee\Eloquent\Plugins\Acf\FieldInterface;
use AmphiBee\Eloquent\Model\Post;

/**
 * Class Text.
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class Text extends BasicField implements FieldInterface
{
    /**
     * @var string
     */
    protected $value;

    /**
     * @param string $field
     */
    public function process($field)
    {
        $this->value = $this->fetchValue($field);
    }

    /**
     * @return string
     */
    public function get()
    {
        return $this->value;
    }
}
