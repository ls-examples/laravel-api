<?php

namespace App\Http\Controllers;

use App\Book;
use App\Http\Requests\ListBook;
use App\Http\Requests\SaveBook;
use App\Http\Resources\Book as BookResource;
use App\Http\Resources\BookCollection;
use App\Images\ImageException;
use OpenApi\Annotations as OA;

class BookController extends Controller
{

    /**
     * @OA\Get(
     *     path="/book",
     *     @OA\Parameter(
     *          in="query",
     *          name="search",
     *          required=false,
     *          description="Query for search",
     *          @OA\Schema(type="string", nullable=true)
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="order_by",
     *          required=false,
     *          description="Field order by",
     *          @OA\Schema(type="string", nullable=true, enum={"title", "author"})
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="order_direction",
     *          required=false,
     *          description="Order direction",
     *          @OA\Schema(type="string", nullable=true, enum={"desc", "asc"})
     *     ),
     *     @OA\Parameter(
     *          in="query",
     *          name="page",
     *          required=false,
     *          description="Page number",
     *          @OA\Schema(type="integer", nullable=true)
     *     ),
     *     @OA\Response(
     *      response="200",
     *      description="Success reaponse",
     *     ),
     *     @OA\Response(
     *      response="422",
     *      description="Invalid request",
     *     ),
     *     @OA\Response(
     *      response="500",
     *      description="Server error",
     *     ),
     * )
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
        } else {
            $data['image_id'] = null;
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
