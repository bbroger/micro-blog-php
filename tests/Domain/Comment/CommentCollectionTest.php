<?php
declare(strict_types=1);

namespace App\Domain\Comment;

use PHPUnit\Framework\TestCase;
use App\Domain\Post\Post;

class CommentCollectionTest extends TestCase
{
    private CommentCollection $comments;

    protected function setUp(): void
    {
        $this->comments = new CommentCollection();
    }

    public function testShouldAddComment(): void
    {
        $comment = $this->givenAComment(1);

        $this->assertEquals(0, $this->comments->count());

        $this->comments->add($comment);

        $this->assertEquals(1, $this->comments->count());
    }

    public function testShouldGetCommentById(): void
    {
        $comment  = $this->givenAComment(1);

        $this->comments->add($comment);

        $this->assertEquals($comment, $this->comments->getCommentById($comment->getId()));
    }

    public function testShouldGetCommentsByPost(): void
    {
        $post     = $this->givenAPost(1);
        $comment1 = $this->givenAComment(1, $post);
        $comment2 = $this->givenAComment(2, $post);

        $this->comments->add($comment1);
        $this->comments->add($comment2);

        $comments = $this->comments->getCommentsByPost($post);
        $copy     = $comments->getAggregates();

        $this->assertEquals(2, $comments->count());
        $this->assertEquals($comment1, array_shift($copy));
        $this->assertEquals($comment2, array_shift($copy));
    }

    public function testShouldReturnEmptyCollectionIfDoesNotHaveCommentsByPostId(): void
    {
        $post1    = $this->givenAPost(1);
        $post2    = $this->givenAPost(2);
        $comment1 = $this->givenAComment(1, $post1);
        $comment2 = $this->givenAComment(2, $post1);

        $this->comments->add($comment1);
        $this->comments->add($comment2);

        $comments = $this->comments->getCommentsByPost($post2);

        $this->assertEquals(0, $comments->count());
    }

    /**
     * @return Comment
     */
    private function givenAComment(int $id, Post $post = null): Comment
    {
        $comment = new Comment('Test');
        $comment->setId($id);
        
        if ($post) {
            $comment->setPost($post);
        }

        return $comment;
    }

    private function givenAPost(int $id): Post
    {
        $post = new Post('Title', 'Description', 'Content');
        $post->setId($id);

        return $post;
    }
}
