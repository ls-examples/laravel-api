<?php

namespace App\Book\Facades;


use Illuminate\Support\Facades\Facade;

class FilterConverter extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\Book\FilterConverter::class;
    }
}
