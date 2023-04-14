<?php

namespace App\Models;

use App\PConstant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberRating extends Model
{
    use HasFactory;
    protected $table = "member_rating";

    public function member(){
        return $this->belongsTo(Member::class);
    }

    public function memberRating(){
        return $this->belongsTo(Member::class, 'member_id_rating', 'id');
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
