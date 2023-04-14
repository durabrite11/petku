<?php

namespace App\Models;

use App\PConstant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Groomer extends Model
{
    use HasApiTokens, HasFactory;

    protected $hidden = ['image', 'password'];
    /**
    * Get the member's image.
    */
    public function image()
    {
        return $this->morphOne(ImageResource::class, 'imageable');
    }

    /**
    * Get the member balance.
    */
    public function groomerBalance()
    {
        return $this->hasOne(GroomerBalance::class);
    }

    /**
    * Get the member location.
    */
    public function groomerLocation()
    {
        return $this->hasOne(GroomerLocation::class);
    }

    /**
    * Get the groomer's service.
    */
    public function groomerService()
    {
        return $this->hasMany(GroomerService::class);
    }
    
    public function apiResponse()
    {
        $image = $this->image;
        $imagePath = $image != null ? $this->image->url : "/images/user.png";

        $response = $this;
        $response["image_url"] = PConstant::BASE_URL."".$imagePath;
        $response["balance"] = $this->groomerBalance;
        $response["location"] = $this->groomerLocation;
        return $response;
    }
}
