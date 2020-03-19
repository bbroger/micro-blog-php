<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Post;

use PHPUnit\Framework\TestCase;
use App\Infrastructure\Persistence\Gateway\MySQL;
use App\Domain\Post\Post;
use \Exception;

require_once realpath('./bootstrap/app.php');

class MySQLPostDAOTest extends TestCase
{
    use \App\Infrastructure\Persistence\Gateway\Gateway;
    use \App\Infrastructure\Persistence\User\UserTestHelper;
    use \App\Infrastructure\Persistence\Category\CategoryTestHelper;
    use PostTestHelper;


    protected function setUp(): void
    {
        $this->gateway = new MySQL();
    }

    protected function tearDown(): void
    {
        $pdo      = $this->gateway->getConnection();

        $prepared = $pdo->prepare('TRUNCATE user');
        $prepared->execute();

        $prepared = $pdo->prepare('TRUNCATE category');
        $prepared->execute();

        $prepared = $pdo->prepare('TRUNCATE post');
        $prepared->execute();
    }

    public function testShouldStorePost(): void
    {
        $author   = $this->givenAStoredUser();
        $category = $this->givenAStoredCategory();
        $post     = $this->givenAStoredPost($category, $author);

        $this->assertEquals(1, $post->getId());
    }

    public function testShouldFindPostById(): void
    {
        $author   = $this->givenAStoredUser();
        $category = $this->givenAStoredCategory();
        $postDAO  = $this->givenAPostDAO();
        $post     = $this->givenAStoredPost($category, $author);
        $object   = $postDAO->findById($post->getId());

        $this->assertInstanceOf(Post::class, $object);
        $this->assertEquals($post->getTitle(), $object->getTitle());
        $this->assertEquals($post->getDescription(), $object->getDescription());
        $this->assertEquals($post->getContent(), $object->getContent());
    }

    public function testShouldThrowExceptionIfPostNotFound(): void
    {
        $id = 1;

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(sprintf('Post with id %d not found.', $id));

        $this->givenAPostDAO()->findById($id);
    }

    public function testShouldUpdatePost(): void
    {
        $author   = $this->givenAStoredUser();
        $category = $this->givenAStoredCategory();
        
        $postDAO  = $this->givenAPostDAO();
        $post     = $this->givenAStoredPost($category, $author);
        $post->setTitle('New title');
        $post->setDescription('New description');
        $post->setContent('New content');

        $this->assertEquals(1, $postDAO->update($post));

        $object   = $postDAO->findById($post->getId());

        $this->assertEquals($post->getTitle(), $object->getTitle());
        $this->assertEquals($post->getDescription(), $object->getDescription());
        $this->assertEquals($post->getContent(), $object->getContent());
    }

    public function testShouldThrowAnExceptionWhenTryingToUpdatePostThatDoesNotExist(): void
    {
        $author   = $this->givenAStoredUser();
        $category = $this->givenAStoredCategory();
        $post     = $this->givenAStoredPost($category, $author);
        $post->setId(2);
        $post->setTitle('New title');
        $post->setDescription('New description');
        $post->setContent('New content');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(sprintf(
            'Post with id %d not found.', 
            $post->getId()
        ));

        $this->givenAPostDAO()->update($post);
    }

    public function testShouldDestroyPost(): void
    {
        $author   = $this->givenAStoredUser();
        $category = $this->givenAStoredCategory();
        $post     = $this->givenAStoredPost($category, $author);

        $this->assertEquals(1, $this->givenAPostDAO()->destroy($post));
    }

    public function testShouldThrowAnExceptionWhenTryingToDestroyAPostThatDoesNotExist(): void
    {
        $author   = $this->givenAStoredUser();
        $category = $this->givenAStoredCategory();
        $post     = $this->givenAStoredPost($category, $author);
        $post->setId(2);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(sprintf(
            'Post with id %d not found.', 
            $post->getId()
        ));

        $this->givenAPostDAO()->destroy($post);
    }
}
