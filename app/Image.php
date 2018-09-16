<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Image
 *
 * @mixin \Eloquent
 * @property int $id
 * @property string $original_name
 * @property string $system_sub_path
 * @property string $extension
 * @property int $size
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Image whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Image whereExtension($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Image whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Image whereOriginalName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Image whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Image whereSystemSubPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Image whereUpdatedAt($value)
 */
class Image extends Model
{
    protected $dates = [
        'created_at', 'updated_at'
    ];

    protected $fillable = [
        'original_name', 'system_sub_path', 'mime_type', 'extension', 'size'
    ];
}
