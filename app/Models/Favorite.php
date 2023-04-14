<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;
    
    protected $table ="favorite";


    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
    * response api.
    */
    public function apiResponse()
    {
        $response = $this;
        $response["petName"] = $this->service->pet->name;
        return $response;
    }
}
