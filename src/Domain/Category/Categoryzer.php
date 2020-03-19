<?php
declare(strict_types=1);

namespace App\Domain\Category;

trait Categoryzer
{
    private Category $category;

    /**
     * @param Category $category
     */
    public function setCategory(Category $category): void
    {
        $this->category = $category;
    }

    /**
     * @return Category
     */
    public function getCategory(): ?Category
    {
        return !empty($this->category) ? $this->category : null;
    }
}
