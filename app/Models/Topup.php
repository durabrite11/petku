<?php

namespace App\Models;

use App\PConstant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Topup extends Model
{
    use HasFactory;

    protected $table = "topup";


    /**
    * Get the topup's image.
    */
    public function image()
    {
        return $this->morphOne(ImageResource::class, 'imageable');
    }


    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function getImage(){
        $image = $this->image;
        $imagePath = $image != null ? $this->image->url : PConstant::IMAGE_MEMBER_DEFAULT;
        return url($imagePath);
    }

    public function toResult(){
        return [
            "id"=> $this->id,
            "memberId" => $this->member_id,
            "memberName" => $this->member==null?"":$this->member->name,
            "code" => $this->code,
            "bank" => $this->bank,
            "bankAccountName" => $this->bank_account_name,
            "bankAccountNumber" => $this->bank_account_number,
            "nominal" => $this->nominal,
            "totalTransfer" => $this->total_transfer,
            "status" => $this->status,
            "createdAt" =>(string) $this->created_at,
            "image" => $this->getImage(),
        ];
    }
}
