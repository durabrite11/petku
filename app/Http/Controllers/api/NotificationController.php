<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Exception;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function sendNotification(Request $request){
        $errCode= 0;
        $errMessage = "";
        $data = [];
        $model = $request->user();
        
        if($errCode == 0){
            try {
                sendNotification($request->member_id, $request->title, $request->description);
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
        $model = $request->user();
        
        if($errCode == 0){
            try { 
                $notificationController = new Notification();
                if(strlen($request->id) > 0){
                    $data = $notificationController->find($request->id)->apiResponse();
                }else{
                    $data =  $notificationController::where('member_id', $model->id)
                        ->orderBy('created_at', 'desc')
                        ->get()->map(function (Notification $item){
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
