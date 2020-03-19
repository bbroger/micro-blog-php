<?php
declare(strict_types=1);

namespace App\Domain\Comment;

use PHPUnit\Framework\TestCase;
use App\Domain\Category\Category;
use App\Domain\Post\Post;
use App\Domain\User\User;
use \InvalidArgumentException;
use \LengthException;

class CommentTest extends TestCase
{
    private Category $category;
    
    private Post $post;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = new Category('Title', 'description');
        $this->category->setId(1);
        
        $this->post     = new Post('Title', 'Description', 'Content');
        $this->post->setId(1);
        $this->post->setCategory($this->category);

        $this->user     = new User('Rafael', 'Felipe', 'manofirmz@gmail.com');
    }

    public function testShouldCreateComment(): void
    {
        $content = 'Comment test';
        $comment = new Comment($content);
        $comment->setPost($this->post);
        $comment->setUser($this->user);

        $this->assertEquals($content, $comment->getComment());
        $this->assertEquals($this->post, $comment->getPost());
        $this->assertEquals($this->user, $comment->getUser());
    }

    public function testShouldThrowsExceptionIfEmptyComment(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The comment must be filled.');
        
        $content = '';
        $comment = new Comment($content);
    }

    public function testShoudThrowsExceptionIfTheCommentExceedsTheLimitSize(): void 
    {
        $this->expectException(LengthException::class);
        $this->expectExceptionMessage(sprintf(
            'The comment must have a maximum of %d characters.', 
            Comment::MAX_COMMENT_LENGTH
        ));
        
        $content = 'In nibh eros, vulputate eu nunc et, venenatis luctus dolor. 
                    Pellentesque convallis massa vitae augue faucibus posuere. 
                    Proin sagittis neque at luctus luctus. Pellentesque sagittis 
                    risus vel ornare pharetra. Proin ac tortor purus. Class aptent 
                    taciti metus.';
        $comment = new Comment($content);
    }
}
