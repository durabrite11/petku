<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\TransactionChangeSchedule as ModelsTransactionChangeSchedule;
use App\PConstant;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionChangeSchedule extends Controller
{
    
    public function get(Request $request){
        $errCode= 0;
        $errMessage = "";
        $data = [];
        $userModel = $request->user();
        $requestBy = $userModel->type;
        $memberId = $userModel->id;
        if($errCode == 0){
            try {
                $transactionChangedStatusModel = ModelsTransactionChangeSchedule::selectRaw('transaction.*, transaction_change_schedule.*')->join('transaction', 'transaction.id', 'transaction_change_schedule.transaction_id'); 
                if($userModel->type =='Groomer'){
                    $transactionChangedStatusModel = $transactionChangedStatusModel->where('transaction.member_id_groomer', "=", $memberId);
                } else {
                    $transactionChangedStatusModel = $transactionChangedStatusModel->where('transaction.member_id', "=", $memberId);
                }
                if(strlen($request->id) > 0){
                    $data = $transactionChangedStatusModel->find($request->id)->apiResponse($userModel->type);
                }else{
                    $data =  $transactionChangedStatusModel->get()->map(function (ModelsTransactionChangeSchedule $item) use ($userModel){ 
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
    public function change(Request $request){
        $errCode= 0;
        $errMessage = "";
        $data = [];
        $userModel = $request->user();
        $requestBy = $userModel->type;
        $memberId = $userModel->id;
        $transactionStatus = [
            PConstant::TRANSACTION_STATUS_SUCCESS,
            PConstant::TRANSACTION_STATUS_APPROVED,
        ];
        $rules = array(
            'id'       => 'required',
            'schedule'       => 'required|date_format:Y-m-d H:i:s',
        );
        $Validator = Validator::make($request->all(), $rules);
        if($Validator->fails()){
            $errCode = 1;
            $errMessage = $Validator->errors()->first();
        }
        if ($errCode == 0) {
            $transactionModel = Transaction::where("id", $request->id)->first();
            if($transactionModel == null){
                $errCode = 1;
                $errMessage = "Transaksi ini tidak tersedia atau dihapus";
            }
        }
        if ($errCode == 0) {
            if(!in_array($transactionModel->status_transaction, $transactionStatus)){
                $errCode = 1;
                $errMessage = "Transaksi ini tidak dapat ubah jadwal";
            }
        }
        if ($errCode == 0) {
            $transactionChangedStatusModel = ModelsTransactionChangeSchedule::where("transaction_id", $request->id)
                ->where("request_by", "!=", $requestBy)
                ->where("status", "PENDING")
                ->first(); 
            if($transactionChangedStatusModel){
                $errCode = 1;
                $errMessage = "Transaksi ini dalam proses perubahan jadwal";
            }
        }
                
        DB::beginTransaction();
        if($errCode == 0){
            try {
                $transactionChangedStatusModel = ModelsTransactionChangeSchedule::where("transaction_id", $request->id)
                    ->where("request_by", $requestBy)
                    ->where("status", "PENDING")
                    ->first(); 
                if ($transactionChangedStatusModel == null) {
                    $transactionChangedStatusModel = new ModelsTransactionChangeSchedule();
                    if ($requestBy == "Member") {
                        sendNotification($transactionModel->member_id, "Transaksi", "Layanan ".$transactionModel->code." anda telah melakukan perubahan jadwal");
                    } else {
                        sendNotification($transactionModel->member_id_groomer, "Transaksi", "Layanan ".$transactionModel->code." anda telah melakukan perubahan jadwal");
                    }
                }
                $transactionChangedStatusModel->transaction_id = $transactionModel->id;
                $transactionChangedStatusModel->request_by = $requestBy;
                $transactionChangedStatusModel->schedule = $request->schedule;
                $transactionChangedStatusModel->save();
                $data = $transactionChangedStatusModel;
                DB::commit();
            } catch (Exception $ex) {
                $errCode = 1;
                $errMessage = $ex->getMessage();
                DB::rollBack();
            }
        }
        $response = ["errCode"=> $errCode, "errMessage"=>$errMessage, "data"=> $data];
        return response()->json($response);
        
    }
    
    public function confirm(Request $request){
        $errCode= 0;
        $errMessage = "";
        $data = [];
        $userModel = $request->user();
        $requestBy = $userModel->type;
        $memberId = $userModel->id;
        $transactionScheduleStatus = [
            PConstant::TRANSACTION_STATUS_REJECTED,
            PConstant::TRANSACTION_STATUS_APPROVED,
        ];
        $transactionStatus = [
            PConstant::TRANSACTION_STATUS_SUCCESS,
            PConstant::TRANSACTION_STATUS_APPROVED,
        ];
        $rules = array(
            'id'       => 'required',
            'status'       => 'required|in:'.implode(',', $transactionScheduleStatus),
        );
        $Validator = Validator::make($request->all(), $rules);
        if($Validator->fails()){
            $errCode = 1;
            $errMessage = $Validator->errors()->first();
        }
        if ($errCode == 0) {
            $transactionChangedStatusModel = ModelsTransactionChangeSchedule::where("id", $request->id)
                ->where("status", "PENDING")
                ->first(); 
            if($transactionChangedStatusModel == null){
                $errCode = 1;
                $errMessage = "Perubahan jadwal ini sudah disetujui atau sudah dihapus";
            }
        }
        
        if ($errCode == 0) {
            if($transactionChangedStatusModel->request_by == $requestBy){
                $errCode = 1;
                $errMessage = "Anda tidak dapat melakukan approve perubahan jadwal ini";
            }
        }
        
        if ($errCode == 0) {
            $transactionModel = Transaction::where("id", $transactionChangedStatusModel->transaction_id)->first();
            if($transactionModel == null){
                $errCode = 1;
                $errMessage = "Transaksi ini tidak tersedia atau dihapus";
            }
        }
        
        if ($errCode == 0) {
            if(!in_array($transactionModel->status_transaction, $transactionStatus)){
                $errCode = 1;
                $errMessage = "Transaksi ini tidak dapat ubah jadwal";
            }
        }
                
        DB::beginTransaction();
        if($errCode == 0){
            try {
                if($request->status == PConstant::TRANSACTION_STATUS_APPROVED){
                    $transactionModel->schedule = $transactionChangedStatusModel->schedule;
                    $transactionModel->save();
                }
                $transactionChangedStatusModel->status = $request->status ;
                $transactionChangedStatusModel->save();
                $data = [
                    "message" => "success"
                ];
                DB::commit();
            } catch (Exception $ex) {
                $errCode = 1;
                $errMessage = $ex->getMessage();
                DB::rollBack();
            }
        }
        $response = ["errCode"=> $errCode, "errMessage"=>$errMessage, "data"=> $data];
        return response()->json($response);
        
    }
}
