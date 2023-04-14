<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    use HasFactory;
    protected $table = "service_type";

    public function service()
    {
        return $this->hasMany(Service::class);
    }
    /**
    * response api.
    */
    public function apiResponse()
    {
        $response = $this;
        return $response;
    }
}
