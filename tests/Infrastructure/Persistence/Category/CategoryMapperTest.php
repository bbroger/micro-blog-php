<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Category;

use PHPUnit\Framework\TestCase;
use App\Infrastructure\Persistence\Gateway\GatewayInterface;
use App\Infrastructure\Persistence\Gateway\MySQL;
use App\Domain\Category\Category;
use \Exception;

require_once realpath('./bootstrap/app.php');

class CategoryMapperTest extends TestCase
{
    private GatewayInterface $gateway;

    protected function setUp(): void
    {
        $this->gateway = new MySQL();
    }

    protected function tearDown(): void
    {
        $pdo     = $this->gateway->getConnection();
        $prepare = $pdo->prepare('TRUNCATE category');
        $prepare->execute();
    }

    public function testShouldGetSubCategory(): void
    {
        $parent = $this->givenAStoredCategory('Root');
        $sub    = $this->givenAStoredCategory('Sub', $parent);
        
        $mapper = $this->givenACategoryMapper();
        $hasOne = $mapper->getCategory($sub);

        $this->assertInstanceOf(Category::class, $hasOne);
        $this->assertEquals($parent->getTitle(), $hasOne->getTitle());
    }

    public function testShouldThrowExceptionIfNotExistsSubCategory(): void
    {
        $category = $this->givenAStoredCategory('Root');
        
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(
            'This is a root category and does not belong to any other category.'
        );
        
        $this->givenACategoryMapper()->getCategory($category);
    }

    /**
     * @param string $title
     * @param Category $parent
     */
    private function givenACategory(string $title, Category $parent = null): Category
    {
        $category = new Category($title, 'test');

        if ($parent) {
            $category->setCategory($parent);
        }

        return $category;
    }

    /**
     * @return Category
     */
    private function givenAStoredCategory(string $title, Category $parent = null): Category 
    {
        $categoryDAO = $this->givenACategoryDAO();
        $category    = $this->givenACategory($title, $parent);
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

    /**
     * @return CategoryMapper
     */
    private function givenACategoryMapper(): MySQLCategoryMapper
    {
        return new MySQLCategoryMapper($this->gateway);
    }
}
