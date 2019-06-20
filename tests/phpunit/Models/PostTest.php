<?php

namespace UnderScorer\ORM\Tests\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use UnderScorer\ORM\Models\Post;
use UnderScorer\ORM\Models\User;
use UnderScorer\ORM\Tests\TestCase;
use WP_Post;

/**
 * Class PostTest
 * @package UnderScorer\Tests\ORM\WP
 */
final class PostTest extends TestCase
{

    /**
     * @covers \UnderScorer\ORM\Models\Post::taxonomy
     * @covers \UnderScorer\ORM\Models\Post::addTerms
     * @covers \UnderScorer\ORM\Models\Post::taxonomies
     */
    public function testHasRelationshipWithTaxonomies(): void
    {

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
     * @covers \UnderScorer\ORM\Models\Post::meta
     * @covers \UnderScorer\ORM\Models\Post::addMeta
     * @covers \UnderScorer\ORM\Models\Post::getSingleMeta
     */
    public function testHasRelationshipWithMeta(): void
    {

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
     * @covers \UnderScorer\ORM\Models\Post::meta
     */
    public function testCanQueryByMetaValue(): void
    {

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
     * @covers \UnderScorer\ORM\Models\Post::taxonomies
     */
    public function testCanQueryByTerms(): void
    {

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

    /**
     * @covers \UnderScorer\ORM\Models\Post
     */
    public function testReturnsCarbonInstanceForDateProperties(): void
    {

        /**
         * @var Post $post
         */
        $post = $this->postFactory->create();

        $this->assertInstanceOf(
            Carbon::class,
            $post->post_date
        );

        $this->assertInstanceOf(
            Carbon::class,
            $post->post_date_gmt
        );

        $this->assertInstanceOf(
            Carbon::class,
            $post->post_modified
        );

    }

    /**
     * @covers \UnderScorer\ORM\Models\Post
     * @covers \UnderScorer\ORM\Models\Post::update
     */
    public function testIsUpdatingModifiedDateOnUpdate(): void
    {

        /**
         * @var Post $post
         */
        $post = $this->postFactory->create();

        $this->assertEquals(
            $post->post_modified->toDateTimeString(),
            Carbon::now()->toDateTimeString()
        );

        sleep( 1 );

        $post->post_title = 'Updated!';
        $post->update();

        $this->assertEquals(
            $post->post_modified->toDateTimeString(),
            Carbon::now()->toDateTimeString()
        );

    }

    /**
     * @covers \UnderScorer\ORM\Models\Post::author
     */
    public function testHasRelationToUser(): void
    {

        /**
         * @var User $user
         */
        $user = $this->userFactory->create();

        /**
         * @var Post $post
         */
        $post = $this->postFactory->create( [
            'post_author' => $user->ID,
        ] );

        $author = $post->author;

        $this->assertEquals(
            $author->ID,
            $user->ID
        );

    }

    /**
     * @covers \UnderScorer\ORM\Models\Post::save
     */
    public function testSavingShouldTriggerWordpressHooks(): void
    {
        $post = new Post( [
            'post_title'   => 'Some title',
            'post_content' => 'Some content',
        ] );

        $hooksCount = 0;

        add_action( 'save_post', function ( int $ID, WP_Post $post ) use ( &$hooksCount ) {
            $this->assertEquals( 'Some title', $post->post_title );
            $this->assertEquals( 'Some content', $post->post_content );

            $hooksCount ++;
        }, 10, 2 );

        add_action( 'wp_insert_post', function ( int $ID, WP_Post $post, bool $update ) use ( &$hooksCount ) {
            $this->assertEquals( 'Some title', $post->post_title );
            $this->assertEquals( 'Some content', $post->post_content );
            $this->assertFalse( $update );

            $hooksCount ++;
        }, 10, 3 );

        $post->save();

        $this->assertEquals( 2, $hooksCount );
    }

    /**
     * @covers \UnderScorer\ORM\Models\Post::save
     */
    public function testUpdatingShouldTriggerWordPressHooks(): void
    {

        /**
         * @var Post $post
         */
        $post = $this->postFactory->create( [
            'post_title'   => 'Old title',
            'post_content' => 'Old content',
        ] );

        $post->post_title   = 'New title!';
        $post->post_content = 'New content!';

        $hooksCount = 0;

        add_action( 'post_updated', function ( int $ID, WP_Post $post, WP_Post $oldPost ) use ( &$hooksCount ) {
            $this->assertEquals( 'New title!', $post->post_title );
            $this->assertEquals( 'New content!', $post->post_content );

            $this->assertEquals( 'Old title', $oldPost->post_title );
            $this->assertEquals( 'Old content', $oldPost->post_content );

            $hooksCount++;
        }, 10, 3 );


        add_action( 'edit_post', function ( int $ID, WP_Post $post ) use ( &$hooksCount ) {
            $this->assertEquals( 'New title!', $post->post_title );
            $this->assertEquals( 'New content!', $post->post_content );

            $hooksCount++;
        }, 10, 2 );

        add_action( 'save_post', function ( int $ID, WP_Post $post, $test = null ) use ( &$hooksCount ) {
            $this->assertEquals( 'New title!', $post->post_title );
            $this->assertEquals( 'New content!', $post->post_content );

            $hooksCount ++;
        }, 10, 3 );

        add_action( 'wp_insert_post', function ( int $ID, WP_Post $post, bool $update ) use ( &$hooksCount ) {
            $this->assertEquals( 'New title!', $post->post_title );
            $this->assertEquals( 'New content!', $post->post_content );
            $this->assertTrue( $update );

            $hooksCount ++;
        }, 10, 3 );

        $post->save();

        $this->assertEquals( 4, $hooksCount );
    }

}
