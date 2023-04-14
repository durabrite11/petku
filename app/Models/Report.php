<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;
    
    protected $table ="report";


    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function memberReported()
    {
        return $this->belongsTo(Member::class, 'member_id_reported', 'member_id');
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
