<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Category;

use App\Domain\Category\Category;
use App\Infrastructure\Persistence\Gateway\GatewayInterface;
use \Exception;
use \PDOException;

class MySQLCategoryDAO implements CategoryDAOInterface 
{
    use \App\Infrastructure\Persistence\Gateway\Gateway;
    use \App\Domain\Entity\RowCount;

    const TABLE = 'category';

    public function __construct(GatewayInterface $gateway)
    {
        $this->setGateway($gateway);
    }

    /**
     * @param Category $category
     */
    public function store(Category $category): int
    {
        $statement = sprintf('
            INSERT INTO %s (
                title, 
                description, 
                fk_category
            ) VALUES (
                :title, 
                :description, 
                :fk_category
            )
        ', self::TABLE);

        try {
            $pdo      = $this->gateway->getConnection();
            $prepared = $pdo->prepare($statement);
            $parent   = $category->getCategory();
            $prepared->bindValue(':title', $category->getTitle());
            $prepared->bindValue(':description', $category->getDescription());
            $prepared->bindValue(':fk_category', !empty($parent) ? $parent->getId() : null);
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
    public function findById(int $id): Category
    {
        $statement = sprintf('
            SELECT 
                id, 
                title, 
                description, 
                fk_category 
            FROM 
                %s 
            WHERE 
                id = :id
        ', self::TABLE);

        try {
            $pdo      = $this->gateway->getConnection();
            $prepared = $pdo->prepare($statement);
            $prepared->bindValue(':id', $id);
            $prepared->execute();

            $object   = $prepared->fetchObject();

            if (!$object) {
                throw new Exception(sprintf('Category with id %d not found.', $id));
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
     * @param Category $category
     * @throws Exception
     */
    public function update(Category $category): int
    {
        $statement = sprintf('
            UPDATE 
                %s 
            SET 
                title = :title, 
                description = :description, 
                fk_category = :fk_category
            WHERE id = :id
        ', self::TABLE);

        try {
            if (!$this->exists($category)) {
                throw new Exception(sprintf(
                    'Category with id %d not found.', 
                    $category->getId()
                ));
            }
            
            $pdo      = $this->gateway->getConnection();
            $prepared = $pdo->prepare($statement);
            $parent   = $category->getCategory();
            $prepared->bindValue(':title', $category->getTitle());
            $prepared->bindValue(':description', $category->getDescription());
            $prepared->bindValue(':fk_category', !empty($parent) ? $parent->getId() : null);
            $prepared->bindValue(':id', $category->getId());
            $prepared->execute();

            return (int) $prepared->rowCount();
        } catch (PDOException $e) {
            echo $e->getMessage() . PHP_EOL;
            die;
        }
    }

    /**
     * @param Category $category
     */
    public function destroy(Category $category): int
    {
        $statement = sprintf('DELETE FROM %s WHERE id = :id', self::TABLE);

        try {
            if (!$this->exists($category)) {
                throw new Exception(sprintf(
                    'Category with id %d not found.', 
                    $category->getId()
                ));
            }

            $pdo      = $this->gateway->getConnection();
            $prepared = $pdo->prepare($statement);
            $prepared->bindValue(':id', $category->getId());
            $prepared->execute();

            return (int) $prepared->rowCount();
        } catch (PDOException $e) {
            echo $e->getMessage() . PHP_EOL;
            die;
        }
    }
}
