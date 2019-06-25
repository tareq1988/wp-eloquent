<?php

namespace UnderScorer\ORM\Tests\Models;

use UnderScorer\ORM\Models\Post;
use UnderScorer\ORM\Tests\TestCase;

/**
 * Class WithMetaTest
 * @package UnderScorer\ORM\Tests\ORM\Models
 */
class WithMetaTest extends TestCase
{


    public function testShouldUpdateMeta(): void
    {
        /**
         * @var Post $post
         */
        $post = Post::query()->find(
            $this->factory()->post->create()
        );

        $post->addMeta( 'test', 'test' );
        $post->updateMeta( 'test', 'test1' );

        $this->assertEquals(
            'test1',
            $post->getSingleMeta( 'test' )->getMetaValue()
        );
    }

}
