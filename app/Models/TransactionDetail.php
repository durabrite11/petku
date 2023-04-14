<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    use HasFactory;

    protected $table ="transaction_detail";
    
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

}
