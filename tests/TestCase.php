<?php

namespace Tests;

use App\Book;
use App\Image;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Faker\Factory;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected $faker;

    /**
     * TestCase constructor.
     * @param null $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->faker = Factory::create();
    }

    protected function getValidBase64Image()
    {
        return "data:image/jpg;base64," . base64_encode(file_get_contents(__DIR__ . "/files/book/valid.jpg"));
    }

    protected function getTooBigImageBase64()
    {
        return "data:image/jpg;base64," . base64_encode(file_get_contents(__DIR__ . "/files/book/big.jpg"));
    }

    /**
     * @param array $data
     * @param bool $withImage
     * @return Book
     */
    protected function createBookInDatabase(array $data = [], $withImage = false)
    {
        if ($withImage) {
            $image = factory(Image::class)->create();
            return factory(Book::class)->create(array_merge($data, [
                'image_id' => $image->id
            ]));
        }

        return factory(Book::class)->create($data);
    }
}
