<?php

namespace AmphiBee\Eloquent\Plugins\Acf;

use AmphiBee\Eloquent\Plugins\Acf\Exception\MissingFieldNameException;
use AmphiBee\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Class AdvancedCustomFields.
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class AdvancedCustomFields
{
    /**
     * @var mixed
     */
    protected $post;

    /**
     * @param mixed $post
     */
    public function __construct(Model $post)
    {
        $this->post = $post;
    }

    /**
     * @param string $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        $field = FieldFactory::make($name, $this->post);

        return $field ? $field->get() : null;
    }

    /**
     * Make possible to call $post->acf->fieldType('fieldName').
     *
     * @param string$name
     * @param array $arguments
     *
     * @return mixed
     *
     * @throws MissingFieldNameException
     */
    public function __call($name, $arguments)
    {
        if (!isset($arguments[0])) {
            throw new MissingFieldNameException('The field name is missing');
        }

        $field = FieldFactory::make($arguments[0], $this->post, Str::snake($name));

        return $field ? $field->get() : null;
    }
}
