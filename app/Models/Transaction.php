<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $table ="transaction";
    protected $hidden = ['member', 'memberGroomer', 'transactionDetail', 'transactionChangeSchedule'];
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function memberGroomer()
    {
        return $this->belongsTo(Member::class, 'member_id_groomer', 'id');
    }

    public function transactionDetail()
    {
        return $this->hasOne(TransactionDetail::class);
    }
    
    public function transactionChangeSchedule()
    {
        return $this->hasMany(transactionChangeSchedule::class);
    }
    /**
    * response api.
    */
    public function apiResponse()
    {
        $isFavorite = "0";
        $serviceModel =$this->transactionDetail->first()->service;
        $serviceId = $serviceModel->id;
        $memebrId = $this->member->id;
        $favoriteModel = Favorite::where('member_id', $memebrId)->where('service_id', $serviceId)->first();
        if($favoriteModel!=null){
            $isFavorite = "1";
        }
        $response = $this;
        $response["detail"] = $this->transactionDetail;
        $response["buyer"] = $this->member;
        $response["groomer"] = $this->memberGroomer;
        $response["memberImage"] = $this->member?$this->member->getImage():"";
        $response["memberRating"] = $this->member?$this->member->getRating():"";
        $response["groomerImage"] = $this->memberGroomer?$this->memberGroomer->getImage():"";
        $response["groomerRating"] = $this->memberGroomer?$this->memberGroomer->getRating():"";
        $response["serviceName"] = $serviceModel->name;
        $response["serviceTypeName"] = $serviceModel->serviceType->name;
        $response["isFavorite"] =$isFavorite;
        $response["petName"] = $serviceModel->pet->name;
        $response["changeSchedule"] = $this->transactionChangeSchedule;
        return $response;
    }
}
