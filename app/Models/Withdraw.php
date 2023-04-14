<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdraw extends Model
{
    use HasFactory;
    
    protected $table ='withdraw';
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function toResult(){
        return [
            "id"=> $this->id,
            "memberId" => $this->member_id,
            "memberName" => $this->member==null?"":$this->member->name,
            "code" => $this->code,
            "bank" => $this->bank,
            "accountName" => $this->account_name,
            "accountNumber" => $this->account_number,
            "nominal" => $this->nominal,
            "charge" => $this->charge,
            "total" => $this->total,
            "status" => $this->status,
            "createdAt" =>(string) $this->created_at,
        ];
    }
}
