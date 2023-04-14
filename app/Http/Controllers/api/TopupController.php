<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\ImageResource;
use App\Models\Topup;
use App\PConstant;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use PDO;

class TopupController extends Controller
{
    public function get(Request $request){
        $errCode= 0;
        $errMessage = "";
        $model = $request->user();
        $data = [];
        
        if($errCode == 0){
            try {
                $topupModel = new Topup();
                if(strlen($request->id) > 0){
                    $model = $topupModel->where("member_id", $model->id)->where("id", $request->id)->first();
                    if($topupModel != null){
                        $data = $model->toResult();
                    }
                }else{
                    $data =  $topupModel::where("member_id", $model->id)
                        ->orderBy("created_at", "desc")
                        ->get()
                        ->map(function (Topup $item){
                            return $item->toResult();
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

    public function topup(Request $request){

        $errCode= 0;
        $errMessage = "";
        $data = [];
        $model = $request->user();
        $bankModel = null;
        $memberId = $model->id;
        $rules = array(
            'bank_id' => 'required',
            'nominal' => 'required|numeric|min:10000',
        );
        $Validator = Validator::make($request->all(), $rules);
        if($Validator->fails()){
            $errCode = 1;
            $errMessage = $Validator->errors()->first();
        }

        if($errCode == 0){
            $cekTopupModel = Topup::where("member_id", $memberId)->where("status", PConstant::TOPUP_STATUS_PENDING)->first();
            if($cekTopupModel != null){
                $errCode = 1;
                $errMessage = "You already have topup pending, please pay or cancel and create new topup";
            }
        }

        if($errCode == 0){
            $bankModel = Bank::find($request->bank_id);
            if($bankModel == null){
                $errCode = 1;
                $errMessage = "Bank not found";
            }
        }
        if($errCode == 0){
            if($request->hasFile('image') == false){
                $errCode = 1;
                $errMessage = "Bukti pembayaran harus diisi";
            }
        }

        if($errCode == 0){
            try {
                $totalTransfer = $request->nominal + PConstant::TOPUP_CHARGE;
                $topupModel = new Topup();
                $topupModel->code = nextTopupCode();
                $topupModel->member_id = $memberId;
                $topupModel->bank_id = $request->bank_id;
                $topupModel->bank = $bankModel->name;
                $topupModel->bank_account_name = $bankModel->account_name;
                $topupModel->bank_account_number = $bankModel->account_number;
                $topupModel->nominal = $request->nominal;
                $topupModel->charge = PConstant::TOPUP_CHARGE;
                $topupModel->total_transfer = $totalTransfer;
                $topupModel->save();
                if ($request->hasFile('image')) {
                    $imageResource = new ImageResource();
                    $imageResource->saveImage($request->file("image"), $topupModel);
                }
                $data = Topup::find($topupModel->id)->toResult();
            } catch (Exception $ex) {
                $errCode = 1;
                $errMessage = $ex->getMessage();
            }
        }

        $response = ["errCode"=> $errCode, "errMessage"=>$errMessage, "data"=> $data];
        return response()->json($response);
    }
}
