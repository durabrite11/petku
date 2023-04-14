<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceType;
use Exception;
use Illuminate\Http\Request;

class ServiceTypeController extends Controller
{
    
    public function get(Request $request){
        $errCode= 0;
        $errMessage = "";
        $data = [];
        $model = $request->user();
        
        if($errCode == 0){
            try {
                $serviceModel = new ServiceType();
                if(strlen($request->id) > 0){
                    $data = $serviceModel->find($request->id)->apiResponse();
                }else{
                    $data =  $serviceModel::where('member_id', "=", $request->member_id)
                        ->whereNull('member_id', "=", $request->member_id)
                        ->get()->map(function (ServiceType $item) use ($model){
                            $response = $item->apiResponse();
                            $response["totalService"] =$item->service->where('member_id', $model->id)->count();
                        return $response ;
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
    public function item($id, Request $request){
        $errCode= 0;
        $errMessage = "";
        $data = [];
        $model = $request->user();
        $memberId = $request->member_id;
        if(strlen($request->member_id) == 0){
            $memberId = $model->id;
        }
        
        if($errCode == 0){
            try {
                $serviceModel = new Service();
                $data =  $serviceModel::where('service_type_id', "=", $id)
                    ->where('member_id', "=", $memberId)
                    ->get()
                    ->map(function (Service $item){
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
