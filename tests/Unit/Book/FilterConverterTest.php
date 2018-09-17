<?php

namespace Tests\Unit\Book;


use App\Book\FilterConverter;
use App\Http\Requests\ListBook;
use Mockery;
use Tests\TestCase;

class FilterConverterTest extends TestCase
{
    /**
     * @dataProvider convertFromRequestDataProvider
     * @param array $data
     */
    public function testConvertFromRequest(array $data)
    {
        $converter = new FilterConverter();
        $request = $this->getMockRequest($data);
        $filter = $converter->convertFromRequest($request);
        $this->assertEquals($data['search'], $filter->getSearchValue());
        $this->assertEquals($data['order_by'] ?: 'id', $filter->getOrderBy());
        $this->assertEquals($data['order_direction'] ?: 'desc', $filter->getOrderDirection());
    }

    /**
     * @param $data
     * @return Mockery\Mock|ListBook
     */
    private function getMockRequest($data)
    {
        $mock = Mockery::mock(ListBook::class)->makePartial();

        $mock->shouldReceive('all')
            ->andReturn($data);

        $mock->shouldReceive('get')
            ->with('search', '')
            ->andReturn(isset($data['search']) ? $data['search'] : '');

        $mock->shouldReceive('get')
            ->with('order_by', 'id')
            ->andReturn(isset($data['order_by']) && $data['order_by'] ? $data['order_by'] : 'id');

        $mock->shouldReceive('get')
            ->with('order_direction', 'desc')
            ->andReturn(isset($data['order_direction']) && $data['order_direction'] ? $data['order_direction'] : 'desc');

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
                    'search' => 'some search',
                    'order_direction' => 'desc',
                    'order_by' => 'title',
                ],
            ],
            [
                [
                    'search' => '',
                    'order_direction' => 'desc',
                    'order_by' => 'title',
                ],
            ],
            [
                [
                    'search' => 'some search',
                    'order_direction' => '',
                    'order_by' => '',
                ],
            ],
        ];
    }


}
