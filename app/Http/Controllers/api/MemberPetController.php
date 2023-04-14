<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\MemberPet;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MemberPetController extends Controller
{
    public function get(Request $request){
        $errCode= 0;
        $errMessage = "";
        $data = [];
        $model = $request->user();
        
        if($errCode == 0){
            try {
                $memberPetModel = new MemberPet();
                if(strlen($request->id) > 0){
                    $data = $memberPetModel->find($request->id)->apiResponse();
                }else{
                    $data =  $memberPetModel::where('member_id', "=", $model->id)->where('status', 1)
                        ->get()->map(function (MemberPet $item){
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

    public function create(Request $request){
        $errCode= 0;
        $errMessage = "";
        $data = [];
        $user = $request->user();
        $rules = array(
            'pet_id' => 'required',
            'name' => 'required|max:255',
            'age'       => 'required',
            'gender' => 'required|max:255',
        );
        $Validator = Validator::make($request->all(), $rules);
        if($Validator->fails()){
            $errCode = 1;
            $errMessage = $Validator->errors()->first();
        }
        if($errCode == 0){
            try {
                $petId = $request->pet_id;
                $name = $request->name;
                $age = $request->age;
                $gender = $request->gender;

                $memberPet = MemberPet::find($request->id);
                if($memberPet == null){
                    $memberPet = new MemberPet();
                }
                $memberPet->member_id = $user->id;
                $memberPet->pet_id = $petId;
                $memberPet->name = $name;
                $memberPet->age = $age;
                $memberPet->gender = $gender;
                $memberPet->save();
                $data = $memberPet->apiResponse();
            } catch (Exception $ex) {
                $errCode = 1;
                $errMessage = $ex->getMessage();
            }
        }
        $response = ["errCode"=> $errCode, "errMessage"=>$errMessage, "data"=> $data];
        return response()->json($response);
    }

    
    public function delete(Request $request){
        $errCode= 0;
        $errMessage = "";
        $data = [];
        $user = $request->user();
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
                $memberPet = MemberPet::find($request->id);
                if($memberPet){
                    $memberPet->status = 0;
                    $memberPet->save();
                }
                $data["message"]="Delete berhasil!";
            } catch (Exception $ex) {
                $errCode = 1;
                $errMessage = $ex->getMessage();
            }
        }
        $response = ["errCode"=> $errCode, "errMessage"=>$errMessage, "data"=> $data];
        return response()->json($response);
    }
}
