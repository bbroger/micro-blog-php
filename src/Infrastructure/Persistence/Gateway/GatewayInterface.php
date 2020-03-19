<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Gateway;

use \PDO;

interface GatewayInterface
{
    public function getConnection(): PDO;
}
