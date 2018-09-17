<?php

namespace App\Book\Services;


use App\Book;
use App\Book\BookException;
use Illuminate\Support\Facades\DB;

class BookService
{
    /**
     * @param Book\BookDto $bookData
     * @return \App\Book
     * @throws \App\Book\BookException
     * @throws \Exception
     */
    public function create(Book\BookDto $bookData)
    {
        $book = new Book($bookData->toArray());

        DB::beginTransaction();
        try {
            if ($bookData->image) {
                $image = \Images::saveImage($bookData->image);
                $book->image_id = $image->id;
            }

            $book->save();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new BookException($e);
        }

        DB::commit();

        return $book;
    }

    /**
     * @param \App\Book $book
     * @param Book\BookDto $bookData
     * @throws \App\Book\BookException
     * @throws \Exception
     */
    public function update(Book $book, Book\BookDto $bookData)
    {
        $book->fill($bookData->toArray());
        DB::beginTransaction();
        try {
            if ($bookData->image) {
                $image = \Images::saveImage($bookData->image);
                $book->image_id = $image->id;
            } else {
                $book->image_id = null;
            }

            $book->save();
        } catch (\Exception $e) {
            DB::rollBack();
            throw new BookException($e);
        }

        DB::commit();
    }

}
