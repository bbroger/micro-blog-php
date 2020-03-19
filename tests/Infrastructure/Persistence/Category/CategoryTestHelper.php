<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Category;

use App\Infrastructure\Persistence\Category\MySQLCategoryDAO;
use App\Domain\Category\Category;

trait CategoryTestHelper
{
    /**
     * @return Category
     */
    private function givenACategory(): Category
    {
        return new Category('Test', 'test');
    }

    /**
     * @return Category
     */
    private function givenAStoredCategory(): Category
    {
        $categoryDAO = $this->givenACategoryDAO();
        $category    = new Category('Test', 'test');
        $category->setId($categoryDAO->store($category));

        return $category;
    }

    /**
     * @return CategoryDAO
     */
    private function givenACategoryDAO(): MySQLCategoryDAO
    {
        return new MySQLCategoryDAO($this->gateway);
    }
}
