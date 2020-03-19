<?php
declare(strict_types=1);

namespace App\Domain\Category;

interface Categorizable
{
    /**
     * @param Category $category
     */
    public function setCategory(Category $category): void;

    /**
     * @return Category
     */
    public function getCategory(): ?Category;
}
