<?php

namespace App\Http\Controllers;

use App\Book;
use App\Http\Requests\ListBook;
use App\Http\Requests\SaveBook;
use App\Http\Resources\Book as BookResource;
use App\Http\Resources\BookCollection;
use App\Images\ImageException;
use App\Images\ImageService;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{

    /**
     * @param ListBook $request
     * @return BookCollection
     */
    public function index(ListBook $request)
    {
        return new BookCollection(\BookFilterConverter::convertFromRequest($request)
            ->search());
    }


    /**
     * @param SaveBook $request
     * @return BookResource
     * @throws ImageException
     */
    public function create(SaveBook $request)
    {
        $data = $request->all();
        if ($image = $request->get('image')) {
            $data['image_id'] = \Images::saveImage($data['image'])->id;

        }

        $book = Book::create($data);
        return new BookResource($book);
    }

    /**
     * @param Book $book
     * @param SaveBook $request
     * @return BookResource
     */
    public function update(Book $book, SaveBook $request)
    {
        $data = $request->all();
        if ($image = $request->get('image')) {
            $data['image_id'] = \Images::saveImage($data['image'])->id;

        }
        $book->fill($data);
        $book->save();
        return new BookResource($book);
    }

    /**
     * @param Book $book
     * @return void
     * @throws \Exception
     */
    public function delete(Book $book)
    {
        $book->delete();
    }

    /**
     * @param Book $book
     * @return BookResource
     */
    public function view(Book $book)
    {
        return new BookResource($book);
    }
}
