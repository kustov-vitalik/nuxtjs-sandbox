<?php

declare(strict_types=1);

namespace App\Common\Dto;

use InvalidArgumentException;

class SortOrder implements \JsonSerializable
{
    private string $property;
    private string $direction;

    /**
     * @param string $sortOrder
     * @return SortOrder
     */
    public static function fromString(string $sortOrder): SortOrder
    {
        [$property, $direction] = explode('.', $sortOrder);

        return new SortOrder($property, mb_strtoupper($direction));
    }

    /**
     * SortOrder constructor.
     * @param string $property
     * @param string $direction
     */
    public function __construct(string $property, string $direction)
    {
        if (!in_array(mb_strtoupper($direction), [Direction::ASC, Direction::DESC], true)) {
            throw new InvalidArgumentException(sprintf('Invalid direction: %s', $direction));
        }

        $this->property = $property;
        $this->direction = $direction;
    }

    /**
     * @return string
     */
    public function getProperty(): string
    {
        return $this->property;
    }

    /**
     * @return string
     */
    public function getDirection(): string
    {
        return $this->direction;
    }

    public function jsonSerialize(): array
    {
        return [
            'property' => $this->getProperty(),
            'direction' => $this->getDirection(),
        ];
    }
}
