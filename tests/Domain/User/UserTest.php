<?php
declare(strict_types=1);

namespace App\Domain\User;

use PHPUnit\Framework\TestCase;
use \InvalidArgumentException;

class UserTest extends TestCase
{
    public function testShouldCreateUser(): void 
    {
        $name    = 'Rafael';
        $surname = 'Felipe';
        $email   = 'manofirmz@gmail.com';
        $user    = new User($name, $surname, $email);

        $this->assertEquals($name, $user->getName());
        $this->assertEquals($surname, $user->getSurname());
        $this->assertEquals($email, $user->getEmail());
    }

    public function testShouldThrowsExceptionIfEmptyName(): void 
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Name must be filled.');

        $name    = '';
        $surname = 'Felipe';
        $email   = 'manofirmz@gmail.com';
        $user    = new User($name, $surname, $email);
    }

    public function testShouldThrowsExceptionIfEmptySurname(): void 
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Surname must be filled.');

        $name    = 'Rafael';
        $surname = '';
        $email   = 'manofirmz@gmail.com';
        $user    = new User($name, $surname, $email);
    }

    public function testShouldThrowsExceptionIfEmptyEmail(): void 
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('E-mail must be filled.');

        $name    = 'Rafael';
        $surname = 'Felipe';
        $email   = '';
        $user    = new User($name, $surname, $email);
    }

    public function testShouldThrowsExceptionIfInvalidEmail(): void 
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid e-mail.');

        $name    = 'Rafael';
        $surname = 'Felipe';
        $email   = 'manofirmz@gmail';
        $user    = new User($name, $surname, $email);
    }
}
