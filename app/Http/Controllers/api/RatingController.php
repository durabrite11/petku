<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\MemberRating;
use Illuminate\Http\Request;
use App\Models\Report;
use App\Models\Transaction;
use Exception;
use Illuminate\Support\Facades\Validator;

class RatingController extends Controller
{
    public function create(Request $request){
        $errCode= 0;
        $errMessage = "";
        $data = [];
        $model = $request->user();
        $memberId = $model->id;
        $rating = $request->rating;
        $description = $request->description;
        $memberIdRating = $request->memberId;
        $transactionId = $request->transactionId;
        $ratingModel = null;
        $rules = array(
            'memberId'       => 'required',
            'transactionId'       => 'required',
            'rating'       => 'required',
            'description'       => 'required',
        );
        $Validator = Validator::make($request->all(), $rules);
        if($Validator->fails()){
            $errCode = 1;
            $errMessage = $Validator->errors()->first();
        }
        if ($errCode == 0) {
            $memberModel = MemberRating::where("member_id", $memberId)
                ->where("transaction_id", $transactionId)
                ->first();
            if($memberModel){
                $errCode = 1;
                $errMessage = "Anda sudah melakukan rating sebelumnya";
            }
        }
        if($errCode == 0){
            try {
                $ratingModel = MemberRating::where("member_id", $memberId)
                    ->where("member_id_rating", $memberIdRating)
                    ->first();
                if ($ratingModel == null) {
                    $ratingModel = new MemberRating();
                    $ratingModel->member_id = $memberId;
                    $ratingModel->member_id_rating = $memberIdRating;
                    $ratingModel->transaction_id = $transactionId;
                }
                $ratingModel->rating = $rating;
                $ratingModel->description = $description;
                $ratingModel->save();
                $data= $ratingModel->apiResponse();
            } catch (Exception $ex) {
                $errCode = 1;
                $errMessage = $ex->getMessage();
            }
        }
        $response = ["errCode"=> $errCode, "errMessage"=>$errMessage, "data"=> $data];
        return response()->json($response);
    }
    
    public function get(Request $request){
        $errCode= 0;
        $errMessage = "";
        $data = [];
        $memberId = $request->member_id;
        if($errCode == 0){
            try {
                $model =  MemberRating::where('member_id_rating', $memberId);
                $data = $model->orderBy('created_at', 'desc')
                    ->get()->map(function (MemberRating $item){
                    return $item->apiResponse();
                });
            } catch (Exception $ex) {
                $errCode = 1;
                $errMessage = $ex->getMessage();
            }
        }
        $response = ["errCode"=> $errCode, "errMessage"=>$errMessage, "data"=> $data];
        return response()->json($response);
    }
}
