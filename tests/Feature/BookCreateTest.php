<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\TestCase;

class BookCreateTest extends TestCase
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
            ->json('POST', '/api/book/create', $data);
    }

    /**
     * @dataProvider createDataProvider
     * @param $data
     * @param $success
     * @param $errorFields
     */
    public function testCreate(array $data, bool $success, array $errorFields = [])
    {
        $response = $this->sendRequest($data);
        if ($success) {
            $response->assertSuccessful();
            $response->assertJsonFragment([
                'title' => $data['title'],
                'author' => $data['author'],
                'description' => $data['description'],
                'year' => isset($data['year']) ? $data['year'] ?: null : null,
            ]);
            $dataBaseAttributes = [];
            foreach ($data as $key => $value) {
                if ($key == 'image') {
                    continue;
                }

                $dataBaseAttributes[$key] = $value ?: null;
            }

            $this->assertDatabaseHas('books', $dataBaseAttributes);
            if ($data['image']) {
                $this->assertDatabaseMissing('books', [
                    'image_id' => null
                ]);
            } else {
                $response->assertJsonFragment([
                    'image' => null
                ]);
            }

            return;
        }


        $response->assertJsonValidationErrors($errorFields);

    }

    public function createDataProvider()
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
                    'title' => 'some name',
                    'author' => 'some author',
                    'description' => 'some description',
                    'image' => $this->getValidBase64Image(),
                    'year' => 1935
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
                    'description' => ''
                ], false, ['description']
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
