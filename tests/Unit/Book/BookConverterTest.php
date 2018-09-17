<?php

namespace Tests\Unit\Book;


use App\Book\BookConverter;
use App\Http\Requests\SaveBook;
use Mockery;
use Tests\TestCase;

class BookConverterTest extends TestCase
{
    /**
     * @dataProvider convertFromRequestDataProvider
     * @param array $data
     */
    public function testConvertFromRequest(array $data)
    {
        $converter = new BookConverter();
        $request = $this->getMockRequest($data);
        $bookDto = $converter->convertFromRequest($request);
        foreach ($data as $field => $value) {
            $this->assertEquals($value, $bookDto->{$field});
        }
    }

    /**
     * @param $data
     * @return Mockery\Mock|SaveBook
     */
    private function getMockRequest($data)
    {
        $mock = Mockery::mock(SaveBook::class)->makePartial();

        $mock->shouldReceive('all')
            ->andReturn($data);

        foreach ($data as $field => $value) {
            $mock->shouldReceive('get')
                ->with($field)
                ->once()
                ->andReturn($value);
        }

        return $mock;
    }

    /**
     * @return array
     */
    public function convertFromRequestDataProvider()
    {
        return [
            [
                [
                    'title' => 'Руслан и Людмила',
                    'author' => 'Пушкин А.С',
                    'description' => 'Первая законченная поэма Александра Сергеевича Пушкина; волшебная сказка, вдохновлённая древнерусскими былинами',
                    'year' => 2014,
                    'image' => 'some base 64 code'
                ],
            ],
        ];
    }


}
