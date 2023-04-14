<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MemberBalanceHistory;
use Exception;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    //get bank item
    public function get(Request $request){
        $errCode= 0;
        $errMessage = "";
        $data = [];
        
        $userModel = $request->user();
        if($errCode == 0){
            try {
                $model = new MemberBalanceHistory();
                if(strlen($request->id) > 0){
                    $data = $model->find($request->id)->apiResponse();
                }else{
                    $data =  $model::orderBy("created_at", "desc")
                        ->where('member_id', $userModel->id)
                        ->get()
                        ->map(function (MemberBalanceHistory $item){
                        return $item->apiResponse();
                    });
                }
            } catch (Exception $ex) {
                $errCode = 1;
                $errMessage = $ex->getMessage();
            }
        }
        $response = ["errCode"=> $errCode, "errMessage"=>$errMessage, "data"=> $data];
        return response()->json($response);
    }
}
