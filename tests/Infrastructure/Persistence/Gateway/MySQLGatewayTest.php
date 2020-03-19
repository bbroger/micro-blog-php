<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Gateway;

use PHPUnit\Framework\TestCase;
use \Exception;
use \PDO;

require_once realpath('./bootstrap/app.php');

class MySQLGatewayTest extends TestCase
{
    private GatewayInterface $gateway;

    protected function setUp(): void
    {
        $this->gateway = new MySQL();
    }

    public function testShouldGetConnection(): void
    {
        $this->assertInstanceOf(PDO::class, $this->gateway->getConnection());
    }
}
