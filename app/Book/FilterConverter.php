<?php

namespace App\Book;


use App\Http\Requests\ListBook;


class FilterConverter
{
    /**
     * @param \App\Http\Requests\ListBook $request
     * @return Filter
     */
    public function convertFromRequest(ListBook $request)
    {

        return (new Filter())
            ->setOrderBy($request->get('order_by', 'id'))
            ->setOrderDesc($request->get('order_direction', 'desc'))
            ->setSearch($request->get('search', ''));
    }

}
