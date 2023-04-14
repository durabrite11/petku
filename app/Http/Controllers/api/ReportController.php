<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use Illuminate\Http\Request;
use App\Models\Report;
use Exception;

class ReportController extends Controller
{
    public function create(Request $request){
        $errCode= 0;
        $errMessage = "";
        $data = [];
        $model = $request->user();
        $memberId = $model->id;
        $memberIdReported = $request->id;
        $description = $request->description;
        $reportModel = null;
        if ($errCode == 0) {
            $memberModel = Member::where("id", $memberIdReported)
                ->first();
            if($memberModel == null){
                $errCode = 1;
                $errMessage = "Member atau Groomer ini tidak tersedia atau dihapus";
            }
        }
        if($errCode == 0){
            try {
                $reportModel = Report::where("member_id", $memberId)
                    ->where("member_id_reported", $memberIdReported)
                    ->first();
                if ($reportModel == null) {
                    $reportModel = new Report();
                    $reportModel->member_id = $memberId;
                    $reportModel->member_id_reported = $memberIdReported;
                }
                $reportModel->description = $description;
                $reportModel->save();
                $data= $reportModel->apiResponse();
            } catch (Exception $ex) {
                $errCode = 1;
                $errMessage = $ex->getMessage();
            }
        }
        $response = ["errCode"=> $errCode, "errMessage"=>$errMessage, "data"=> $data];
        return response()->json($response);
    }
}
