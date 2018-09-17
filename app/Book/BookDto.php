<?php

namespace App\Book;


class BookDto
{
    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $author;

    /**
     * @var integer|null
     */
    public $year;

    /**
     * @var string|null
     */
    public $image;

    /**
     * @param string $value
     * @return BookDto
     */
    public function setTitle(string $value): BookDto
    {
        $this->title = $value;
        return $this;
    }

    /**
     * @param string $value
     * @return BookDto
     */
    public function setAuthor(string $value): BookDto
    {
        $this->author = $value;
        return $this;
    }

    /**
     * @param string $value
     * @return BookDto
     */
    public function setDescription(string $value): BookDto
    {
        $this->description = $value;
        return $this;
    }

    /**
     * @param int|null $value
     * @return BookDto
     */
    public function setYear(?int $value): BookDto
    {
        $this->year = $value;
        return $this;
    }

    /**
     * @param null|string $value
     * @return BookDto
     */
    public function setImage(?string $value): BookDto
    {
        $this->image = $value;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
          'title' => $this->title,
          'author' => $this->author,
          'description' => $this->description,
          'year' => $this->year,
          'image' => $this->image,
        ];
    }
}
