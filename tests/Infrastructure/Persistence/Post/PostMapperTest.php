<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Post;

use PHPUnit\Framework\TestCase;
use App\Infrastructure\Persistence\Gateway\MySQL;
use App\Domain\User\User;
use App\Domain\Category\Category;
use App\Domain\Comment\CommentCollection;
use \Exception;

require_once realpath('./bootstrap/app.php');

class PostMapperTest extends TestCase
{
    use \App\Infrastructure\Persistence\Gateway\Gateway;
    use \App\Infrastructure\Persistence\User\UserTestHelper;
    use \App\Infrastructure\Persistence\Post\PostTestHelper;
    use \App\Infrastructure\Persistence\Category\CategoryTestHelper;
    use \App\Infrastructure\Persistence\Comment\CommentTestHelper;

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

        $prepared = $pdo->prepare('TRUNCATE comment');
        $prepared->execute();
    }

    public function testShouldStoreComments(): void
    {
        $author     = $this->givenAStoredUser();
        $category   = $this->givenAStoredCategory();
        $post       = $this->givenAPost($category, $author);

        $postDAO    = $this->givenAPostDAO();
        $commentDAO = $this->givenACommentDAO();
        $mapper     = $this->givenAPostMapper();

        $comment1   = $this->givenAComment($author);
        $comment2   = $this->givenAComment($author);
        $comment3   = $this->givenAComment($author);
        
        $post->add($comment1);
        $post->add($comment2);
        $post->add($comment3);
        
        $this->assertEquals(1, $mapper->store($post, $postDAO, $commentDAO));
    }

    public function testShouldGetCategory(): void
    {
        $author     = $this->givenAStoredUser();
        $category   = $this->givenAStoredCategory();
        $post       = $this->givenAStoredPost($category, $author);

        $mapper     = $this->givenAPostMapper();
        $belongsTo  = $mapper->getCategory($post);

        $this->assertInstanceOf(Category::class, $belongsTo);
        $this->assertEquals($category->getTitle(), $belongsTo->getTitle());
        $this->assertEquals($category->getDescription(), $belongsTo->getDescription());
    }

    public function testShouldGetAuthor(): void
    {
        $author     = $this->givenAStoredUser();
        $category   = $this->givenAStoredCategory();
        $post       = $this->givenAStoredPost($category, $author);

        $mapper     = $this->givenAPostMapper();
        $belongsTo  = $mapper->getAuthor($post);

        $this->assertInstanceOf(User::class, $belongsTo);
        $this->assertEquals($author->getName(), $belongsTo->getName());
        $this->assertEquals($author->getSurname(), $belongsTo->getSurname());
        $this->assertEquals($author->getEmail(), $belongsTo->getEmail());
    }

    public function testShouldGetComments(): void 
    {
        $author        = $this->givenAStoredUser();
        $category      = $this->givenAStoredCategory();
        $post          = $this->givenAPost($category, $author);

        $postDAO       = $this->givenAPostDAO();
        $commentDAO    = $this->givenACommentDAO();
        $postMapper    = $this->givenAPostMapper();
        $commentMapper = $this->givenACommentMapper();

        $comment1      = $this->givenAComment($author);
        $comment2      = $this->givenAComment($author);
        $comment3      = $this->givenAComment($author);
        
        $post->add($comment1);
        $post->add($comment2);
        $post->add($comment3);

        $postMapper->store($post, $postDAO, $commentDAO);

        $hasMany = $postMapper->getComments($post, $commentMapper);

        $this->assertInstanceOf(CommentCollection::class, $hasMany);
        $this->assertEquals(3, $hasMany->count());
    }
}
