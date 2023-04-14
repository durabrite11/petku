<?php

namespace App\Models;

use App\PConstant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;
    protected $table ="chat";

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function memberGroomer()
    {
        return $this->belongsTo(Member::class, 'member_id_groomer', 'id');
    }
    
    public function apiResponse($userType)
    {
        $response = $this;
        $description="Belum ada Pesan";
        $image= PConstant::IMAGE_MEMBER_DEFAULT;
        if($userType == 'Groomer'){
           $title = $this->member->name;
           $image = $this->member->getImage();
           $memberId = $this->member_id;
           $chatId = $this->id;
           $chatDetailModel = ChatDetail::where('chat_id', $chatId)
            ->where('member_id', $memberId)
            ->orderBy('created_at', 'desc')
            ->first();
            if($chatDetailModel){
                $description = $chatDetailModel->chat;
            }
        }else {
           $title = $this->memberGroomer->name;
           $memberId = $this->member_id_groomer;
           $image = $this->member->getImage();
           $chatId = $this->id;
           $chatDetailModel = ChatDetail::where('chat_id', $chatId)
            ->where('member_id', $memberId)
            ->orderBy('created_at', 'desc')
            ->first();
            if($chatDetailModel){
                $description = $chatDetailModel->chat;
            }
        }
        $response["title"] = $title;
        $response["description"] = $description;
        $response["image"] = $image;
        return $response;
    }
}
