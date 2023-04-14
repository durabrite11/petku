<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChatCollection;
use App\Models\Chat;
use App\Models\ChatDetail;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    //get bank item
    public function get(Request $request){
        $errCode= 0;
        $errMessage = "";
        $data = [];
        $userModel = $request->user();
        $memberId = $userModel->id;
        if($errCode == 0){
            try {
                $chatModel = new Chat(); 
                if($userModel->type =='Groomer'){
                    $chatModel = $chatModel->where('member_id_groomer', "=", $memberId);
                } else {
                    $chatModel = $chatModel->where('member_id', "=", $memberId);
                }
                if(strlen($request->id) > 0){
                    $data = $chatModel->find($request->id)->apiResponse($userModel->type);
                }else{
                    $data =  $chatModel->get()->map(function (Chat $item) use ($userModel){ 
                        return $item->apiResponse($userModel->type);
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
    
    //create chat
    public function create(Request $request){
        $errCode= 0;
        $errMessage = "";
        $data = [];
        $model = $request->user();
        $memberId = $model->id;
        $memberIdGroomer = $request->id;
        $rules = array(
            'id' => 'required',
        );
        $Validator = Validator::make($request->all(), $rules);
        if($Validator->fails()){
            $errCode = 1;
            $errMessage = $Validator->errors()->first();
        }
        if($errCode == 0){
            try {
                $chatModel = Chat::where('member_id', $memberId)->where('member_id_groomer', $memberIdGroomer)->first();
                if($chatModel == null){
                    $chatModel = new Chat();
                    $chatModel->member_id = $memberId;
                    $chatModel->member_id_groomer = $memberIdGroomer;
                    $chatModel->last_message = now();
                    $chatModel->save();
                }
                $data = $chatModel->apiResponse($model->type);
            } catch (Exception $ex) {
                $errCode = 1;
                $errMessage = $ex->getMessage();
            }
        }
        $response = ["errCode"=> $errCode, "errMessage"=>$errMessage, "data"=> $data];
        return response()->json($response);
    }

    
    //create get message
    public function getMessage(Request $request){
        $errCode= 0;
        $errMessage = "";
        $data = [];
        $model = $request->user();
        $memberId = $model->id;
        $rules = array(
            'chat_id' => 'required',
        );
        $Validator = Validator::make($request->all(), $rules);
        if($Validator->fails()){
            $errCode = 1;
            $errMessage = $Validator->errors()->first();
        }
        if($errCode == 0){
            $chatModel = Chat::where('id', $request->chat_id)->first();
            if($chatModel == null){
                $errCode = 1;
                $errMessage = "Chat room tidak ditemukan";
            }
        }
        if($errCode == 0){
            try {
                $chatDetailModel = new ChatDetail();
                $data =  $chatDetailModel::where('chat_id', $request->chat_id)->orderBy('id', 'desc')->get()
                    ->map(function (ChatDetail $item){
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
    //create send message
    public function sendMessage(Request $request){
        $errCode= 0;
        $errMessage = "";
        $data = [];
        $model = $request->user();
        $memberId = $model->id;
        $rules = array(
            'id' => 'required',
            'chat' => 'required',
        );
        $Validator = Validator::make($request->all(), $rules);
        if($Validator->fails()){
            $errCode = 1;
            $errMessage = $Validator->errors()->first();
        }
        if($errCode == 0){
            $chatModel = Chat::where('id', $request->id)->first();
            if($chatModel == null){
                $errCode = 1;
                $errMessage = "Chat room tidak ditemukan";
            }
        }
        if($errCode == 0){
            try {
                $chatDetailModel = new ChatDetail();
                $chatDetailModel->member_id= $memberId;
                $chatDetailModel->chat_id= $request->id;
                $chatDetailModel->chat = $request->chat;
                $chatDetailModel->save();
                $data = $chatDetailModel->apiResponse();
                if ($model->type =="Groomer") {
                    $fcmToken = $chatModel->member->fcm_token;
                } else {
                    $fcmToken = $chatModel->memberGroomer->fcm_token;
                }
                $chatModel->last_message = now();
                $chatModel->save();
                sendPushNotification($fcmToken, "Pesan Baru", "Anda mendapatakn pesan baru");

            } catch (Exception $ex) {
                $errCode = 1;
                $errMessage = $ex->getMessage();
            }
        }
        $response = ["errCode"=> $errCode, "errMessage"=>$errMessage, "data"=> $data];
        return response()->json($response);
    }
}
