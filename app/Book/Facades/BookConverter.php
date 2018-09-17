<?php

namespace App\Book\Facades;


use Illuminate\Support\Facades\Facade;

class BookConverter extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\Book\BookConverter::class;
    }
}
