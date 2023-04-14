<?php

namespace App\Models;

use App\PConstant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $hidden = ['image'];
    /**
    * Get the bank's image.
    */
    public function image()
    {
        return $this->morphOne(ImageResource::class, 'imageable');
    }

    /**
    * Get the bank's image url.
    */
    public function imageUrl()
    {
        $image = $this->image;
        $imagePath = $image != null ? $this->image->url : "/images/user.png";
        return PConstant::BASE_URL."".$imagePath;
    }
}
