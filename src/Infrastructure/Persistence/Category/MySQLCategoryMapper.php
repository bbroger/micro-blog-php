<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Category;

use App\Domain\Category\Category;
use App\Infrastructure\Persistence\Gateway\GatewayInterface;
use \Exception;
use \PDOException;

class MySQLCategoryMapper implements MySQLCategoryMapperInterface 
{
    use \App\Infrastructure\Persistence\Gateway\Gateway;

    public function __construct(GatewayInterface $gateway)
    {
        $this->setGateway($gateway);
    }

    /**
     * @param Category $category
     * @throws Exception
     */
    public function getCategory(Category $category): Category
    {
        $statement = '
            SELECT 
                r.id, 
                r.title, 
                r.description, 
                r.fk_category 
            FROM 
                category r 
            INNER JOIN 
                category s 
            ON 
                s.fk_category = r.id
            WHERE 
                s.id = :id
        ';

        try {
            $pdo      = $this->gateway->getConnection();
            $prepared = $pdo->prepare($statement);
            $prepared->bindValue(':id', $category->getId());
            $prepared->execute();

            $object   = $prepared->fetchObject();

            if (!$object) {
                $message = 'This is a root category and does not belong to any other category.';
                throw new Exception($message);
            }

            $root     = new Category($object->title, $object->description);
            $root->setId((int) $object->id);

            return $root;
        } catch (PDOException $e) {
            echo $e->getMessage() . PHP_EOL;
            die;
        }
    }
}
