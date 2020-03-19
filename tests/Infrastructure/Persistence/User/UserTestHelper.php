<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use App\Infrastructure\Persistence\User\MySQLUserDAO;
use App\Domain\User\User;

trait UserTestHelper
{
    /**
     * @return User
     */
    private function givenAUser(): User
    {
        return new User('Rafael', 'Felipe', 'manofirmz@gmail.com');
    }

    /**
     * @return User
     */
    private function givenAStoredUser(): User
    {
        $userDAO = $this->givenAUserDAO();
        $user    = $this->givenAUser();
        $user->setId($userDAO->store($user));

        return $user;
    }

    /**
     * @return UserDAO
     */
    private function givenAUserDAO(): MySQLUserDAO
    {
        return new MySQLUserDAO($this->gateway);
    }
}
