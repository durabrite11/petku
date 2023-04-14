<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatDetail extends Model
{
    use HasFactory;
    protected $table ="chat_detail";

    
    public function member()
    {
        return $this->belongsTo(Member::class);
    }
    
    public function chat()
    {
        return $this->belongsTo(Chat::class);
    }

    public function apiResponse()
    {
        $response = $this;
        return $response;
    }
}
