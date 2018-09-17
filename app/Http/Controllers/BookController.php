<?php

namespace App\Http\Controllers;

use App\Book;
use App\Http\Requests\ListBook;
use App\Http\Requests\SaveBook;
use App\Http\Resources\Book as BookResource;
use App\Http\Resources\BookCollection;
use BookConverter;
use OpenApi\Annotations as OA;

class BookController extends Controller
{

    /**
     * @OA\Get(
     *     path="/book",
     *
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
     *      description="Success response",
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
     * @OA\Post(
     *     path="/book/create",
     *     summary="Add new book",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="title",
     *                     type="string",
     *                     description="Title",
     *                     nullable=false,
     *                     maxLength=150
     *                 ),
     *                 @OA\Property(
     *                     property="author",
     *                     type="string",
     *                     description="Author",
     *                     nullable=false,
     *                     maxLength=100
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     description="Description",
     *                     nullable=false,
     *                     maxLength=2000,
     *                 ),
     *                 @OA\Property(
     *                     property="year",
     *                     type="integer",
     *                     description="Year",
     *                     nullable=true,
     *                 ),
     *                 @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     description="Image in base64, max 512Kb",
     *                     nullable=true,
     *                 ),
     *                 example={"image": "", "year": "2014", "title": "Руслан и Людмила", "author": "Пушкин А.С", "description" : "Первая законченная поэма Александра Сергеевича Пушкина; волшебная сказка, вдохновлённая древнерусскими былинами"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     ),
     * )
     * @param SaveBook $request
     * @param Book\Services\BookService $books
     * @return BookResource
     * @throws Book\BookException
     */
    public function create(SaveBook $request, Book\Services\BookService $books)
    {
        $book = $books->create(
            BookConverter::convertFromRequest($request)
        );
        return new BookResource($book);
    }

    /**
     * @OA\Post(
     *     path="/book/{book}/update",
     *     summary="Update book",
     *     @OA\Parameter(
     *          in="path",
     *          name="book",
     *          required=true,
     *          description="Book id",
     *          @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="title",
     *                     type="string",
     *                     description="Title",
     *                     nullable=false,
     *                     maxLength=150
     *                 ),
     *                 @OA\Property(
     *                     property="author",
     *                     type="string",
     *                     description="Author",
     *                     nullable=false,
     *                     maxLength=100
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     description="Description",
     *                     nullable=false,
     *                     maxLength=2000,
     *                 ),
     *                 @OA\Property(
     *                     property="year",
     *                     type="integer",
     *                     description="Year",
     *                     nullable=true,
     *                 ),
     *                 @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     description="Image in base64, max 512Kb",
     *                     nullable=true,
     *                 ),
     *                 example={"image": "", "year": "2014", "title": "Руслан и Людмила", "author": "Пушкин А.С", "description" : "Первая законченная поэма Александра Сергеевича Пушкина; волшебная сказка, вдохновлённая древнерусскими былинами"}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found"
     *     ),
     * )
     * @param Book $book
     * @param SaveBook $request
     * @return BookResource
     * @throws \Exception
     */
    public function update(Book $book, SaveBook $request, Book\Services\BookService $books)
    {
        $books->update(
            $book,
            BookConverter::convertFromRequest($request)
        );
        return new BookResource($book);
    }

    /**
     * @OA\Post(
     *     path="/book/{book}/delete",
     *     summary="Delete book",
     *     @OA\Parameter(
     *          in="path",
     *          name="book",
     *          required=true,
     *          description="Book id",
     *          @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found"
     *     ),
     * )
     * @param Book $book
     * @return void
     * @throws \Exception
     */
    public function delete(Book $book)
    {
        $book->delete();
    }

    /**
     * @OA\Get(
     *     path="/book/{book}",
     *     summary="View book",
     *     @OA\Parameter(
     *          in="path",
     *          name="book",
     *          required=true,
     *          description="Book id",
     *          @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="OK"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid request"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found"
     *     ),
     * )
     * @param Book $book
     * @return BookResource
     * @throws \Exception
     */
    public function view(Book $book)
    {
        return new BookResource($book);
    }
}
