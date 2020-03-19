<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Category;

use App\Domain\Category\Category;

interface MySQLCategoryMapperInterface 
{
    /**
     * @param Category $category
     * @throws Exception
     */
    public function getCategory(Category $category): Category;
}
