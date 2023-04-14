<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Groomer;
use App\Models\ImageResource;
use App\Models\Member;
use App\Models\MemberLocation;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MemberController extends Controller
{
    public function update(Request $request){
        $errCode= 0;
        $errMessage = "";
        $data = [];
        $model = $request->user();
        $rules = array(
            'name'       => 'required',
            'phone'       => 'required',
            'hour_open'       => 'date_format:H:i',
            'hour_close'       => 'date_format:H:i',
        );
        $Validator = Validator::make($request->all(), $rules);
        if($Validator->fails()){
            $errCode = 1;
            $errMessage = $Validator->errors()->first();
        }
        if($errCode == 0){
            try {
                $model->name =  $request->name;
                $model->address =  $request->address;
                $model->hour_open =  $request->hour_open;
                $model->hour_close =  $request->hour_close;
                $model->save();
                if ($request->hasFile('image')) {
                    $imageResource = new ImageResource();
                    $imageResource->saveImage($request->file("image"), $model);
                }
                $data = $model->apiResponse();
            } catch (Exception $ex) {
                $errCode = 1;
                $errMessage = $ex->getMessage();
            }
        }
        $response = ["errCode"=> $errCode, "errMessage"=>$errMessage, "data"=> $data];
        return response()->json($response);
    }
    
    public function setLocation(Request $request){
        $errCode= 0;
        $errMessage = "";
        $data = [];
        $model = $request->user();
        $rules = array(
            'latitude'       => 'required',
            'longitude' => 'required',
            'address' => 'required|max:255',
            'description' => 'required|max:255',
        );
        $Validator = Validator::make($request->all(), $rules);
        if($Validator->fails()){
            $errCode = 1;
            $errMessage = $Validator->errors()->first();
        }
        
        if($errCode == 0){
            try {
                $memberLocationModel =  $model->memberLocation;
                if ($memberLocationModel!=null && $request->default  == 1 ){
                    return;
                }
                if($memberLocationModel == null){
                    $memberLocationModel = new MemberLocation();
                }
                $memberLocationModel->member_id = $model->id;
                $memberLocationModel->latitude = $request->latitude;
                $memberLocationModel->longitude = $request->longitude;
                $memberLocationModel->address = $request->address;
                $memberLocationModel->description = $request->description;
                $memberLocationModel->active = 1;
                $memberLocationModel->save();
                $data = $memberLocationModel->apiResponse();
            } catch (Exception $ex) {
                $errCode = 1;
                $errMessage = $ex->getMessage();
            }
        }
        $response = ["errCode"=> $errCode, "errMessage"=>$errMessage, "data"=> $data];
        return response()->json($response);
    }
}
