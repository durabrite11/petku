<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\Member;
use App\Models\Service;
use App\Models\ServiceType;
use App\PConstant;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
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
                        ->whereNull('member_id')
                        ->get()->map(function (ServiceType $item){
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

    public function save(Request $request){
        $errCode= 0;
        $errMessage = "";
        $data = [];
        $user = $request->user();
        
        $rules = array(
            'service_type_id' => 'required',
            'name' => 'required|max:255',
            'pet_id'       => 'required',
            'description' => 'required|max:255',
            'price' => 'numeric|min:0',
        );
        $Validator = Validator::make($request->all(), $rules);
        if($Validator->fails()){
            $errCode = 1;
            $errMessage = $Validator->errors()->first();
        }
        if($errCode == 0){
            try {
                $serviceModel = Service::find($request->id);
                if($serviceModel == null){
                    $serviceModel = new Service();
                }
                $serviceModel->member_id = $user->id;
                $serviceModel->service_type_id = $request->service_type_id;
                $serviceModel->pet_id = $request->pet_id;
                $serviceModel->name = $request->name;
                $serviceModel->description = $request->description;
                $serviceModel->price = $request->price;
                $serviceModel->save();
                $data = $serviceModel->apiResponse();
            } catch (Exception $ex) {
                $errCode = 1;
                $errMessage = $ex->getMessage();
            }
        }
        $response = ["errCode"=> $errCode, "errMessage"=>$errMessage, "data"=> $data];
        return response()->json($response);
    }

    
    public function detail(Request $request){
        $errCode= 0;
        $errMessage = "";
        $model = $request->user();
        $memberLocation = $model->memberLocation;
        $serviceId = $request->id;
        $priceDistance = 10000;
        $data = [];
        if($errCode == 0){
            try {
                    $serviceModel = Service::find($serviceId);
                    
                    $data = $serviceModel->apiResponse();


                    $qRsult = DB::table('member_locations')->selectRaw(" 
                    (6371 * ACOS( 
                        COS( RADIANS(".$memberLocation->latitude.") ) 
                      * COS( RADIANS( latitude ) ) 
                      * COS( RADIANS( longitude ) - RADIANS(".$memberLocation->longitude.") ) 
                      + SIN( RADIANS(".$memberLocation->latitude.") ) 
                      * SIN( RADIANS( latitude ) )
                        ) ) AS distance_in_km ")
                    ->where("member_id", $serviceModel->member_id)
                    ->orderBy("distance_in_km", "asc")
                    ->first();
                    $data["distance"] = $qRsult->distance_in_km;
                    $priceDistance = max($priceDistance, $qRsult->distance_in_km * PConstant::CHARGE_PER_KM);
                    $data["priceDistance"] = $priceDistance;
                    $data["total"] = $priceDistance+$serviceModel->price;
            } catch (Exception $ex) {
                $errCode = 1;
                $errMessage = $ex->getMessage();
            }
        }
        $response = ["errCode"=> $errCode, "errMessage"=>$errMessage, "data"=> $data];
        return response()->json($response);
    }

    public function setFavorite(Request $request){
        $errCode= 0;
        $errMessage = "";
        $data = [];
        $model = $request->user();
        $memberId = $model->id;
        $serviceId = $request->id;
        $favoriteModel = null;
        if ($errCode == 0) {
            $groomerModel = Service::where("id", $serviceId)
                ->first();
            if($groomerModel == null){
                $errCode = 1;
                $errMessage = "Layanan ini tidak tersedia atau dihapus";
            }
        }
        if($errCode == 0){
            try {
                $favoriteModel = Favorite::where("member_id", $memberId)
                    ->where("service_id", $serviceId)
                    ->first();
                if ($favoriteModel) {
                    $favoriteModel->delete();
                    $data["action"] = "Berhasil menghapus dari favorit";
                } else {
                    $favoriteModel = new Favorite();
                    $favoriteModel->member_id = $memberId;
                    $favoriteModel->service_id = $serviceId;
                    $favoriteModel->save();
                    $data["action"] = "Berhasil menambah ke favorit";
                }
            } catch (Exception $ex) {
                $errCode = 1;
                $errMessage = $ex->getMessage();
            }
        }
        $response = ["errCode"=> $errCode, "errMessage"=>$errMessage, "data"=> $data];
        return response()->json($response);
    }

    public function getFavorite(Request $request){
        $errCode= 0;
        $errMessage = "";
        $data = [];
        $model = $request->user();
        
        if($errCode == 0){
            try {
                $favoriteModel = new Favorite();
                if(strlen($request->id) > 0){
                    $data = $favoriteModel->find($request->id)->apiResponse();
                }else{
                    $data =  $favoriteModel::with(['member', 'service'])->where('member_id', $model->id)->get()->map(function (Favorite $item){
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
