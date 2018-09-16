<?php

namespace Tests\Feature;

use App\Book;
use App\Image;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\TestCase;

class BookUpdateTest extends TestCase
{

    use DatabaseMigrations;

    /**
     * @param array $data
     * @param int $bookId
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    private function sendRequest(array $data, int $bookId)
    {
        return $this
            ->withoutMiddleware(ThrottleRequests::class)
            ->json('POST', "/api/book/{$bookId}/update", $data);
    }

    /**
     * @dataProvider updateDataProvider
     * @param $data
     * @param $success
     * @param $errorFields
     */
    public function testUpdate(array $data, bool $success, array $errorFields = [])
    {
        $existBook = factory(Book::class)->create();

        $response = $this->sendRequest($data, $existBook->id);
        if ($success) {
            $response->assertSuccessful();
            $dataBaseAttributes = [
                'id' => $existBook->id,
            ];
            foreach ($data as $key => $value) {
                if ($key == 'image') {
                    continue;
                }

                $dataBaseAttributes[$key] = $value ?: null;
            }

            $this->assertDatabaseHas('books', $dataBaseAttributes);
            if ($data['image']) {
                $this->assertDatabaseMissing('books', [
                    'id' => $existBook->id,
                    'image_id' => null
                ]);
            }

            return;
        }

        $response->assertStatus(422);
        $response->assertJsonValidationErrors($errorFields);

    }

    /**
     * @dataProvider updateDataProvider
     * @param $data
     * @param $success
     * @param $errorFields
     */
    public function testUpdateIfImageExist(array $data, bool $success, array $errorFields = [])
    {
        $image = factory(Image::class)->create();
        $existBook = factory(Book::class)->create([
            'image_id' => $image->id,
        ]);

        $response = $this->sendRequest($data, $existBook->id);
        if ($success) {
            $response->assertSuccessful();
            $response->assertJsonFragment([
                'title' => $data['title'],
                'author' => $data['author'],
                'description' => $data['description'],
                'year' => isset($data['year']) ? $data['year'] ?: null : null,
            ]);
            $dataBaseAttributes = [
                'id' => $existBook->id,
            ];
            foreach ($data as $key => $value) {
                if ($key == 'image') {
                    continue;
                }

                $dataBaseAttributes[$key] = $value ?: null;
            }

            $this->assertDatabaseHas('books', $dataBaseAttributes);
            if (isset($data['image'])) {
                $this->assertDatabaseMissing('books', [
                    'id' => $existBook->id,
                    'image_id' => $existBook->image_id
                ]);
            } else {
                $this->assertDatabaseHas('books', [
                    'id' => $existBook->id,
                    'image_id' => $existBook->image_id
                ]);
            }

            return;
        }

        $this->assertDatabaseHas('books', [
            'id' => $existBook->id,
            'title' => $existBook->title,
            'year' => $existBook->year,
            'author' => $existBook->author,
            'description' => $existBook->description,
            'image_id' => $existBook->image_id
        ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrors($errorFields);
    }

    public function updateDataProvider()
    {
        return [
            [
                [
                    'title' => 'some name',
                    'author' => 'some author',
                    'year' => 1999,
                    'description' => 'some description',
                    'image' => '',
                ], true,
            ],
            [
                [
                    'title' => 'some name',
                    'author' => 'some author',
                    'year' => '',
                    'description' => 'some description',
                    'image' => '',
                ], true,
            ],
            [
                array(
                    'year' => 1999,
                    'title' => 'some name',
                    'author' => 'some author',
                    'description' => 'some description',
                    'image' => $this->getValidBase64Image(),
                ), true,
            ],
            [
                [
                    'title' => 'some name',
                    'author' => 'some author',
                    'description' => 'some description'
                ], false, ['year']
            ],
            [
                [
                    'title' => 'some name',
                    'author' => 'some author',
                    'description' => 'some description',
                    'image' => $this->getTooBigImageBase64(),
                ], false, ['image']
            ],
            [
                [
                    'title' => 'some name',
                    'author' => 'some author',
                    'description' => 'some description',
                    'image' => 'invalid'
                ], false, ['image']
            ],
            [
                [
                    'title' => 'some name',
                    'author' => 'some author',
                    'description' => 'some description',
                ], false, ['image']
            ],
            [
                [
                    'year' => Carbon::tomorrow()->addYear()->format('Y'),
                    'title' => 'some name',
                    'author' => 'some author',
                    'description' => 'some description'
                ], false, ['year']
            ],
            [
                [
                    'title' => '',
                    'author' => 'some author',
                    'year' => 1999,
                    'description' => 'some description'
                ], false, ['title']
            ],
            [
                [
                    'author' => 'some author',
                    'year' => 1999,
                    'description' => 'some description'
                ], false, ['title']
            ],
            [
                [
                    'title' => 'some name',
                    'author' => '',
                    'year' => 1999,
                    'description' => 'some description'
                ], false, ['author']
            ],
            [
                [
                    'title' => 'some name',
                    'year' => 1999,
                    'description' => 'some description'
                ], false, ['author']
            ],
            [
                [
                    'title' => 'some name',
                    'author' => 'some author',
                    'year' => 1999,
                ], false, ['description']
            ],
            [
                [
                    'title' => 'some name',
                    'author' => 'some author',
                    'year' => 1999,
                    'description' => ''
                ], false, ['description']
            ],
            [
                [
                    'title' => 'some name',
                    'author' => 'some author',
                    'year' => 1999,
                    'description' => str_repeat("a", 2001)
                ], false, ['description']
            ],
            [
                [
                    'title' => str_repeat("a", 151),
                    'author' => 'some author',
                    'year' => 1999,
                    'description' => 'some text'
                ], false, ['title']
            ],
            [
                [
                    'title' => 'some name',
                    'author' => str_repeat("a", 101),
                    'year' => 1999,
                    'description' => 'some description'
                ], false, ['author']
            ]

        ];
    }

}
