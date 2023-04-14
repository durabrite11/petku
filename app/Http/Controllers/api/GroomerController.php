<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use App\Models\ImageResource;
use App\Models\Member;
use App\Models\MemberLocation;
use App\Models\Service;
use App\Models\ServiceType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class GroomerController extends Controller
{
    public function get(Request $request){
        $errCode= 0;
        $errMessage = "";
        $data = [];
        $user = $request->user();
        $memberLocation = $user->memberLocation;
        $search = $request->search;
        $filter = $request->filter;
        if($errCode == 0){
            try {
                $query = DB::table('member_locations')
                    ->join('members', 'members.id', '=', 'member_locations.member_id')
                    ->join('services', 'members.id', '=', 'services.member_id')
                    ->selectRaw("distinct(members.id), members.*,  member_locations.*, 
                    (6371 * ACOS( 
                        COS( RADIANS(".$memberLocation->latitude.") ) 
                      * COS( RADIANS( latitude ) ) 
                      * COS( RADIANS( longitude ) - RADIANS(".$memberLocation->longitude.") ) 
                      + SIN( RADIANS(".$memberLocation->latitude.") ) 
                      * SIN( RADIANS( latitude ) )
                        ) ) AS distance_in_km ")
                    ->where("members.type", "Groomer")
                    ->whereIn("members.id",function ($db){
                        $db->select("member_id")->from("services");
                    });
                if (strlen($search) > 0) {
                    $query = $query->where(function($q) use ($search){
                        $q->where("members.name", "like", '%'.$search.'%');
                        $q->orWhere("services.name", "like", '%'.$search.'%');
                    });
                }
                if ($filter == "favorite") {
                    $query = $query->whereIn("services.id", function ($q) use ($user){
                        $q->select("service_id")->from("favorite")->where('member_id', $user->id);
                    });
                }
                if ($filter == "petsaya") {
                    $query = $query->whereIn("services.pet_id", function ($q) use ($user){
                        $q->select("pet_id")->from("member_pet")->where('member_id', $user->id)->where("status", 1);
                    });
                }
                $query = $query->get();
                foreach ($query as $result) {
                    unset($result->password);
                    $groomerModel = Member::find($result->id);
                    $result->imageBanner = $groomerModel->getImage();
                    $data[] = $result;
                }
            } catch (Exception $ex) {
                $errCode = 1;
                $errMessage = $ex->getMessage();
            }
        }
        $response = ["errCode"=> $errCode, "errMessage"=>$errMessage, "data"=> $data];
        return response()->json($response);
    }
    
    public function getByService(Request $request){
        $errCode= 0;
        $errMessage = "";
        $data = [];
        $model = $request->user();
        $memberLocation = $model->memberLocation;
        if($errCode == 0){
            try {
                $query = DB::table('services')
                    ->join('services', 'services.id', '=', 'member_locations.member_id')
                    ->join('members', 'members.id', '=', 'member_locations.member_id')
                    ->selectRaw("members.*, member_locations.*, services.name, services.price, (6371 * ACOS( 
                        COS( RADIANS(".$memberLocation->latitude.") ) 
                      * COS( RADIANS( latitude ) ) 
                      * COS( RADIANS( longitude ) - RADIANS(".$memberLocation->longitude.") ) 
                      + SIN( RADIANS(".$memberLocation->latitude.") ) 
                      * SIN( RADIANS( latitude ) )
                        ) ) AS distance_in_km ")
                    ->where("members.type", "Groomer")
                    ->orderBy("distance_in_km", "asc")
                    ->get();
                foreach ($query as $result) {
                    unset($result->password);
                    $groomerModel = Member::find($result->id);
                    $result->imageBanner = $groomerModel->getImage();
                    $data[] = $result;
                }
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
        $groomerId = $request->id;
        if($errCode == 0){
            try {
                $qRsult = DB::table('member_locations')
                    ->join('members', 'members.id', '=', 'member_locations.member_id')->selectRaw("members.*,  member_locations.*, 
                    (6371 * ACOS( 
                        COS( RADIANS(".$memberLocation->latitude.") ) 
                      * COS( RADIANS( latitude ) ) 
                      * COS( RADIANS( longitude ) - RADIANS(".$memberLocation->longitude.") ) 
                      + SIN( RADIANS(".$memberLocation->latitude.") ) 
                      * SIN( RADIANS( latitude ) )
                        ) ) AS distance_in_km ")
                    ->where("members.type", "Groomer")
                    ->where("members.id", $groomerId)
                    ->orderBy("distance_in_km", "asc")
                    ->first();
                    unset($qRsult->password);
                    $serviceModel = ServiceType::with(['service'=> function ($q) use ($groomerId){
                        return $q->where("member_id", $groomerId);
                    }])->whereHas("service", function ($q) use ($groomerId){
                        return $q->where("services.member_id", $groomerId);
                    })->get();
                    $groomerModel = Member::find($groomerId);
                    $data = (array)$qRsult;
                    $data["memberRating"] = $groomerModel->getRating();
                    $data["imageBanner"] = $groomerModel->getImage();
                    $data["serviceType"] = $serviceModel;
            } catch (Exception $ex) {
                $errCode = 1;
                $errMessage = $ex->getMessage();
            }
        }
        $response = ["errCode"=> $errCode, "errMessage"=>$errMessage, "data"=> $data];
        return response()->json($response);
    }
}
