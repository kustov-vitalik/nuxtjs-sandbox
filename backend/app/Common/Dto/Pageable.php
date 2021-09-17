<?php

declare(strict_types=1);


namespace App\Common\Dto;


use Symfony\Component\HttpFoundation\Request;

class Pageable implements \JsonSerializable
{
    private int $page;
    private int $size;
    private Sort $sort;

    public static function of(int $page, int $limit, string $sort = null): Pageable
    {
        return new Pageable($page, $limit, Sort::fromString($sort));
    }

    public static function ofRequest(Request $request): Pageable
    {
        $bag = $request->query;
        return self::of(
            $bag->getInt('page', 1),
            $bag->getInt('size', 25),
            $bag->get('sort')
        );
    }

    /**
     * Pageable constructor.
     * @param int $page
     * @param int $limit
     * @param Sort $sort
     */
    public function __construct(int $page, int $limit, Sort $sort)
    {
        if ($page < 1) {
            throw new \InvalidArgumentException('We use 1-based pagination. "page" parameter can not be less than 1');
        }
        $this->page = $page;
        $this->size = $limit;
        $this->sort = $sort;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @return Sort
     */
    public function getSort(): Sort
    {
        return $this->sort;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->size * ($this->page - 1);
    }

    /**
     * @param int $page
     */
    public function setPage(int $page): void
    {
        $this->page = $page;
    }

    /**
     * @param int $size
     */
    public function setSize(int $size): void
    {
        $this->size = $size;
    }

    /**
     * @param Sort $sort
     */
    public function setSort(Sort $sort): void
    {
        $this->sort = $sort;
    }

    public function jsonSerialize(): array
    {
        return [
            'page' => $this->getPage(),
            'size' => $this->getSize(),
            'sort' => $this->getSort(),
        ];
    }
}
