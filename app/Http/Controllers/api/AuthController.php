<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Member;
use App\Models\Groomer;
use App\PConstant;
use Exception;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request){
        $errCode= 0;
        $errMessage = "";
        $data = [];
        $typeUser = PConstant::LOGIN_TYPE;
        $aktif = 1;
        $rules = array(
            'name'       => 'required',
            'phone' => 'required|min:11|numeric|unique:members,phone',
            'email'      => 'required|email||unique:members,phone',
            'password' => 'required|confirmed|min:6',
            'password_confirmation' => 'required|min:6',
            'question' => 'required',
            'answer' => 'required',
            'type' => 'required|in:'.implode(',', $typeUser)
        );
        $Validator = Validator::make($request->all(), $rules);
        if($Validator->fails()){
            $errCode = 1;
            $errMessage = $Validator->errors()->first();
        }
        DB::beginTransaction();
        if($errCode == 0){
            try {
                if($request->type == PConstant::LOGIN_GROOMER){
                    $aktif = 0;
                }
                $model = new Member();
                $model->name = $request->name;
                $model->phone = $request->phone;
                $model->email = $request->email;
                $model->type = $request->type;
                $model->aktif = $aktif;
                $model->question = $request->question;
                $model->answer = $request->answer;
                $model->password = Hash::make($request->password);
                $model->save();
                if($model){
                    $data = $model;
                } else {
                    $errCode = 1;
                    $errMessage = $Validator->errors();
                }
            } catch (Exception $ex) {
                $errCode = 1;
                $errMessage = $ex->getMessage();
            }
        }
        $response = ["errCode"=> $errCode, "errMessage"=>$errMessage, "data"=> $data];
        if ($errCode == 0) {
            DB::commit();
            return response()->json($response);
        } else {
            DB::rollBack();
            return response()->json($response);
        }
    }
    public function login(Request $request){
        $errCode= 0;
        $errMessage = "";
        $data = [];
        $typeUser = PConstant::LOGIN_TYPE;
        $rules = array(
            'email'      => 'required|email|',
            'password' => 'required|min:6',
            'type' => 'required|in:'.implode(',', $typeUser)
        );
        $Validator = Validator::make($request->all(), $rules);
        if($Validator->fails()){
            $errCode = 1;
            $errMessage = $Validator->errors()->first();
        }
        if ($errCode == 0) {
            $className = "App\\Models\\Member";
            $model = $className::where("email", "=", $request->email)
                ->where('type',"=", $request->type)
                ->first();
            if ($model){
                if($model->aktif == 0) {
                    $errCode = 1;
                    $errMessage = "Akun anda menunggu persetujuan!";
                }else if($model->status == 0){
                    $errCode = 1;
                    $errMessage = "Akun ini dinonaktifkan!";
                } else if(Hash::check($request->password, $model->password)){
                    $token = $model->createToken($model->name." ".$request->device_name)->plainTextToken;
                    $model->fcm_token = $request->token; 
                    $model->save();
                    $data = [];
                    $data["login"] = $model->apiResponse();
                    $data["token"] = $token;
                } else {
                    $errCode = 1;
                    $errMessage = "Password salah!";
                }
            } else {
                $errCode = 1;
                $errMessage = "Akun tidak ditemukan!";
            }
           
        }
        $response = ["errCode"=> $errCode, "errMessage"=>$errMessage, "data"=> $data];
        return response()->json($response);
    }
    
    public function forgotPassword(Request $request){
        $errCode= 0;
        $errMessage = "";
        $data = [];
        $rules = array(
            'email'      => 'required|email',
            'question'      => 'required',
            'answer'      => 'required',
        );
        $Validator = Validator::make($request->all(), $rules);
        if($Validator->fails()){
            $errCode = 1;
            $errMessage = $Validator->errors()->first();
        }
        if ($errCode == 0) {
            $model = Member::where("email", "=", $request->email)
                ->where('question',"=", $request->question)
                ->where('answer',"=", $request->answer)
                ->first();
            if ($model){
                $data = [];
                $data = $model->apiResponse();                
            } else {
                $errCode = 1;
                $errMessage = "Akun tidak ditemukan!";
            }
           
        }
        $response = ["errCode"=> $errCode, "errMessage"=>$errMessage, "data"=> $data];
        return response()->json($response);
    }

    public function setPassword(Request $request){
        $errCode= 0;
        $errMessage = "";
        $data = [];
        $rules = array(
            'id'      => 'required',
            'password' => 'required|confirmed|min:6',
            'password_confirmation' => 'required|min:6',
        );
        $Validator = Validator::make($request->all(), $rules);
        if($Validator->fails()){
            $errCode = 1;
            $errMessage = $Validator->errors()->first();
        }
        if ($errCode == 0) {
            $model = Member::find($request->id);
            if ($model){
                $model->password = Hash::make($request->password);
                $model->save();
                $data = [];
                $data = $model->apiResponse();                
            } else {
                $errCode = 1;
                $errMessage = "Akun tidak ditemukan!";
            }
           
        }
        $response = ["errCode"=> $errCode, "errMessage"=>$errMessage, "data"=> $data];
        return response()->json($response);
    }
    public function getInfo(Request $request){
        $errCode= 0;
        $errMessage = "";
        $data = [];
        $model = $request->user();
        if ($model->status == 0) {
            $errCode = 1;
            $errMessage = "Akun ini telah dinonaktifkan";
        }
        if($errCode == 0){
            try {
                $data = $model->apiResponse();
                $data["topupCharge"] = PConstant::TOPUP_CHARGE;
            } catch (Exception $ex) {
                $errCode = 1;
                $errMessage = $ex->getMessage();
            }
        }
        $response = ["errCode"=> $errCode, "errMessage"=>$errMessage, "data"=> $data];
        return response()->json($response);
    }
}
