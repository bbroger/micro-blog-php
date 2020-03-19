<?php
declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\Entity\Identifiable;
use App\Domain\Entity\Identifier as Identifier;
use \InvalidArgumentException;

class User implements Identifiable
{
    use Identifier;

    private string $name;
    
    private string $surname;
    
    private string $email;

    public function __construct(string $name, string $surname, string $email)
    {
        $this->setName($name);
        $this->setSurname($surname);
        $this->setEmail($email);
    }

    /**
     * @return string
     */
    public function getName(): string 
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSurname(): string 
    {
        return $this->surname;
    }

    /**
     * @return string
     */
    public function getEmail(): string 
    {
        return $this->email;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        if (empty($name)) {
            throw new InvalidArgumentException('Name must be filled.');
        }

        $this->name = $name;
    }

    /**
     * @param string $name
     */
    public function setSurname(string $surname): void
    {
        if (empty($surname)) {
            throw new InvalidArgumentException('Surname must be filled.');
        }

        $this->surname = $surname;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        if (empty($email)) {
            throw new InvalidArgumentException('E-mail must be filled.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid e-mail.');
        }

        $this->email = $email;
    }
}
