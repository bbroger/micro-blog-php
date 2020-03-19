<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Comment;

use App\Domain\Comment\Comment;
use App\Domain\User\User;
use App\Infrastructure\Persistence\Gateway\GatewayInterface;
use \Exception;
use \PDOException;

class MySQLCommentMapper implements MySQLCommentMapperInterface 
{
    use \App\Infrastructure\Persistence\Gateway\Gateway;

    public function __construct(GatewayInterface $gateway)
    {
        $this->setGateway($gateway);
    }

    /**
     * @param Comment $comment
     * @throws Exception
     */
    public function getUser(Comment $comment): User
    {
        $statement = '
            SELECT 
                u.id, 
                u.name, 
                u.surname, 
                u.email 
            FROM 
                user u 
            INNER JOIN 
                comment c 
            ON 
                c.fk_user = u.id 
            WHERE 
                c.id = :id
        ';

        try {
            $pdo      = $this->gateway->getConnection();
            $prepared = $pdo->prepare($statement);
            $prepared->bindValue(':id', $comment->getId());
            $prepared->execute();

            $object   = $prepared->fetchObject();
            
            if (!$object) {
                throw new Exception(sprintf(
                    'The user that belongs to the id %d comment was not found.', 
                    $comment->getId()
                ));
            }

            $user    = new User($object->name, $object->surname, $object->email);
            $user->setId((int) $object->id);

            return $user;
        } catch (PDOException $e) {
            echo $e->getMessage() . PHP_EOL;
            die;
        }
    }
}
