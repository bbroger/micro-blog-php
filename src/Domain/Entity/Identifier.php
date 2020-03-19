<?php
declare(strict_types=1);

namespace App\Domain\Entity;

use \InvalidArgumentException;

trait Identifier
{
    private int $id;

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        if ($id <= 0) {
            throw new InvalidArgumentException(
                'Id must have a positive number greater than zero.'
            );
        }

        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
}
