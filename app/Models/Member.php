<?php

namespace App\Models;

use App\PConstant;
use ErrorException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Member extends Model
{
    use HasApiTokens, HasFactory;

    protected $hidden = ['image', 'password', 'memberBalance', 'memberLocation'];

    /**
    * Get the member's image.
    */
    public function image()
    {
        return $this->morphOne(ImageResource::class, 'imageable');
    }

    /**
    * Get the member's token.
    */
    public function token()
    {
        return $this->morphOne(PersonalAccessToken::class, 'imageable');
    }
    

    /**
    * Get the member balance.
    */
    public function memberBalance()
    {
        return $this->hasOne(MemberBalance::class);
    }

    /**
    * Get the member location.
    */
    public function memberLocation()
    {
        return $this->hasOne(MemberLocation::class);
    }

    /**
    * Get the member balance.
    */
    public function memberRating()
    {
        return $this->hasMany(MemberRating::class, 'member_id_rating', 'id');
    }

    
    public function getRating()
    {
        $rating = "Belum Ada";
        $memberRating = $this->memberRating;
        $total =0;
        $count =0;
        foreach ($memberRating as $rating) {
            $total += $rating->rating;
            $count++;
        }
        if ($count > 0 ) {
            $rating = $total/$count;
        }
        return $rating;
    }


    
    public function getBalance()
    {
        $balance =  $this->memberBalance?$this->memberBalance->balance:0;
        return $balance;
    }

    public function inOutBalance($balanceIn, $balanceOut, $modul, $description){
        $memberBalanceModel = $this->memberBalance;
        if ($memberBalanceModel == null) {
            $memberBalanceModel = new  MemberBalance();
            $memberBalanceModel->balance = 0;
            $memberBalanceModel->member_id = $this->id;
            $memberBalanceModel->save();
        }

        if ($balanceIn < 0) {
            throw new ErrorException("Balance in cant be minus");
        }
        if ($balanceOut < 0) {
            throw new ErrorException("Balance out cant be minus");
        }
        $balanceBefore = $memberBalanceModel->balance;
        $balanceAfter = $memberBalanceModel->balance + $balanceIn - $balanceOut;

        $memberBalanceHistoryModel = new MemberBalanceHistory();
        $memberBalanceHistoryModel->member_id = $this->id;
        $memberBalanceHistoryModel->balance_before = $balanceBefore;
        $memberBalanceHistoryModel->balance_after = $balanceAfter;
        $memberBalanceHistoryModel->balance_in = $balanceIn;
        $memberBalanceHistoryModel->balance_out = $balanceOut;
        $memberBalanceHistoryModel->module = $modul;
        $memberBalanceHistoryModel->description = $description;
        $memberBalanceHistoryModel->save();

        $memberBalanceModel->balance = $balanceAfter;
        $memberBalanceModel->save();
        return $memberBalanceModel;
    }
    
    public function getImage(){
        $image = $this->image;
        $imagePath = $image != null ? $this->image->url : PConstant::IMAGE_MEMBER_DEFAULT;
        if($this->type == "Groomer"){
            $imagePath = $image != null ? $this->image->url : PConstant::IMAGE_GROOMER_DEFAULT;
        }
        return url($imagePath);
    }
    public function apiResponse()
    {
        $response = $this;
        $response["image_url"] = $this->getImage();
        $response["balance"] = $this->memberBalance?$this->memberBalance->balance:0;
        $response["location"] = $this->memberLocation;
        return $response;
    }
}
