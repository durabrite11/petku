<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\MemberPet;
use App\Models\Pet;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PetController extends Controller
{
    public function get(Request $request){
        $errCode= 0;
        $errMessage = "";
        $data = [];
        $model = $request->user();
        
        if($errCode == 0){
            try {
                $serviceModel = new Pet();
                if(strlen($request->id) > 0){
                    $data = $serviceModel->find($request->id)->apiResponse();
                }else{
                    $data =  $serviceModel::get()->map(function (Pet $item){
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
