<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Withdraw;
use App\PConstant;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use PDO;

class WithdrawController extends Controller
{
    public function get(Request $request){
        $errCode= 0;
        $errMessage = "";
        $model = $request->user();
        $data = [];
        
        if($errCode == 0){
            try {
                $withdrawModel = new Withdraw();
                if(strlen($request->id) > 0){
                    $model = $withdrawModel->where("member_id", $model->id)->where("id", $request->id)->first();
                    if($withdrawModel != null){
                        $data = $model->toResult();
                    }
                }else{
                    $data =  $withdrawModel::where("member_id", $model->id)
                        ->orderBy("created_at", "desc")
                        ->get()
                        ->map(function (Withdraw $item){
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

    public function create(Request $request){

        $errCode= 0;
        $errMessage = "";
        $data = [];
        $model = $request->user();
        $bankModel = null;
        $memberId = $model->id;
        $rules = array(
            'nominal' => 'required|numeric|min:100000',
            'bank' => 'required',
            'account_name' => 'required',
            'account_number' => 'required',
        );
        $Validator = Validator::make($request->all(), $rules);
        if($Validator->fails()){
            $errCode = 1;
            $errMessage = $Validator->errors()->first();
        }
        
        if($errCode == 0) {
            $memberBalance = $model->getBalance();
            if ($request->nominal > $memberBalance) {
                $errCode = 1;
                $errMessage = "Saldo tidak cukup";
            }
        }

        if($errCode == 0){
            $cekWithdrawModel = Withdraw::where("member_id", $memberId)->where("status", PConstant::TOPUP_STATUS_PENDING)->first();
            if($cekWithdrawModel != null){
                $errCode = 1;
                $errMessage = "You already have withdraw pending, please pay or cancel and create new withdraw";
            }
        }


        if($errCode == 0){
            try {
                $totalTransfer = $request->nominal - PConstant::WITHDRAW_CHARGE;
                $withdrawModel = new Withdraw();
                $withdrawModel->code = nextWithdrawCode();
                $withdrawModel->member_id = $memberId;
                $withdrawModel->bank = $request->bank;
                $withdrawModel->account_name = $request->account_name;
                $withdrawModel->account_number = $request->account_number;
                $withdrawModel->nominal = $request->nominal;
                $withdrawModel->charge = PConstant::WITHDRAW_CHARGE;
                $withdrawModel->total = $totalTransfer;
                $withdrawModel->save();
                $member = $withdrawModel->member;
                $member->inOutBalance(0, $withdrawModel->nominal, "Penarikan saldo", "Penarikan saldo  dengan code ". $withdrawModel->code);
                $data = Withdraw::find($withdrawModel->id)->toResult();
            } catch (Exception $ex) {
                $errCode = 1;
                $errMessage = $ex->getMessage();
            }
        }

        $response = ["errCode"=> $errCode, "errMessage"=>$errMessage, "data"=> $data];
        return response()->json($response);
    }
}
