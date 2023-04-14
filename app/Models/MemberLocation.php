<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberLocation extends Model
{
    use HasFactory;

    
    public function member(){
        $this->belongsTo(Member::class);
    }

    
    /**
    * Get the member's image.
    */
    public function apiResponse()
    {
        $response = $this;
        return $response;
    }

}
