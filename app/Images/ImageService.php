<?php

namespace App\Images;


use App\Image;
use Intervention\Image\Exception\ImageException;
use Intervention\Image\Facades\Image as InterventionImage;

class ImageService
{
    private $diskName = 'upload';

    /**
     * @param string $base64
     * @return \App\Image
     * @throws \App\Images\ImageException
     */
    public function saveImage(string $base64)
    {
        $explode = explode(',', $base64);
        if (count($explode) < 2) {
            throw new \App\Images\ImageException("can't create image, data is invalid");
        }
        $extension = str_replace(
            [
                'data:image/',
                ';',
                'base64',
            ],
            [
                '', '', '',
            ],
            $explode[0]
        );
        $subPath = str_random(10) . "." . $extension;

        try {
            InterventionImage::make($base64)->save($this->getImagePath($subPath));
        } catch (ImageException $e) {
            throw new \App\Images\ImageException($e);
        }

        return Image::create([
            'original_name' => '',
            'system_sub_path' => $subPath,
            'extension' => $extension,
            'size' => strlen(base64_decode($explode[1])),
        ]);
    }

    /**
     * @param $path
     * @return string
     */
    public function getImagePath($path)
    {
        return $this->disk()
            ->path($path);
    }

    /**
     * @param $path
     * @return bool
     */
    public function deleteImage($path)
    {
        return $this->disk()
            ->delete($path);
    }

    /**
     * @return \Illuminate\Filesystem\FilesystemAdapter
     */
    private function disk()
    {
        return \Storage::disk($this->diskName);
    }

    /**
     * @param string $subPath
     * @return string
     */
    public function getImageUrl(string $subPath)
    {
        return route('imagecache', [
            'template' => 'original',
            'filename' => $subPath,
        ]);
    }

    /**
     * @param string $subPath
     * @return string
     */
    public function getSmallImageUrl(string $subPath)
    {
        return route('imagecache', [
            'template' => 'small',
            'filename' => $subPath,
        ]);
    }
}
