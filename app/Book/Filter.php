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
     * @var null|string
     */
    private $orderBy = 'id';

    /**
     * @var null |string
     */
    private $orderDesc = 'desc';

    private $pageSize = 2;

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
     * @param null|string $orderBy
     * @return Filter
     */
    public function setOrderBy(?string $orderBy): Filter
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    /**
     * @param null|string $orderDesc
     * @return Filter
     */
    public function setOrderDesc(?string $orderDesc): Filter
    {
        $this->orderDesc = $orderDesc;

        return $this;
    }


    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function search()
    {

        $query = Book::orderBy($this->orderBy, $this->orderDesc)
            ->with('image');

        if ($this->search) {
            $query->where('title', 'like', "%{$this->search}%")
                ->orWhere('author', 'like', "%{$this->search}%");
        }

        return $query->paginate($this->pageSize);
    }
}
