<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use App\Domain\User\User;

interface UserDAOInterface
{
    /**
     * @param User $user
     */
    public function store(User $user): int;

    /**
     * @param int $id
     * @throws Exception
     */
    public function findById(int $id): User;

    /**
     * @param User $user
     */
    public function update(User $user): int;

    /**
     * @param User $user
     */
    public function destroy(User $user): int;
}
