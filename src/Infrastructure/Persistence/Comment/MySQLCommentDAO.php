<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Comment;

use App\Domain\Category\Category;
use App\Infrastructure\Persistence\Gateway\GatewayInterface;
use App\Domain\Comment\Comment;
use \Exception;
use \PDOException;

class MySQLCommentDAO implements CommentDAOInterface 
{
    use \App\Infrastructure\Persistence\Gateway\Gateway;
    use \App\Domain\Entity\RowCount;

    const TABLE = 'comment';

    public function __construct(GatewayInterface $gateway)
    {
        $this->setGateway($gateway);
    }

    /**
     * @param Comment $comment
     */
    public function store(Comment $comment): int
    {
        $statement = sprintf('
            INSERT INTO %s (
                comment, 
                fk_user, 
                fk_post
            ) VALUES (
                :comment, 
                :fk_user, 
                :fk_post
            )
        ', self::TABLE);

        try {
            $pdo      = $this->gateway->getConnection();
            $prepared = $pdo->prepare($statement);
            $prepared->bindValue(':comment', $comment->getComment());
            $prepared->bindValue(':fk_user', $comment->getUser()->getId());
            $prepared->bindValue(':fk_post', $comment->getPost()->getId());
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
    public function findById(int $id): Comment
    {
        $statement = sprintf(
            'SELECT id, comment, fk_user, fk_post FROM %s WHERE id = :id', 
            self::TABLE
        );

        try {
            $pdo      = $this->gateway->getConnection();
            $prepared = $pdo->prepare($statement);
            $prepared->bindValue(':id', $id);
            $prepared->execute();

            $object   = $prepared->fetchObject();

            if (!$object) {
                throw new Exception(sprintf('Comment with id %d not found.', $id));
            }

            $comment  = new Comment($object->comment);
            $comment->setId((int) $object->id);
            
            return $comment;
        } catch (PDOException $e) {
            echo $e->getMessage() . PHP_EOL;
            die;
        }
    }

    /**
     * @param Comment $comment
     */
    public function update(Comment $comment): int
    {
        $statement = sprintf('
            UPDATE 
                %s 
            SET 
                comment = :comment, 
                fk_user = :fk_user, 
                fk_post = :fk_post
            WHERE id = :id
        ', self::TABLE);

        try {
            if (!$this->exists($comment)) {
                throw new Exception(sprintf(
                    'Comment with id %d not found.', 
                    $comment->getId()
                ));
            }

            $pdo      = $this->gateway->getConnection();
            $prepared = $pdo->prepare($statement);
            $prepared->bindValue(':comment', $comment->getComment());
            $prepared->bindValue(':fk_user', $comment->getUser()->getId());
            $prepared->bindValue(':fk_post', $comment->getPost()->getId());
            $prepared->bindValue(':id', $comment->getId());
            $prepared->execute();

            return (int) $prepared->rowCount();
        } catch (PDOException $e) {
            echo $e->getMessage() . PHP_EOL;
            die;
        }
    }

    /**
     * @param Comment $comment
     */
    public function destroy(Comment $comment): int
    {
        $statement = sprintf('DELETE FROM %s WHERE id = :id', self::TABLE);

        try {
            if (!$this->exists($comment)) {
                throw new Exception(sprintf(
                    'Comment with id %d not found.', 
                    $comment->getId()
                ));
            }

            $pdo      = $this->gateway->getConnection();
            $prepared = $pdo->prepare($statement);
            $prepared->bindValue(':id', $comment->getId());
            $prepared->execute();

            return (int) $prepared->rowCount();
        } catch (PDOException $e) {
            echo $e->getMessage() . PHP_EOL;
            die;
        }
    }
}
