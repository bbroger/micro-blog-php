<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Comment;

use PHPUnit\Framework\TestCase;
use App\Infrastructure\Persistence\Gateway\GatewayInterface;
use App\Infrastructure\Persistence\Gateway\MySQL;
use App\Domain\Comment\Comment;
use \Exception;

class MySQLCommentDAOTest extends TestCase
{
    use \App\Infrastructure\Persistence\User\UserTestHelper;
    use \App\Infrastructure\Persistence\Category\CategoryTestHelper;
    use \App\Infrastructure\Persistence\Post\PostTestHelper;
    use \App\Infrastructure\Persistence\Comment\CommentTestHelper;

    private GatewayInterface $gateway;

    protected function setUp(): void
    {
        $this->gateway = new MySQL();
    }

    protected function tearDown(): void
    {
        $pdo     = $this->gateway->getConnection();
        
        $prepared = $pdo->prepare('TRUNCATE user');
        $prepared->execute();

        $prepared = $pdo->prepare('TRUNCATE category');
        $prepared->execute();

        $prepared = $pdo->prepare('TRUNCATE post');
        $prepared->execute();

        $prepare = $pdo->prepare('TRUNCATE comment');
        $prepare->execute();
    }

    public function testShouldStoreComment(): void
    {
        $user     = $this->givenAStoredUser();
        $category = $this->givenAStoredCategory();
        $post     = $this->givenAStoredPost($category, $user);
        $comment  = $this->givenAStoredComment($user, $post);

        $this->assertEquals(1, $comment->getId());
    }

    public function testShouldFindCommentById(): void
    {
        $user       = $this->givenAStoredUser();
        $category   = $this->givenAStoredCategory();
        $post       = $this->givenAStoredPost($category, $user);
        
        $commentDAO = $this->givenACommentDAO();
        $comment    = $this->givenAStoredComment($user, $post);
        $object     = $commentDAO->findById($comment->getId());

        $this->assertInstanceOf(Comment::class, $object);
        $this->assertEquals($comment->getComment(), $object->getComment());
    }

    public function testShouldThrowExceptionIfCommentNotFound(): void
    {
        $id = 1;
        
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(sprintf('Comment with id %d not found.', $id));

        $this->givenACommentDAO()->findById($id);
    }

    public function testShouldUpdateComment()
    {
        $user       = $this->givenAStoredUser();
        $category   = $this->givenAStoredCategory();
        $post       = $this->givenAStoredPost($category, $user);
        
        $commentDAO = $this->givenACommentDAO();
        $comment    = $this->givenAStoredComment($user, $post);
        $comment->setComment('New comment');

        $this->assertEquals(1, $commentDAO->update($comment));

        $object     = $commentDAO->findById($comment->getId());

        $this->assertEquals($comment->getComment(), $object->getComment());
    }

    public function testShouldThrowAnExceptionWhenTryingToUpdateACommentThatDoesNotExist(): void
    {
        $user       = $this->givenAStoredUser();
        $category   = $this->givenAStoredCategory();
        $post       = $this->givenAStoredPost($category, $user);
        
        $commentDAO = $this->givenACommentDAO();
        $comment    = $this->givenAStoredComment($user, $post);
        $comment->setId(2);
        $comment->setComment('New comment');

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(sprintf(
            'Comment with id %d not found.', 
            $comment->getId()
        ));

        $commentDAO->update($comment);
    }

    public function testShouldDestroyComment(): void
    {
        $user       = $this->givenAStoredUser();
        $category   = $this->givenAStoredCategory();
        $post       = $this->givenAStoredPost($category, $user);
        
        $commentDAO = $this->givenACommentDAO();
        $comment    = $this->givenAStoredComment($user, $post);

        $this->assertEquals(1, $commentDAO->destroy($comment));
    }

    public function testShouldThrowAnExceptionWhenTryingToDestroyACommentThatDoesNotExist(): void
    {
        $user       = $this->givenAStoredUser();
        $category   = $this->givenAStoredCategory();
        $post       = $this->givenAStoredPost($category, $user);
        
        $commentDAO = $this->givenACommentDAO();
        $comment    = $this->givenAStoredComment($user, $post);
        $comment->setId(2);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(sprintf(
            'Comment with id %d not found.', 
            $comment->getId()
        ));

        $commentDAO->destroy($comment);
    }
}
