<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberBalanceHistory extends Model
{
    use HasFactory;

    protected $table = "member_balance_history";

    
    public function apiResponse()
    {
        $response = $this;
        $response["createdAt"] = $this->created_at->format("Y-m-d H:i:s");
        return $response;
    }
}
