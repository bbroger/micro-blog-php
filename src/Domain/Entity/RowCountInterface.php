<?php
declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Entity\Identifiable;

interface RowCountInterface
{
    /**
     * @param Identifiable $entity
     */
    public function exists(Identifiable $entity): bool;
}
