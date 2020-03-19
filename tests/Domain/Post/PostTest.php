<?php
declare(strict_types=1);

namespace App\Domain\Post;

use PHPUnit\Framework\TestCase;
use App\Domain\Category\Category;
use App\Domain\User\User;
use App\Domain\Comment\Comment;
use \InvalidArgumentException;
use \LengthException;

class PostTest extends TestCase
{   
    public function testShoudCreatePost(): void
    {
        $title       = 'Post title';
        $description = 'Post description';
        $content     = 'Post content';
        $author      = new User('Rafael', 'Felipe', 'manofirmz@gmail.com');
        $post        = new Post($title, $description, $content);
        $post->setAuthor($author);

        $this->assertEquals($title, $post->getTitle());
        $this->assertEquals($description, $post->getDescription());
        $this->assertEquals($content, $post->getContent());
        $this->assertEquals($author, $post->getAuthor());
    }

    public function testShouldThrowsExceptionIfInvalidTitle(): void 
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The title must be filled.');

        $title       = '';
        $description = 'Post description';
        $content     = 'Post content';
        $post        = new Post($title, $description, $content);
    }

    public function testShoudThrowsExceptionIfTheTitleExceedsTheLimitSize(): void 
    {
        $this->expectException(LengthException::class);
        $this->expectExceptionMessage(sprintf(
            'The title must have a maximum of %d characters.', 
            Post::MAX_TITLE_LENGTH
        ));
        
        $title       = 'In nibh eros, vulputate eu nunc et, venenatis luctus dolor. 
                        Pellentesque convallis massa vitae augue faucibus posuere. 
                        Proin sagittis neque at luctus luctus. Pellentesque sagittis 
                        risus vel ornare pharetra. Proin ac tortor purus. Class aptent 
                        taciti metus.';
        $description = 'Post description';
        $content     = 'Post content';
        $post        = new Post($title, $description, $content);
    }

    public function testShouldThrowsExceptionIfInvalidDescription(): void 
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The description must be filled.');

        $title       = 'Post title';
        $description = '';
        $content     = 'Post content';
        $post        = new Post($title, $description, $content);
    }

    public function testShoudThrowsExceptionIfTheDescriptionExceedsTheLimitSize(): void 
    {
        $this->expectException(LengthException::class);
        $this->expectExceptionMessage(sprintf(
            'The description must have a maximum of %d characters.', 
            Post::MAX_DESCRIPTION_LENGTH
        ));
        
        $title       = 'Post title';
        $description = 'In nibh eros, vulputate eu nunc et, venenatis luctus dolor. 
                        Pellentesque convallis massa vitae augue faucibus posuere. 
                        Proin sagittis neque at luctus luctus. Pellentesque sagittis 
                        risus vel ornare pharetra. Proin ac tortor purus. Class aptent 
                        taciti metus.';
        $content     = 'Post content';
        $post        = new Post($title, $description, $content);
    }
    
    public function testShouldThrowsExceptionIfInvalidContent(): void 
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The content must be filled.');

        $title       = 'Post title';
        $description = 'Post description';
        $content     = '';
        $post        = new Post($title, $description, $content);
    }

    public function testShouldSetCategory(): void
    {
        $category = new Category('Category title', 'Category description');
        $post     = new Post('Post title', 'Post description', 'Post content');
        $post->setCategory($category);

        $this->assertEquals($category, $post->getCategory());
    }

    public function testShouldAddCommentIntoPost(): void
    {
        $user    = new User('Rafael', 'Felipe', 'manofirmz@gmail.com');
        $comment = new Comment('Test');
        $comment->setUser($user);
        $post    = new Post('Title', 'Description', 'Content');
        
        $this->assertEquals(0, $post->getComments()->count());

        $post->add($comment);

        $this->assertEquals(1, $post->getComments()->count());
    }
}
