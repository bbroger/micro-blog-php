<?php
declare(strict_types=1);

namespace App\Domain\Entity;

use PHPUnit\Framework\TestCase;
use \ArrayIterator;
use \stdClass;
use \Exception;

class CollectionTest extends TestCase
{
    private Collection $collection;

    protected function setUp(): void
    {
        $this->collection = new class() extends Collection {
            /**
             * @param object $object
             */
            public function add(object $object): void
            {
                $this->append($object);
            }

            /**
             * @param string $name
             * @throws Exception
             */
            public function getObjectsByName(string $name): self
            {
                if (!$this->count()) {
                    throw new Exception('Empty collection.');
                }

                $filter   = fn($object) => $object->name === $name;
                $filtered = parent::filter($filter);

                if (!$filtered->count()) {
                    throw new Exception(sprintf('No objects with the name %s.', $name));
                }

                return $filtered;
            }

            public function convertCase(): self
            {
                $mapper = function ($obj) {
                    $obj->name = mb_convert_case($obj->name, MB_CASE_UPPER, 'UTF-8');
                    return $obj;
                };

                return $this->map($mapper);
            }

            public function getTotal(): float
            {
                $reducer = fn($accumulated, $obj) => $accumulated += $obj->price;
                return $this->reduce($reducer, 0);
            }
        };
    }

    public function testShouldAddItem(): void
    {
        $object = new stdClass();
        
        $this->assertEquals(0, $this->collection->count());

        $this->collection->add($object);

        $this->assertEquals(1, $this->collection->count());
    }

    public function testShouldAddItemsFromArray(): void
    {
        $items = [];

        for ($i = 1; $i <= 5; $i++) {
            array_push($items, new stdClass());
        }

        $this->assertEquals(0, $this->collection->count());

        $this->collection->fromArray($items);

        $this->assertEquals(5, $this->collection->count());
    }

    public function testShouldGetAggregates(): void
    {
        $this->assertEquals([], $this->collection->getAggregates());
    }

    public function testShouldGetIterator(): void
    {
        $this->assertInstanceOf(
            ArrayIterator::class, 
            $this->collection->getAggregatesIterator()
        );
    }

    public function testShouldGetObjectsByName(): void 
    {
        $object1 = new stdClass();
        $object1->name = 'Rafael';

        $object2 = clone $object1;
        
        $object3 = clone $object1;
        $object3->name = 'Felipe';

        $this->assertEquals(0, $this->collection->count());

        $this->collection->add($object1);
        $this->collection->add($object2);
        $this->collection->add($object3);

        $result  = $this->collection->getObjectsByName('Rafael');
        $objects = $result->getAggregates();

        $this->assertEquals(2, $result->count());
        $this->assertEquals($object1, array_shift($objects));
        $this->assertEquals($object2, array_shift($objects));
    }

    public function testShouldConvertCase(): void
    {
        $object = new stdClass();
        $object->name = 'Rafael';

        $this->collection->add($object);

        $result = $this->collection->convertCase()->getAggregates();

        foreach ($result as $obj) {
            $this->assertEquals(strtoupper($obj->name), $obj->name);
        }
    }

    public function testShouldCalculateTotalPrice(): void
    {
        $object1 = new stdClass();
        $object1->price = 150.0;

        $object2 = clone $object1;
        $object2->price = 100.0;

        $object3 = clone $object1;
        $object3->price = 220.0;

        $this->collection->fromArray([$object1, $object2, $object3]);

        $this->assertEquals(470.0, $this->collection->getTotal());
    }
}
