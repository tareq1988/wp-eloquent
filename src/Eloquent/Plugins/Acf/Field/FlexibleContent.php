<?php

namespace AmphiBee\Eloquent\Plugins\Acf\Field;

use AmphiBee\Eloquent\Plugins\Acf\FieldFactory;
use AmphiBee\Eloquent\Plugins\Acf\FieldInterface;
use AmphiBee\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

/**
 * Class Flexible Content.
 *
 * @author Marco Boom <info@marcoboom.nl>
 */
class FlexibleContent extends BasicField implements FieldInterface
{
    /**
     * @var Collection
     */
    protected $fields;

    /**
     * @param string $fieldName
     */
    public function process($fieldName)
    {
        $this->name = $fieldName;

        $builder = $this->fetchPostsMeta($fieldName, $this->post);
        $fields = $this->fetchFields($fieldName, $builder);

        $this->fields = new Collection($fields);
    }

    /**
     * @return Collection
     */
    public function get()
    {
        return $this->fields;
    }

    /**
     * @param string $metaKey
     * @param string $fieldName
     *
     * @return int
     */
    protected function retrieveIdFromFieldName($metaKey, $fieldName)
    {
        return (int) str_replace("{$fieldName}_", '', $metaKey);
    }

    /**
     * @param string $metaKey
     * @param string $fieldName
     * @param int    $id
     *
     * @return string
     */
    protected function retrieveFieldName($metaKey, $fieldName, $id)
    {
        $pattern = "{$fieldName}_{$id}_";

        return str_replace($pattern, '', $metaKey);
    }

    /**
     * @param $fieldName
     * @param Post $post
     *
     * @return mixed
     */
    protected function fetchPostsMeta($fieldName, Model $post)
    {
        $builder = $this->postMeta->where($this->getKeyName(), $this->post->getKey());
        $builder->where('meta_key', 'like', "{$fieldName}_%");

        return $builder;
    }

    /**
     * @param $fieldName
     * @param $builder
     *
     * @return mixed
     */
    protected function fetchFields($fieldName, Builder $builder)
    {
        $fields = [];
        $blocks  = $this->fetchValue($fieldName, $this->post);

        foreach ($builder->get() as $meta) {
            $id = $this->retrieveIdFromFieldName($meta->meta_key, $fieldName);

            $name = $this->retrieveFieldName($meta->meta_key, $fieldName, $id);

            $post = $this->post->ID != $meta->post_id ? $this->post->find($meta->post_id) : $this->post;
            $field = FieldFactory::make($meta->meta_key, $post);

            if ($field === null || !array_key_exists($id, $blocks)) {
                continue;
            }

            if (empty($fields[$id])) {
                $fields[$id] = new \stdClass;
                $fields[$id]->type = $blocks[$id];
                $fields[$id]->fields =  new \stdClass;
            }

            $fields[$id]->fields->$name = $field->get();
        }

        ksort($fields);

        return $fields;
    }
}
