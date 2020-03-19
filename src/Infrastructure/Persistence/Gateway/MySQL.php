<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Gateway;

use \PDO;
use \PDOException;
use \Exception;

class MySQL implements GatewayInterface 
{
    /**
     * @return PDO
     */
    public function getConnection(): PDO
    {
        try {
            $charset = [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'];
            $pdo     = new PDO(
                $this->getDsn(), 
                getenv('USER'), 
                getenv('PASS'), 
                $charset
            );
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $pdo;
        } catch (PDOException $e) {
            echo $e->getMessage() . PHP_EOL;
            die;
        }
    }

    /**
     * @return string
     */
    private function getDsn(): string
    {
        return sprintf('mysql:host=%s;dbname=%s', getenv('HOST'), getenv('DBNAME'));
    }
}
