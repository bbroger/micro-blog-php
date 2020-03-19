<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Category;

use App\Domain\Category\Category;

interface CategoryDAOInterface
{
    /**
     * @param Category $category
     */
    public function store(Category $category): int;

    /**
     * @param int $id
     * @throws Exception
     */
    public function findById(int $id): Category;

    /**
     * @param Category $category
     * @throws Exception
     */
    public function update(Category $category): int;

    /**
     * @param Category $category
     */
    public function destroy(Category $category): int;
}
