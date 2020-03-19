<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use PHPUnit\Framework\TestCase;

use App\Infrastructure\Persistence\Gateway\GatewayInterface;
use App\Infrastructure\Persistence\Gateway\MySQL;
use App\Domain\User\User;
use \Exception;

class MySQLUserDAOTest extends TestCase
{
    use UserTestHelper;
    
    private GatewayInterface $gateway;

    protected function setUp(): void
    {
        $this->gateway = new MySQL();
    }

    protected function tearDown(): void
    {
        $pdo     = $this->gateway->getConnection();
        $prepare = $pdo->prepare('TRUNCATE user');
        $prepare->execute();
    }

    public function testShouldStoreUser(): void
    {
        $user    = $this->givenAUser();
        $userDAO = $this->givenAUserDAO();

        $this->assertEquals(1, $userDAO->store($user));
    }

    public function testShouldFindUserById(): void
    {
        $user    = $user = $this->givenAStoredUser();
        $userDAO = $this->givenAUserDAO();
        $object  = $userDAO->findById($user->getId());

        $this->assertInstanceOf(User::class, $object);
        $this->assertEquals($user->getName(), $object->getName());
        $this->assertEquals($user->getSurname(), $object->getSurname());
        $this->assertEquals($user->getEmail(), $object->getEmail());
    }

    public function testShouldThrowExceptionIfUserNotFound(): void
    {
        $id  = 1;

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(sprintf('User with id %d not found.', $id));
        
        $this->givenAUserDAO()->findById($id);
    }

    public function testShouldUpdateUser(): void
    {
        $userDAO = $this->givenAUserDAO();
        $user    = $this->givenAStoredUser();
        $user->setEmail('rafael-webdesign@live.com');

        $this->assertEquals(1, $userDAO->update($user));

        $object  = $userDAO->findById($user->getId());

        $this->assertEquals($user->getEmail(), $object->getEmail());
    }

    public function testShouldThrowAnExceptionWhenTryingToUpdateUserThatDoesNotExist(): void
    {
        $user = $this->givenAStoredUser();
        $user->setId(2);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(sprintf(
            'User with id %d not found.', 
            $user->getId()
        ));

        $this->givenAUserDAO()->update($user);
    }

    public function testShouldDestroyUser(): void
    {
        $user = $this->givenAStoredUser();
        
        $this->assertEquals(1, $this->givenAUserDAO()->destroy($user));
    }

    public function testShouldThrowAnExceptionWhenTryingToDestroyACategoryThatDoesNotExist(): void
    {
        $user = $this->givenAStoredUser();
        $user->setId(2);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(sprintf(
            'User with id %d not found.', 
            $user->getId()
        ));

        $this->givenAUserDAO()->destroy($user);
    }
}
