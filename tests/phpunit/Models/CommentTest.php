<?php

namespace UnderScorer\ORM\Tests\Models;

use UnderScorer\ORM\Tests\TestCase;
use UnderScorer\ORM\Models\Comment;
use UnderScorer\ORM\Models\Post;
use UnderScorer\ORM\Models\User;

/**
 * Class CommentTest
 * @package UnderScorer\ORM\Tests\ORM\WP
 */
final class CommentTest extends TestCase
{

    /**
     * @covers \UnderScorer\ORM\Models\Comment::post
     */
    public function testHasRelationToPost(): void
    {

        /**
         * @var Post $post
         */
        $post = $this->postFactory->create();

        /**
         * @var Comment $comment
         */
        $comment = $post->comments()->create( [
            'comment_content' => 'Test comment',
        ] );

        $commentPost = $comment->post;

        $this->assertEquals(
            $post->post_title,
            $commentPost->post_title
        );

    }

    /**
     * @covers \UnderScorer\ORM\Models\Comment::post
     */
    public function testHasRelationToUser(): void
    {

        /**
         * @var User $user
         */
        $user = $this->userFactory->create();

        User::current();

        /**
         * @var Comment $comment
         */
        $comment = $user->comments()->create( [
            'comment_content' => 'Test comment',
        ] );

        $commentPost = $comment->user;

        $this->assertEquals(
            $user->user_login,
            $commentPost->user_login
        );

    }

}
