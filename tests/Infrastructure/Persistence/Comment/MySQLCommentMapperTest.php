<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Comment;

use PHPUnit\Framework\TestCase;
use App\Infrastructure\Persistence\Gateway\MySQL;
use App\Domain\User\User;
use \Exception;

require_once realpath('./bootstrap/app.php');

class MySQLCommentMapperTest extends TestCase
{
    use \App\Infrastructure\Persistence\Gateway\Gateway;
    use \App\Infrastructure\Persistence\User\UserTestHelper;
    use \App\Infrastructure\Persistence\Category\CategoryTestHelper;
    use \App\Infrastructure\Persistence\Post\PostTestHelper;
    use CommentTestHelper;

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

    public function testShouldGetUser(): void
    {
        $user      = $this->givenAStoredUser();
        $category  = $this->givenAStoredCategory();
        $post      = $this->givenAStoredPost($category, $user);
        $comment   = $this->givenAStoredComment($user, $post);
        $mapper    = $this->givenACommentMapper();
        $belongsTo = $mapper->getUser($comment);

        $this->assertInstanceOf(User::class, $belongsTo);
        $this->assertEquals($user->getName(), $belongsTo->getName());
        $this->assertEquals($user->getSurname(), $belongsTo->getSurname());
        $this->assertEquals($user->getEmail(), $belongsTo->getEmail());
    }

    public function testShouldThrowExceptionIfTheUserWhoCommentedIsNotFound(): void
    {
        $user          = $this->givenAStoredUser();
        $user->setId(2);
        
        $category      = $this->givenAStoredCategory();
        $post          = $this->givenAStoredPost($category, $user);
        $comment       = $this->givenAStoredComment($user, $post);
        $commentMapper = $this->givenACommentMapper();
        
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(sprintf(
            'The user that belongs to the id %d comment was not found.', 
            $comment->getId()
        ));
        
        $commentMapper->getUser($comment);
    }
}
