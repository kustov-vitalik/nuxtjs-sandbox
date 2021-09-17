<?php

namespace App\Common\Dto;


use ArrayAccess;
use Iterator;
use IteratorAggregate;
use Traversable;

class PageableResult implements IteratorAggregate, ArrayAccess, \JsonSerializable
{
    private array $results;
    private int $resultCount;
    private Pageable $pageable;

    /**
     * PageableResult constructor.
     * @param array $results
     * @param int $resultCount
     * @param Pageable $pageable
     */
    private function __construct(array $results, int $resultCount, Pageable $pageable)
    {
        $this->results = $results;
        $this->resultCount = $resultCount;
        $this->pageable = $pageable;
    }

    /**
     * @param array $results
     * @param int $resultsCount
     * @param Pageable $pageable
     * @return PageableResult
     */
    public static function of(array $results, int $resultsCount, Pageable $pageable): PageableResult
    {
        return new PageableResult($results, $resultsCount, $pageable);
    }

    /**
     * @return array
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * @return int
     */
    public function getResultCount(): int
    {
        return $this->resultCount;
    }

    /**
     * @return Pageable
     */
    public function getPageable(): Pageable
    {
        return $this->pageable;
    }

    /**
     * Retrieve an external iterator
     * @link https://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable|Iterator An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     * @since 5.0.0
     */
    public function getIterator(): Traversable|Iterator
    {
        return new class($this->results) implements Iterator {
            private array $results;

            public function __construct(array $results)
            {
                $this->results = $results;
            }

            /**
             * Return the current element
             * @link https://php.net/manual/en/iterator.current.php
             * @return mixed Can return any type.
             * @since 5.0.0
             */
            public function current(): mixed
            {
                return current($this->results);
            }

            /**
             * Move forward to next element
             * @link https://php.net/manual/en/iterator.next.php
             * @return void Any returned value is ignored.
             * @since 5.0.0
             */
            public function next(): void
            {
                next($this->results);
            }

            /**
             * Return the key of the current element
             * @link https://php.net/manual/en/iterator.key.php
             * @return mixed scalar on success, or null on failure.
             * @since 5.0.0
             */
            public function key(): mixed
            {
                return key($this->results);
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

                return $k !== false && $k !== null;
            }

            /**
             * Rewind the Iterator to the first element
             * @link https://php.net/manual/en/iterator.rewind.php
             * @return void Any returned value is ignored.
             * @since 5.0.0
             */
            public function rewind(): void
            {
                reset($this->results);
            }
        };
    }

    /**
     * Whether a offset exists
     * @link https://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset): bool
    {
        return isset($this->results[$offset]);
    }

    /**
     * Offset to retrieve
     * @link https://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset): mixed
    {
        return $this->results[$offset];
    }

    /**
     * Offset to set
     * @link https://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value): void
    {
        $this->results[$offset] = $value;
    }

    /**
     * Offset to unset
     * @link https://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset): void
    {
        unset($this->results[$offset]);
    }

    public function jsonSerialize(): array
    {
        return [
            'results' => $this->getResults(),
            'resultCount' => $this->getResultCount(),
            'pageable' => $this->getPageable()
        ];
    }
}
