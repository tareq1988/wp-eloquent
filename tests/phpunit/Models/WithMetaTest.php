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
     * @var Post
     */
    protected $post;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->post = $this->postFactory->create();
    }

    /**
     * @covers WithMeta::updateMeta
     */
    public function testShouldUpdateMeta(): void
    {
        $post = $this->post;

        $post->addMeta( 'test', 'test' );
        $post->updateMeta( 'test', 'test1' );

        $this->assertEquals(
            'test1',
            $post->getSingleMeta( 'test' )
        );
    }

    /**
     * @covers WithMeta::updateMeta
     */
    public function testUpdateMetaShouldCreateMetaIfItDoesNotExist(): void
    {
        $post = $this->post;

        $post->updateMeta( 'test', 'test1' );

        $this->assertEquals(
            'test1',
            $post->getSingleMeta( 'test' )
        );
    }

    /**
     * @covers WithMeta::deleteMeta
     */
    public function testShouldRemoveMeta(): void
    {
        $post = $this->post;

        $post->updateMeta( 'test', 'test1' );
        $this->assertNotEmpty( $post->getSingleMeta( 'test' ) );

        $post->deleteMeta( 'test' );

        $this->assertEmpty( $post->getSingleMeta( 'test' ) );
    }

    /**
     * @covers WithMeta::deleteMeta
     */
    public function testShouldDeleteMetaBasingOnValue(): void
    {
        $post = $this->post;

        $post->addMeta( 'test', 'test1' );
        $post->addMeta( 'test', 'test2' );

        $post->deleteMeta( 'test', 'test2' );

        $metas = $post->getMeta( 'test' );

        $this->assertCount( 1, $metas );
    }

}
