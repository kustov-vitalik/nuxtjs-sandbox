<?php

declare(strict_types=1);


namespace App\Common\Dto;

use IteratorAggregate;

class Sort implements IteratorAggregate, \JsonSerializable
{
    /** @var SortOrder[] */
    private array $orders;

    public function __construct(SortOrder...$orders)
    {
        $this->orders = $orders;
    }

    public static function fromString(string $sort = null): Sort
    {
        $items = [];

        if ($sort) {
            foreach (explode(':', $sort) as $sortOrder) {
                $items[] = SortOrder::fromString($sortOrder);
            }
        }

        return new Sort(...$items);
    }

    /**
     * Retrieve an external iterator
     * @link https://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return SortOrderIterator An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator(): SortOrderIterator
    {
        return new SortOrderIterator(...$this->orders);
    }

    public function jsonSerialize(): array
    {
        return [
            'orders' => $this->orders,
        ];
    }
}
