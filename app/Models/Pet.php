<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    use HasFactory;
    /**
    * response api.
    */
    public function apiResponse()
    {
        $response = $this;
        return $response;
    }
}
