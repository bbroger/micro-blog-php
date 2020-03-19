<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Gateway;

use \PDO;

trait Gateway 
{
    private GatewayInterface $gateway;

    /**
     * @param 
     */
    public function setGateway(GatewayInterface $gateway): void
    {
        $this->gateway = $gateway;
    }
}
