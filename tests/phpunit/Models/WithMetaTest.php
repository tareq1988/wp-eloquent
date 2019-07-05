<?php

namespace UnderScorer\ORM\Tests\Models;

use UnderScorer\ORM\Models\Post;
use UnderScorer\ORM\Tests\TestCase;

/**
 * Class WithMetaTest
 * @package UnderScorer\ORM\Tests\ORM\Models
 */
final class WithMetaTest extends TestCase
{

    /**
     * @covers WithMeta::updateMeta
     */
    public function testShouldUpdateMeta(): void
    {
        /**
         * @var Post $post
         */
        $post = $this->postFactory->create();

        $post->addMeta( 'test', 'test' );
        $post->updateMeta( 'test', 'test1' );

        $this->assertEquals(
            'test1',
            $post->getSingleMeta( 'test' )->getMetaValue()
        );
    }

    /**
     * @covers WithMeta::updateMeta
     */
    public function testUpdateMetaShouldCreateMetaIfItDoesNotExist(): void
    {
        /**
         * @var Post $post
         */
        $post = $this->postFactory->create();

        $post->updateMeta( 'test', 'test1' );

        $this->assertEquals(
            'test1',
            $post->getSingleMeta( 'test' )->getMetaValue()
        );
    }

}
