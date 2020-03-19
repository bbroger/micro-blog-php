<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Post;

use App\Infrastructure\Persistence\Comment\MySQLCommentMapper;
use App\Infrastructure\Persistence\Comment\MySQLCommentDAO;
use App\Domain\Comment\CommentCollection;
use App\Domain\Comment\Comment;
use App\Domain\Category\Category;
use App\Domain\Post\Post;
use App\Domain\User\User;
use App\Infrastructure\Persistence\Gateway\GatewayInterface;
use \PDO;
use \Exception;
use \PDOException;

class MySQLPostMapper implements MySQLPostMapperInterface 
{
    use \App\Infrastructure\Persistence\Gateway\Gateway;

    public function __construct(GatewayInterface $gateway)
    {
        $this->setGateway($gateway);
    }

    /**
     * @param Post $post
     * @param MySQLPostDAO $postDAO
     * @param MySQLCommentDAO $commentDAO
     */
    public function store(
        Post $post, 
        MySQLPostDAO $postDAO, 
        MySQLCommentDAO $commentDAO
    ): int
    {
        $post->setId($postDAO->store($post));

        foreach ($post->getComments()->getAggregates() as $comment) {
            $comment->setPost($post);
            $commentDAO->store($comment);
        }

        return $post->getId();
    }

    /**
     * @param Post $post
     * @throws Exception
     */
    public function getCategory(Post $post): Category
    {
        $statement = '
            SELECT 
                c.id, 
                c.title, 
                c.description, 
                c.fk_category
            FROM 
                category c 
            INNER JOIN 
                post p 
            ON 
                p.fk_category = c.id 
            WHERE 
                p.id = :id
        ';

        try {
            $pdo      = $this->gateway->getConnection();
            $prepared = $pdo->prepare($statement);
            $prepared->bindValue(':id', $post->getId());
            $prepared->execute();

            $object   = $prepared->fetchObject();

            if (!$object) {
                throw new Exception(sprintf(
                    'The category that belongs to the id %d post was not found.', 
                    $post->getId()
                ));
            }

            $category = new Category($object->title, $object->description);
            $category->setId((int) $object->id);

            return $category;
        } catch (PDOException $e) {
            echo $e->getMessage() . PHP_EOL;
            die;
        }
    }

    /**
     * @param Post $post
     * @param UserDAO $userDAO
     */
    public function getAuthor(Post $post): User
    {
        $statement = '
            SELECT 
                a.id, 
                a.name, 
                a.surname, 
                a.email 
            FROM 
                user a 
            INNER JOIN 
                post p 
            ON 
                p.fk_author = a.id 
            WHERE 
                p.id = :id
        ';

        try {
            $pdo      = $this->gateway->getConnection();
            $prepared = $pdo->prepare($statement);
            $prepared->bindValue(':id', $post->getId());
            $prepared->execute();

            $object   = $prepared->fetchObject();

            if (!$object) {
                throw new Exception(sprintf(
                    'The author of post with id %d not found.', 
                    $post->getId()
                ));
            }

            $author   = new User($object->name, $object->surname, $object->email);
            $author->setId((int) $object->id);

            return $author;
        } catch (PDOException $e) {
            echo $e->getMessage() . PHP_EOL;
            die;
        }
    }

    /**
     * @param Post $post
     * @throws Exception
     * @return CommentCollection
     */
    public function getComments(Post $post, MySQLCommentMapper $mapper): CommentCollection
    {
        $statement = '
            SELECT 
                c.id, 
                c.comment, 
                c.fk_post, 
                c.fk_user
            FROM 
                comment c 
            INNER JOIN 
                post p 
            ON 
                c.fk_post = p.id 
            WHERE 
                p.id = :id
        ';

        try {
            $pdo      = $this->gateway->getConnection();
            $prepared = $pdo->prepare($statement);
            $prepared->bindValue(':id', $post->getId());
            $prepared->execute();

            $objects  = $prepared->fetchAll(PDO::FETCH_OBJ);

            if (!count($objects)) {
                throw new Exception(sprintf(
                    'The post with id %d has no comments.', 
                    $post->getId()
                ));
            }

            $comments = new CommentCollection();
            $mapped   = array_map(function ($comment) use ($mapper) {
                $object = new Comment($comment->comment);
                $object->setId((int) $comment->id);
                $object->setUser($mapper->getUser($object));
                
                return $object;
            }, $objects);
            $comments->fromArray($mapped);

            return $comments;
        } catch (PDOException $e) {
            echo $e->getMessage() . PHP_EOL;
            die;
        }
    }
}
