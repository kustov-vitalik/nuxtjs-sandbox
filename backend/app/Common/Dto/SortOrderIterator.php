<?php

namespace App\Common\Dto;

class SortOrderIterator implements \Iterator
{
    private array $sortOrders;

    public function __construct(SortOrder...$items)
    {
        $this->sortOrders = $items;
    }

    /**
     * Return the current element
     * @link https://php.net/manual/en/iterator.current.php
     * @return SortOrder
     * @since 5.0.0
     */
    public function current(): SortOrder
    {
        return current($this->sortOrders);
    }

    /**
     * Move forward to next element
     * @link https://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next(): void
    {
        next($this->sortOrders);
    }

    /**
     * Return the key of the current element
     * @link https://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key(): mixed
    {
        return key($this->sortOrders);
    }

    /**
     * Checks if current position is valid
     * @link https://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid(): bool
    {
        $k = $this->key();
        return $k !== null && $k !== false;
    }

    /**
     * Rewind the Iterator to the first element
     * @link https://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind(): void
    {
        reset($this->sortOrders);
    }
}
