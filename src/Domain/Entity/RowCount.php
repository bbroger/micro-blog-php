<?php
declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Entity\Identifiable;
use \PDOException;

trait RowCount
{
    /**
     * @param Identifiable $entity
     */
    public function exists(Identifiable $entity): bool
    {
        $statement = sprintf(
            'SELECT COUNT(id) as count FROM %s WHERE id = :id', 
            self::TABLE
        );

        try {
            $pdo      = $this->gateway->getConnection();
            $prepared = $pdo->prepare($statement);
            $prepared->bindValue(':id', $entity->getId());
            $prepared->execute();
            $object   = $prepared->fetchObject();

            return (bool) $object->count;
        } catch (PDOException $e) {
            echo $e->getMessage() . PHP_EOL;
            die;
        }
    }
}
