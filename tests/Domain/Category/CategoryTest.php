<?php
declare(strict_types=1);

namespace App\Domain\Category;

use PHPUnit\Framework\TestCase;
use App\Domain\Post\Post;
use \InvalidArgumentException;
use \LengthException;

class CategoryTest extends TestCase
{
    public function testShouldCreateCategory(): void
    {
        $title       = 'Test title';
        $description = 'Test description';
        $category    = new Category($title, $description);

        $this->assertEquals($title, $category->getTitle());
        $this->assertEquals($description, $category->getDescription());
        $this->assertEquals(null, $category->getCategory());
    }

    public function testShoudThrowsExceptionIfInvalidTitle(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The title must be filled.');
        
        $title       = '';
        $description = 'Test description';
        $category    = new Category($title, $description);
    }

    public function testShoudThrowsExceptionIfTheTitleExceedsTheLimitSize(): void 
    {
        $this->expectException(LengthException::class);
        $this->expectExceptionMessage(sprintf(
            'The title must have a maximum of %d characters.', 
            Category::MAX_TITLE_LENGTH
        ));

        $title       = 'In nibh eros, vulputate eu nunc et, venenatis luctus dolor. 
                        Pellentesque convallis massa vitae augue faucibus posuere. 
                        Proin sagittis neque at luctus luctus. Pellentesque sagittis 
                        risus vel ornare pharetra. Proin ac tortor purus. Class aptent 
                        taciti metus.';
        $description = 'Test description';
        $category    = new Category($title, $description);

    }
    
    public function testShoudThrowsExceptionIfInvalidDescription(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The description must be filled.');

        $title       = 'Test title';
        $description = '';
        $category    = new Category($title, $description);
    }

    public function testShoudThrowsExceptionIfTheDescriptionExceedsTheLimitSize(): void 
    {
        $this->expectException(LengthException::class);
        $this->expectExceptionMessage(sprintf(
            'The description must have a maximum of %d characters.', 
            Category::MAX_DESCRIPTION_LENGTH
        ));

        $title        = 'Test title';
        $description  = 'In nibh eros, vulputate eu nunc et, venenatis luctus dolor. 
                        Pellentesque convallis massa vitae augue faucibus posuere. 
                        Proin sagittis neque at luctus luctus. Pellentesque sagittis 
                        risus vel ornare pharetra. Proin ac tortor purus. Class aptent 
                        taciti metus.';
        $category    = new Category($title, $description);
    }

    public function testShouldCreateSubCategory(): void
    {
        $title       = 'Test title';
        $description = 'Test description';
        
        $parent      = new Category($title, $description);
        $sub         = clone $parent;
        $sub->setCategory($parent);

        $this->assertEquals($parent, $sub->getCategory());
    }

    public function testShouldAddPost(): void
    {
        $category = new Category('Test', 'test');
        $category->setId(1);
        
        $this->assertEquals(0, $category->getPosts()->count());
        
        $post = new Post('Title', 'Description', 'Content');
        $post->setId(1);
        
        $category->add($post);
        $posts = $category->getPosts();

        $this->assertEquals(1, $posts->count());
        $this->assertEquals($post, $posts->getPostById(1));
    }
}
