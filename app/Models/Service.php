<?php

namespace App\Models;

use App\PConstant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

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

    public function serviceType(){
        return $this->belongsTo(ServiceType::class);
    }

    public function pet(){
        return $this->belongsTo(Pet::class);
    }

    public function groomer(){
        return $this->belongsTo(Member::class, 'member_id', 'id');
    }

    /**
    * response api.
    */
    public function apiResponse()
    {
        $response = $this;
        $response["serviceTypeName"] = $this->serviceType->name;
        $response["petName"] = $this->pet->name;
        $response["location"] = $this->groomer->memberLocation;
        return $response;
    }
}
