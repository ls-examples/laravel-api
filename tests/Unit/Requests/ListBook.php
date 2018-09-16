<?php

namespace Tests\Unit\Requests;


use App\Http\Requests\ListBook;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ListBookTest extends TestCase
{
    /**
     * @dataProvider rulesDataProvider
     * @param $attributes
     * @param $passes
     * @param array $errorsKeys
     */
    public function testRules($attributes, $passes, $errorsKeys = [])
    {
        $request = new ListBook();
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
                ['search' => 'some search', 'order_by' => 'title', 'order_direction' => 'desc'],
                true,
            ],
            [
                ['order_by' => 'title', 'order_direction' => 'asc'],
                true,
            ],
            [
                ['order_by' => 'author', 'order_direction' => 'asc'],
                true,
            ],
            [
                ['order_by' => 'author', 'order_direction' => 'desc'],
                true,
            ],
            [
                ['search' => 'some search', 'order_by' => 'id', 'order_direction' => 'desc'],
                false,
                ['order_by']
            ],
            [
                ['search' => 'some search', 'order_by' => 'description', 'order_direction' => 'desc'],
                false,
                ['order_by']
            ],
            [
                ['search' => 'some search', 'order_by' => '', 'order_direction' => 'desc'],
                false,
                ['order_by']
            ],
            [
                ['search' => 'some search',  'order_direction' => 'asc'],
                false,
                ['order_by']
            ],
            [
                ['search' => 'some search', 'order_by' => 'title', 'order_direction' => '1'],
                false,
                ['order_direction']
            ],
            [
                ['search' => 'some search', 'order_by' => 'title', 'order_direction' => ''],
                false,
                ['order_direction']
            ],
            [
                ['search' => 'some search', 'order_by' => 'title',],
                false,
                ['order_direction']
            ],
        ];
    }
}
