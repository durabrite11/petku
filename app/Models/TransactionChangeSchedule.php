<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionChangeSchedule extends Model
{
    use HasFactory;
    protected $table ="transaction_change_schedule";

    public function transaction(){
        return $this->belongsTo(Transaction::class);
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
