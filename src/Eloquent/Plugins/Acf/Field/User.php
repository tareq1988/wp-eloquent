<?php

namespace AmphiBee\Eloquent\Plugins\Acf\Field;

use AmphiBee\Eloquent\Plugins\Acf\FieldInterface;
use AmphiBee\Eloquent\Model\Post;

/**
 * Class User.
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class User extends BasicField implements FieldInterface
{
    /**
     * @var \AmphiBee\Eloquent\Model\User
     */
    protected $user;

    /**
     * @var \AmphiBee\Eloquent\Model\User
     */
    protected $value;

    /**
     * @param Post $post
     */
    public function __construct(Post $post)
    {
        parent::__construct($post);
        $this->user = new \AmphiBee\Eloquent\Model\User();
        $this->user->setConnection($post->getConnectionName());
    }

    /**
     * @param string $fieldName
     */
    public function process($fieldName)
    {
        $userId = $this->fetchValue($fieldName);
        $this->value = $this->user->find($userId);
    }

    /**
     * @return \AmphiBee\Eloquent\Model\User
     */
    public function get()
    {
        return $this->value;
    }
}
