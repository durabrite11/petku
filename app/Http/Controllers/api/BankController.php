<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BankCollection;
use App\Models\Bank;
use Exception;
use Illuminate\Http\Request;

class BankController extends Controller
{
    //get bank item
    public function get(Request $request){
        $errCode= 0;
        $errMessage = "";
        $data = [];
        $model = $request->user();
        
        if($errCode == 0){
            try {
                $bankModel = new Bank();
                if(strlen($request->id) > 0){
                    $data = $bankModel->find($request->id)->apiResponse();
                }else{
                    $data =  $bankModel::get()->map(function (Bank $item){
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
