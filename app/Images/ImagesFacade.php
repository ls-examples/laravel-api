<?php

namespace App\Images;


use Illuminate\Support\Facades\Facade;

class ImagesFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return ImageService::class;
    }
}
