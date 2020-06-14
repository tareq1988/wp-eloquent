<?php

namespace AmphiBee\Eloquent\Plugins\Acf\Field;

use AmphiBee\Eloquent\Model\Post;
use AmphiBee\Eloquent\Model;
use AmphiBee\Eloquent\Model\Meta\PostMeta;
use AmphiBee\Eloquent\Model\Term;
use AmphiBee\Eloquent\Model\Meta\TermMeta;
use AmphiBee\Eloquent\Model\User;
use AmphiBee\Eloquent\Model\Meta\UserMeta;

/**
 * Class BasicField.
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */
abstract class BasicField
{
    /**
     * @var Model
     */
    protected $post;

    /**
     * @var PostMeta
     */
    protected $postMeta;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var string
     */
    protected $connection;

    /**
     * Constructor method.
     *
     * @param Post $post
     */
    public function __construct(Model $post)
    {
        $this->post = $post;

        if ($post instanceof Post) {
            $this->postMeta = new PostMeta();
        } elseif ($post instanceof Term) {
            $this->postMeta = new TermMeta();
        } elseif ($post instanceof User) {
            $this->postMeta = new UserMeta();
        }

        $this->postMeta->setConnection($post->getConnectionName());
    }

    /**
     * Get the value of a field according it's post ID.
     *
     * @param string $field
     *
     * @return array|string
     */
    public function fetchValue($field)
    {
        $postMeta = $this->postMeta->where(
           $this->getKeyName(), $this->post->getKey()
        )->where('meta_key', $field)->first();

        if (isset($postMeta->meta_value) and ! is_null($postMeta->meta_value)) {
            $value = $postMeta->meta_value;
            if ($array = @unserialize($value) and is_array($array)) {
                $this->value = $array;

                return $array;
            } else {
                $this->value = $value;

                return $value;
            }
        }
    }

    /**
     * @param string $fieldName
     *
     * @return string
     */
    public function fetchFieldKey($fieldName)
    {
        $this->name = $fieldName;

        $postMeta = $this->postMeta->where($this->getKeyName(), $this->post->getKey())
            ->where('meta_key', '_' . $fieldName)
            ->first();

        if (!$postMeta) {
            return null;
        }

        $this->key = $postMeta->meta_value;

        return $this->key;
    }

    /**
     * @param string $fieldKey
     *
     * @return string|null
     */
    public function fetchFieldType($fieldKey)
    {
        $post = Post::on($this->post->getConnectionName())
                   ->orWhere(function ($query) use ($fieldKey) {
                       $query->where('post_name', $fieldKey);
                       $query->where('post_type', 'acf-field');
                   })->first();

        if ($post) {
            $fieldData = unserialize($post->post_content);
            $this->type = isset($fieldData['type']) ? $fieldData['type'] : 'text';

            return $this->type;
        }

        return null;
    }

    /**
     * Get the name of the key for the field.
     *
     * @return string
     */
    public function getKeyName()
    {
        if ($this->post instanceof Post) {
            return 'post_id';
        } elseif ($this->post instanceof Term) {
            return 'term_id';
        } elseif ($this->post instanceof User) {
            return 'user_id';
        }
    }


    /**
     * @return mixed
     */
    public function __toString()
    {
        return $this->get();
    }
}
