<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use App\Domain\User\User;
use App\Infrastructure\Persistence\Gateway\GatewayInterface;
use \Exception;
use \PDOException;

class MySQLUserDAO implements UserDAOInterface 
{
    use \App\Infrastructure\Persistence\Gateway\Gateway;
    use \App\Domain\Entity\RowCount;

    const TABLE = 'user';

    public function __construct(GatewayInterface $gateway)
    {
        $this->setGateway($gateway);
    }

    /**
     * @param User $user
     */
    public function store(User $user): int
    {
        $statement = sprintf('
            INSERT INTO %s (
                name, 
                surname, 
                email
            ) VALUES (
                :name, 
                :surname, 
                :email
            )           
        ', self::TABLE);

        try {
            $pdo      = $this->gateway->getConnection();
            $prepared = $pdo->prepare($statement);
            $prepared->bindValue(':name', $user->getName());
            $prepared->bindValue(':surname', $user->getSurname());
            $prepared->bindValue(':email', $user->getEmail());
            $prepared->execute();

            return (int) $pdo->lastInsertId();
        } catch (PDOException $e) {
            echo $e->getMessage() . PHP_EOL;
            die;
        }
    }

    /**
     * @param int $id
     * @throws Exception
     */
    public function findById(int $id): User
    {
        $statement = sprintf(
            'SELECT id, name, surname, email FROM %s WHERE id = :id', 
            self::TABLE
        );

        try {
            $pdo      = $this->gateway->getConnection();
            $prepared = $pdo->prepare($statement);
            $prepared->bindValue(':id', $id);
            $prepared->execute();

            $object   = $prepared->fetchObject();

            if (!$object) {
                throw new Exception(sprintf('User with id %d not found.', $id));
            }

            $user     = new User($object->name, $object->surname, $object->email);
            $user->setId((int) $object->id);
            
            return $user;
        } catch (PDOException $e) {
            echo $e->getMessage() . PHP_EOL;
            die;
        }
    }

    /**
     * @param User $user
     */
    public function update(User $user): int
    {
        $statement = sprintf('
            UPDATE %s 
            SET
                name = :name, 
                surname = :surname, 
                email = :email
            WHERE id = :id;
        ', self::TABLE);

        try {
            if (!$this->exists($user)) {
                throw new Exception(sprintf(
                    'User with id %d not found.', 
                    $user->getId()
                ));
            }

            $pdo      = $this->gateway->getConnection();
            $prepared = $pdo->prepare($statement);
            $prepared->bindValue(':name', $user->getName());
            $prepared->bindValue(':surname', $user->getSurname());
            $prepared->bindValue(':email', $user->getEmail());
            $prepared->bindValue(':id', $user->getId());
            $prepared->execute();

            return (int) $prepared->rowCount();
        } catch (PDOException $e) {
            echo $e->getMessage() . PHP_EOL;
            die;
        }
    }

    /**
     * @param User $user
     */
    public function destroy(User $user): int
    {
        $statement = sprintf('DELETE FROM %s WHERE id = :id', self::TABLE);

        try {
            if (!$this->exists($user)) {
                throw new Exception(sprintf(
                    'User with id %d not found.', 
                    $user->getId()
                ));
            }

            $pdo      = $this->gateway->getConnection();
            $prepared = $pdo->prepare($statement);
            $prepared->bindValue(':id', $user->getId());
            $prepared->execute();

            return (int) $prepared->rowCount();
        } catch (PDOException $e) {
            echo $e->getMessage() . PHP_EOL;
            die;
        }
    }
}
