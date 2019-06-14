<?php

namespace UnderScorer\ORM\Tests;

use UnderScorer\ORM\Tests\Factories\PostFactory;
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
     * @return void
     */
    public function setUp(): void {
        parent::setUp();

        $this->postFactory = new PostFactory( $this->factory()->post );
    }

}
