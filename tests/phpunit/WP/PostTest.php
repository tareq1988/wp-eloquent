<?php

namespace UnderScorer\ORM\Tests\WP;

use Illuminate\Database\Eloquent\Builder;
use UnderScorer\ORM\Tests\TestCase;
use UnderScorer\ORM\WP\Post;

/**
 * Class PostTest
 * @package UnderScorer\Tests\ORM\WP
 */
final class PostTest extends TestCase {

    /**
     * @covers \UnderScorer\ORM\WP\Post::taxonomy
     * @covers \UnderScorer\ORM\WP\Post::addTerms
     * @covers \UnderScorer\ORM\WP\Post::taxonomies
     */
    public function testHasRelationshipWithTaxonomies(): void {

        /**
         * @var Post $post
         */
        $post = $this->postFactory->create();

        $post->addTerms( 'category', [
            [
                'name' => 'Test category',
                'slug' => 'test_category',
            ],
        ] );

        $categories = $post->taxonomy( 'category' );

        // First index, since zero index equals "Uncategorized"
        $this->assertEquals( 'Test category', $categories[ 1 ]->term->name );

    }

    /**
     * @covers \UnderScorer\ORM\WP\Post::meta
     * @covers \UnderScorer\ORM\WP\Post::addMeta
     * @covers \UnderScorer\ORM\WP\Post::getSingleMeta
     */
    public function testHasRelationshipWithMeta(): void {

        /**
         * @var Post $post
         */
        $post = $this->postFactory->create();

        $post->addMeta( 'test_meta', 'This is a test!' );

        $meta = $post->getSingleMeta( 'test_meta' );

        $this->assertEquals(
            'This is a test!',
            $meta->getMetaValue()
        );

    }

    /**
     * @covers \UnderScorer\ORM\WP\Post::meta
     */
    public function testCanQueryByMetaValue(): void {

        /**
         * @var Post $post
         */
        $post = $this->postFactory->create();

        $post->addMeta( 'query_test', 'Query me!' );

        /**
         * @var Post[] $posts
         */
        $posts = Post::query()->whereHas( 'meta', function ( Builder $query ) {
            $query
                ->where( [
                    [ 'meta_key', '=', 'query_test' ],
                    [ 'meta_value', '=', 'Query me!' ],
                ] );
        } )->get();

        $this->assertCount( 1, $posts );

        $this->assertEquals(
            $post->post_title,
            $posts[ 0 ]->post_title
        );

    }

    /**
     * @covers \UnderScorer\ORM\WP\Post::taxonomies
     */
    public function testCanQueryByTerms(): void {

        /**
         * @var Post $post
         */
        $post = $this->postFactory->create();

        $post->addTerms( 'category', [
            [
                'name' => 'Query this category',
                'slug' => 'query_this_category',
            ],
        ] );

        /**
         * @var Post[] $posts
         */
        $posts = Post::query()->whereHas( 'taxonomies', function ( Builder $query ) {

            $query->whereHas( 'term', function ( Builder $query ) {

                $query->where( 'slug', '=', 'query_this_category' );

            } );

        } )->get();

        $this->assertCount( 1, $posts );

        $this->assertEquals(
            $post->post_title,
            $posts[ 0 ]->post_title
        );

    }

}
