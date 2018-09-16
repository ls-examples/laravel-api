<?php

namespace Tests\Unit\Requests;


use App\Http\Requests\SaveBook;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class SaveBookTest extends TestCase
{
    /**
     * @dataProvider rulesDataProvider
     * @param $attributes
     * @param $passes
     * @param array $errorsKeys
     */
    public function testRules($attributes, $passes, $errorsKeys = [])
    {
        $request = new SaveBook();
        $rules = $request->rules();
        $validator = Validator::make($attributes, $rules);
        $this->assertEquals($passes, $validator->passes(), var_export($validator->errors(), 1));

        foreach ($errorsKeys as $errorsKey) {
            $this->assertArrayHasKey($errorsKey, $validator->failed());
        }
    }

    public function rulesDataProvider()
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
                [
                    'title' => 'some name',
                    'author' => 'some author',
                    'description' => 'some description',
                    'image' => '',
                    'year' => 1999,
                ], true,
            ],
            [
                array(
                    'title' => 'some name',
                    'author' => 'some author',
                    'description' => 'some description',
                    'image' => $this->getValidBase64Image(),
                    'year' => 1999,
                ), true,
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
                    'image'  => 'invalid'
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
                ['title' => '',
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
