<?php

namespace App\Book;


use App\Book;
use App\Http\Requests\SaveBook;


class BookConverter
{
    /**
     * @param \App\Http\Requests\SaveBook $request
     * @return \App\Book\BookDto
     */
    public function convertFromRequest(SaveBook $request)
    {
        return (new BookDto())
            ->setTitle($request->get('title'))
            ->setAuthor($request->get('author'))
            ->setDescription($request->get('description'))
            ->setYear($request->get('year' ))
            ->setImage($request->get('image'));
    }


}
