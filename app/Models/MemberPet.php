<?php

namespace App\Models;

use App\PConstant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberPet extends Model
{
    use HasFactory;
    protected $table = "member_pet";

    public function pet(){
        return $this->belongsTo(Pet::class);
    }

    public function member(){
        return $this->belongsTo(Member::class);
    }

    /**
    * response api.
    */
    public function apiResponse()
    {
        $response = $this;
        $response["petName"] = $this->pet->name;
        return $response;
    }
}
