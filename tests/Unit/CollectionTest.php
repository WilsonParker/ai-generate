<?php

namespace Tests\Unit;

use Illuminate\Support\Collection;
use PHPUnit\Framework\TestCase;

class CollectionTest extends TestCase
{
    public function test_push_collection_index(): void
    {

        Collection::macro('insert', function ($index, $value) {
            $this->splice($index, 0, $value);
            return $this;
        });

        $originalArray = [1, 2, 3, 4, 5];

        // The position where you want to insert the new item (index starts from 0)
        $insertPosition = 2;

        // The item you want to insert
        $newItem = 'inserted';

        $collection = collect($originalArray);
        $collection->insert($insertPosition, $newItem);
        dump($collection);
    }
}
