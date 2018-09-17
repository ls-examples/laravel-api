<?php

namespace App\Book;


use App\Book;

class Filter
{
    /**
     * @var null|string
     */
    private $search = null;

    /**
     * @var string
     */
    private $orderBy = 'id';

    /**
     * @var string
     */
    private $orderDirection = 'desc';


    private $pageSize = 10;

    /**
     * @param null|string $search
     * @return Filter
     */
    public function setSearch(?string $search): Filter
    {
        $this->search = $search;

        return $this;
    }

    /**
     * @param string $orderBy
     * @return Filter
     */
    public function setOrderBy(string $orderBy): Filter
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    /**
     * @param string $orderDirection
     * @return Filter
     */
    public function setOrderDirection(string $orderDirection): Filter
    {
        $this->orderDirection = $orderDirection;

        return $this;
    }


    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function search()
    {
        $query = Book::orderBy($this->orderBy, $this->orderDirection)
            ->with('image');

        if ($this->search) {
            $query->where('title', 'like', "%{$this->search}%")
                ->orWhere('author', 'like', "%{$this->search}%");
        }

        return $query->paginate($this->pageSize);
    }

    /**
     * @return null|string
     */
    public function getSearchValue(): ?string
    {
        return $this->search;
    }

    /**
     * @return string
     */
    public function getOrderBy(): string
    {
        return $this->orderBy;
    }

    /**
     * @return string
     */
    public function getOrderDirection(): string
    {
        return $this->orderDirection;
    }
}
