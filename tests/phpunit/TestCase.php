<?php

namespace UnderScorer\ORM\Tests;

use UnderScorer\ORM\Tests\Factories\CommentFactory;
use UnderScorer\ORM\Tests\Factories\PostFactory;
use UnderScorer\ORM\Tests\Factories\UserFactory;
use WP_Ajax_UnitTestCase as BaseTestCase;

/**
 * Class TestCase
 * @package WPK\Tests
 */
abstract class TestCase extends BaseTestCase {

    /**
     * @var PostFactory
     */
    protected $postFactory;

    /**
     * @var UserFactory
     */
    protected $userFactory;

    /**
     * @var CommentFactory
     */
    protected $commentFactory;

    /**
     * @return void
     */
    public function setUp(): void {
        parent::setUp();

        $this->postFactory    = new PostFactory( $this->factory()->post );
        $this->userFactory    = new UserFactory( $this->factory()->user );
        $this->commentFactory = new CommentFactory( $this->factory()->comment );
    }

}
