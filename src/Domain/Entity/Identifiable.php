<?php
declare(strict_types=1);

namespace App\Domain\Entity;

interface Identifiable
{
    /**
     * @param int $id
     */
    public function setId(int $id): void;

    /**
     * @return int
     */
    public function getId(): int;
}
