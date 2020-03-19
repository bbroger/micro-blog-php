<?php
declare(strict_types=1);

namespace App\Domain\Post;

use PHPUnit\Framework\TestCase;
use App\Domain\Category\Category;
use \Exception;

class PostCollectionTest extends TestCase
{
    private PostCollection $posts;

    protected function setUp(): void
    {
        parent::setUp();
        $this->posts = new PostCollection();
    }

    public function testShouldAddPost(): void
    {
        $category = $this->givenACategory();
        $post     = $this->givenAPost();
        $post->setCategory($category);

        $this->assertEquals(0, $this->posts->count());

        $this->posts->add($post);

        $this->assertEquals(1, $this->posts->count());
    }

    public function testShouldGetPostById(): void 
    {
        $post = $this->givenAPost();

        $this->posts->add($post);

        $this->assertEquals($post, $this->posts->getPostById($post->getId()));
    }

    public function testShouldThrowsExceptionIfPostIsEmpty(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('There are no registered posts.');

        $this->posts->getPostById(1);
    }

    public function testShouldThrowsExceptionIfPostNotFound(): void
    {
        $id   = 2;
        $post = $this->givenAPost();

        $this->posts->add($post);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(sprintf('Post with id %d not found.', $id));

        $this->posts->getPostById($id);
    }

    public function testShouldGetPostByCategory(): void
    {
        $category = $this->givenACategory();
        $post     = $this->givenAPost();
        $post->setCategory($category);

        $this->posts->add($post);

        $postsByCategory = $this->posts->getPostsByCategory($category);

        $this->assertInstanceOf(PostCollection::class, $postsByCategory);
        $this->assertEquals(1, $postsByCategory->count());
        $this->assertEquals($post, $postsByCategory->getPostById($post->getId()));
    }

    public function testShouldThrowsExceptionIfNoPostsRegisteredInTheCategory(): void
    {
        $category1 = $this->givenACategory();
        $category2 = $this->givenACategory(2);
        
        $post      = $this->givenAPost();
        $post->setCategory($category1);

        $this->posts->add($post);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(sprintf(
            'There are no posts registered in the category of id %d', 
            $category2->getId()
        ));

        $this->posts->getPostsByCategory($category2);
    }

    /**
     * @return Post
     */
    private function givenAPost(): Post 
    {
        $post = new Post('Post title', 'Post description', 'Post content');
        $post->setId(1);

        return $post;
    }

    /**
     * @return Category
     */
    private function givenACategory($id = null): Category 
    {
        $category = new Category('Category title', 'Category description');
        $category->setId($id ? $id : 1);

        return $category;
    }
}
