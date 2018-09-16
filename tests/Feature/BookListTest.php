<?php

namespace Tests\Feature;

use App\Book;
use App\Image;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\TestCase;

class BookListTest extends TestCase
{

    use DatabaseMigrations;

    /**
     * @param array $data
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    private function sendRequest(array $data)
    {
        return $this
            ->withoutMiddleware(ThrottleRequests::class)
            ->json('GET', '/api/book', $data);
    }


    public function testSimpleList()
    {
        $book = factory(Book::class)->create();
        $image = factory(Image::class)->create();
        $bookWithImage = factory(Book::class)->create([
            'image_id' => $image->id,
        ]);
        $response = $this->sendRequest([]);
        $response->assertSuccessful();
        $response->assertJsonFragment([
            'year' => (int)$book->year,
            'id' => $book->id,
            'title' => $book->title,
            'description' => $book->description,
            'author' => $book->author,
            'image' => null,
        ]);

        $response->assertJsonFragment([
            'year' => (int)$bookWithImage->year,
            'id' => $bookWithImage->id,
            'title' => $bookWithImage->title,
            'description' => $bookWithImage->description,
            'author' => $bookWithImage->author,
            'image' => [
                'url' => route('imagecache', [
                    'template' => 'original',
                    'filename' => $image->system_sub_path,
                ]),
                'thumbnail' => route('imagecache', [
                    'template' => 'small',
                    'filename' => $image->system_sub_path,
                ])
            ]
        ]);
    }

    public function testSearch()
    {
        $search = 'search text';
        $correctBooks = $this->makeCorrectBooks($search);
        $notCorrectBooks = factory(Book::class, 5)->create();

        $response = $this->sendRequest([
            'search' => $search,
        ]);
        $response->assertSuccessful();
        foreach ($correctBooks as $book) {
            $response->assertJsonFragment([
                'id' => $book->id,
            ]);
        }
        foreach ($notCorrectBooks as $book) {
            $response->assertJsonMissing([
                'id' => $book->id,
            ]);
        }

    }

    /**
     * @dataProvider sortingDataProvider
     * @param string $orderBy
     * @param string $orderDirection
     * @return void
     */
    public function testSortingWithSearch(string $orderBy, string $orderDirection)
    {
        $search = 'search text';
        $correctBooks = new Collection();
        $correctBooks->add($this->createBookInDatabase([
            $orderBy => "a $search",
        ]));
        $correctBooks->add($this->createBookInDatabase([
            $orderBy => "b $search",
        ]));
        $correctBooks->add($this->createBookInDatabase([
            $orderBy => "c $search ",
        ]));

        factory(Book::class, 5)->create();

        $response = $this->sendRequest([
            'search' => $search,
            'order_by' => $orderBy,
            'order_direction' => $orderDirection,
        ]);

        $response->assertSuccessful();
        $data = collect(json_decode($response->content(), true)['data'])->pluck($orderBy)->all();
        $expectedData = $correctBooks->pluck($orderBy)->all();
        if ($orderDirection == 'desc') {
            $expectedData = array_reverse($expectedData);
        }
        $this->assertEquals($expectedData, $data);
    }

    /**
     * @dataProvider sortingDataProvider
     * @param string $orderBy
     * @param string $orderDirection
     * @return void
     */
    public function testSorting(string $orderBy, string $orderDirection)
    {
        $correctBooks = new Collection();
        $correctBooks->add($this->createBookInDatabase([
            $orderBy => "a ",
        ]));
        $correctBooks->add($this->createBookInDatabase([
            $orderBy => "b ",
        ]));
        $correctBooks->add($this->createBookInDatabase([
            $orderBy => "c  ",
        ]));

        $response = $this->sendRequest([
            'order_by' => $orderBy,
            'order_direction' => $orderDirection,
        ]);

        $response->assertSuccessful();
        $data = collect(json_decode($response->content(), true)['data'])->pluck($orderBy)->all();
        $expectedData = $correctBooks->pluck($orderBy)->all();
        if ($orderDirection == 'desc') {
            $expectedData = array_reverse($expectedData);
        }
        $this->assertEquals($expectedData, $data);
    }

    /**
     * @return void
     */
    public function testDefaultSortingIdDEsc()
    {
        $correctBooks= factory(Book::class, 5)->create();

        $response = $this->sendRequest([]);

        $response->assertSuccessful();
        $data = collect(json_decode($response->content(), true)['data'])->pluck('id')->all();
        $expectedData = $correctBooks->pluck('id')->all();
        $expectedData = array_reverse($expectedData);

        $this->assertEquals($expectedData, $data);
    }

    private function makeCorrectBooks(string $search)
    {
        $faker = $this->faker;
        $correctBooks = new Collection();
        $correctBooks->add($this->createBookInDatabase([
            'title' => "$search  {$faker->word}",
        ]));
        $correctBooks->add($this->createBookInDatabase([
            'title' => "{$faker->word} $search",
        ]));
        $correctBooks->add($this->createBookInDatabase([
            'title' => "{$faker->word} $search {$faker->word}",
        ]));
        $correctBooks->add($this->createBookInDatabase([
            'author' => "$search  {$faker->word}",
        ]));
        $correctBooks->add($this->createBookInDatabase([
            'author' => "{$faker->word} $search",
        ]));
        $correctBooks->add($this->createBookInDatabase([
            'author' => "{$faker->word} $search {$faker->word}",
        ]));

        return $correctBooks;
    }

    public function sortingDataProvider()
    {
        return [
            [
                'title', 'desc'
            ],
            [
                'title', 'asc'
            ],
            [
                'author', 'desc'
            ],
            [
                'author', 'asc'
            ],
        ];
    }


    /**
     * @dataProvider invalidRequesDataProvider
     * @param array $data
     * @param array $errorFields
     */
    public function testIfInvalidRequest(array $data, array $errorFields = [])
    {
        $response = $this->sendRequest($data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($errorFields);
    }

    public function invalidRequesDataProvider()
    {
        return [
            [
                ['search' => 'some search', 'order_by' => 'id', 'order_direction' => 'desc'],
                ['order_by']
            ],
            [
                ['search' => 'some search', 'order_by' => 'description', 'order_direction' => 'desc'],
                ['order_by']
            ],
            [
                ['search' => 'some search', 'order_by' => '', 'order_direction' => 'desc'],
                ['order_by']
            ],
            [
                ['search' => 'some search',  'order_direction' => 'asc'],
                ['order_by']
            ],
            [
                ['search' => 'some search', 'order_by' => 'title', 'order_direction' => '1'],
                ['order_direction']
            ],
            [
                ['search' => 'some search', 'order_by' => 'title', 'order_direction' => ''],
                ['order_direction']
            ],
            [
                ['search' => 'some search', 'order_by' => 'title',],
                ['order_direction']
            ],
        ];
    }
}
