<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Post;

use App\Domain\Post\Post;
use App\Infrastructure\Persistence\Gateway\GatewayInterface;
use \Exception;
use \PDOException;

class MySQLPostDAO implements PostDAOInterface 
{
    use \App\Infrastructure\Persistence\Gateway\Gateway;
    use \App\Domain\Entity\RowCount;

    const TABLE = 'post';

    public function __construct(GatewayInterface $gateway)
    {
        $this->setGateway($gateway);
    }

    /**
     * @param Post $post
     */
    public function store(Post $post): int
    {
        $statement = sprintf('
            INSERT INTO %s (
                title, 
                description, 
                content, 
                fk_author, 
                fk_category
            ) VALUES (
                :title, 
                :description, 
                :content, 
                :fk_author, 
                :fk_category
            )
        ', self::TABLE);

        try {
            $pdo      = $this->gateway->getConnection();
            $prepared = $pdo->prepare($statement);
            $prepared->bindValue(':title', $post->getTitle());
            $prepared->bindValue(':description', $post->getDescription());
            $prepared->bindValue(':content', $post->getContent());
            $prepared->bindValue(':fk_author', $post->getAuthor()->getId());
            $prepared->bindValue(':fk_category', $post->getCategory()->getId());
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
    public function findById(int $id): Post
    {
        $statement = sprintf('
            SELECT 
                id, 
                title, 
                description, 
                content, 
                fk_author, 
                fk_category 
            FROM %s 
            WHERE id = :id
        ', self::TABLE);

        try {
            $pdo      = $this->gateway->getConnection();
            $prepared = $pdo->prepare($statement);
            $prepared->bindValue(':id', $id);
            $prepared->execute();

            $object   = $prepared->fetchObject();

            if (!$object) {
                throw new Exception(sprintf('Post with id %d not found.', $id));
            }

            $post     = new Post($object->title, $object->description, $object->content);
            $post->setId((int) $object->id);

            return $post;
        } catch (PDOException $e) {
            echo $e->getMessage() . PHP_EOL;
            die;
        }
    }

    /**
     * @param Post $post
     */
    public function update(Post $post): int
    {
        $statement = sprintf('
            UPDATE 
                %s 
            SET 
                title = :title, 
                description = :description, 
                content = :content, 
                fk_author = :fk_author, 
                fk_category = :fk_category 
            WHERE id = :id
        ', self::TABLE);

        try {
            if (!$this->exists($post)) {
                throw new Exception(sprintf(
                    'Post with id %d not found.', 
                    $post->getId()
                ));
            }

            $pdo      = $this->gateway->getConnection();
            $prepared = $pdo->prepare($statement);
            $prepared->bindValue(':title', $post->getTitle());
            $prepared->bindValue(':description', $post->getDescription());
            $prepared->bindValue(':content', $post->getContent());
            $prepared->bindValue(':fk_author', $post->getAuthor()->getId());
            $prepared->bindValue(':fk_category', $post->getCategory()->getId());
            $prepared->bindValue(':id', $post->getId());
            $prepared->execute();

            return (int) $prepared->rowCount();
        } catch (PDOException $e) {
            echo $e->getMessage() . PHP_EOL;
            die;
        }
    }

    /**
     * @param Post $post
     */
    public function destroy(Post $post): int
    {
        $statement = sprintf('DELETE FROM %s WHERE id = :id', self::TABLE);

        try {
            if (!$this->exists($post)) {
                throw new Exception(sprintf(
                    'Post with id %d not found.', 
                    $post->getId()
                ));
            }

            $pdo      = $this->gateway->getConnection();
            $prepared = $pdo->prepare($statement);
            $prepared->bindValue(':id', $post->getId());
            $prepared->execute();

            return (int) $prepared->rowCount();
        } catch (PDOException $e) {
            echo $e->getMessage() . PHP_EOL;
            die;
        }
    }
}
