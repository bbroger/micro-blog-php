<?php
declare(strict_types=1);

namespace App\Infrastructure\Persistence\Category;

use PHPUnit\Framework\TestCase;
use App\Infrastructure\Persistence\Gateway\GatewayInterface;
use App\Infrastructure\Persistence\Gateway\MySQL;
use App\Domain\Category\Category;
use \Exception;

require_once realpath('./bootstrap/app.php');

class MySQLCategoryDAOTest extends TestCase
{
    use CategoryTestHelper;

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

    public function testShouldStoreCategory(): void
    {
        $category    = $this->givenACategory();
        $categoryDAO = $this->givenACategoryDAO();

        $this->assertEquals(1, $categoryDAO->store($category));
    }

    public function testShouldFindCategoryById(): void
    {
        $category    = $this->givenAStoredCategory();
        $categoryDAO = $this->givenACategoryDAO();
        $object      = $categoryDAO->findById($category->getId());

        $this->assertInstanceOf(Category::class, $object);
        $this->assertEquals($category->getTitle(), $object->getTitle());
        $this->assertEquals($category->getDescription(), $object->getDescription());
    }

    public function testThrowExceptionIfCategoryNotFound(): void
    {
        $id  = 1;

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(sprintf('Category with id %d not found.', $id));

        $this->givenACategoryDAO()->findById($id);
    }

    public function testShouldUpdateCategory(): void
    {
        $category    = $this->givenAStoredCategory();
        $category->setDescription('New description');
        $categoryDAO = $this->givenACategoryDAO();

        $this->assertEquals(1, $categoryDAO->update($category));

        $object      = $categoryDAO->findById($category->getId());

        $this->assertEquals($category->getDescription(), $object->getDescription());
    }

    public function testShouldThrowAnExceptionWhenTryingToUpdateACategoryThatDoesNotExist(): void
    {
        $id          = 2;
        $category    = $this->givenACategory();
        $category->setId($id);
        $category->setDescription('New description');
        $categoryDAO = $this->givenACategoryDAO();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(sprintf('Category with id %d not found.', $id));

        $categoryDAO->update($category);
    }

    public function testShouldDestroyCategory(): void
    {
        $category    = $this->givenAStoredCategory();
        $categoryDAO = $this->givenACategoryDAO();

        $this->assertEquals(1, $categoryDAO->destroy($category));
    }

    public function testShouldThrowAnExceptionWhenTryingToDestroyACategoryThatDoesNotExist(): void
    {
        $id          = 2;
        $category    = $this->givenACategory();
        $category->setId($id);
        $categoryDAO = $this->givenACategoryDAO();

        $this->expectException(Exception::class);
        $this->expectExceptionMessage(sprintf('Category with id %d not found.', $id));

        $categoryDAO->destroy($category);
    }
}
