<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\TransactionChangeSchedule;
use App\Models\TransactionDetail;
use App\PConstant;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller
{
    //
    function create(Request $request){
        $errCode= 0;
        $errMessage = "";
        $data = [];
        /* var $model Member */
        $model = $request->user();
        $memberLocation = $model->memberLocation;
        $rules = array(
            'service_id'       => 'required',
            'type'       => 'required|in:Groomer,Home',
            'schedule'       => 'required|date_format:Y-m-d H:i:s|after:5 hours',
        );
        $priceDistance = 10000;
        
        $Validator = Validator::make($request->all(), $rules);
        if($Validator->fails()){
            $errCode = 1;
            $errMessage = $Validator->errors()->first();
        }
        if ($errCode == 0) {
            $serviceModel = Service::where("id", $request->service_id)->first();
            if($serviceModel == null){
                $errCode = 1;
                $errMessage = "Layanan ini tidak tersedia atau dihapus";
            }
        }
                
        if ($errCode == 0) {
            $groomerModel = Member::where("id", $serviceModel->member_id)
                ->where("type", "Groomer")
                ->first();
            if($groomerModel == null){
                $errCode = 1;
                $errMessage = "Groomer ini tidak tersedia atau dihapus";
            }
        }
        if ($errCode == 0) {
            $queryDistance = DB::table('member_locations')
                    ->selectRaw("DEGREES(ACOS(LEAST(1.0, COS(RADIANS(latitude))
                            * COS(RADIANS(".$memberLocation->latitude."))
                            * COS(RADIANS(longitude - ".$memberLocation->longitude."))
                            + SIN(RADIANS(latitude))
                            * SIN(RADIANS(".$memberLocation->latitude."))))) AS distance_in_km ")
                    ->where("member_id", $groomerModel->id)
                    ->orderBy("distance_in_km", "asc")->first();
            if($queryDistance == null){
                $errCode = 1;
                $errMessage = "Lokasi groomer layanan tidak ditemukan";
            }
        }
        if($errCode == 0){
            $distance = ceil($queryDistance->distance_in_km);
            $priceDistance = max($priceDistance, $distance * PConstant::CHARGE_PER_KM);
            if($request->type == "Groomer"){
                $priceDistance = 0;
            }
            $totalPrice = $serviceModel->price + $priceDistance;
        }
        if($errCode == 0) {
            $memberBalance = $model->getBalance();
            if ($totalPrice > $memberBalance) {
                $errCode = 1;
                $errMessage = "Saldo tidak cukup";
            }
        }
        DB::beginTransaction();
        if($errCode == 0){
            try {
                $groomerLocation = $groomerModel->memberLocation;
                $transactionModel = new Transaction();
                $transactionModel->code = nextTransactionCode();
                $transactionModel->member_id = $model->id;
                $transactionModel->member_name = $model->name;
                $transactionModel->member_address = $memberLocation->address;
                $transactionModel->member_latitude = $memberLocation->latitude;
                $transactionModel->member_longitude = $memberLocation->latitude;
                
                $transactionModel->member_id_groomer = $groomerModel->id;
                $transactionModel->groomer_name = $groomerModel->name;
                $transactionModel->groomer_address = $groomerLocation->address;
                $transactionModel->groomer_latitude = $groomerLocation->latitude;
                $transactionModel->groomer_longitude = $groomerLocation->latitude;
                $transactionModel->distance = $distance;
                $transactionModel->price_per_km = PConstant::CHARGE_PER_KM;
                $transactionModel->service_price = $serviceModel->price;
                $transactionModel->delivery_price = $priceDistance;
                $transactionModel->total_price = $totalPrice;
                $transactionModel->schedule = $request->schedule;
                $transactionModel->type = $request->type;
                $transactionModel->transaction_date = now();
                $transactionModel->expired_date = date_add(now(), date_interval_create_from_date_string("1 day"));
                $transaction = $transactionModel->save();
                if ($transaction) {
                    $transactionDetailModel = new TransactionDetail();
                    $transactionDetailModel->transaction_id = $transactionModel->id;
                    $transactionDetailModel->service_id = $serviceModel->id;
                    $transactionDetailModel->price = $serviceModel->price;
                    $transactionDetailModel->notes = $request->notes;
                    $transactionDetailModel->save();
                    $model->inOutBalance(0, $totalPrice, "Transaction", "Transaction with code ".$transactionModel->code);
                    $transactionModel->status_transaction = "SUCCESS";
                    $transactionModel->save();
                }
                sendNotification($groomerModel->id, "Transaksi", "Anda Mendapatkan transaksi baru dengan code:  ".$transactionModel->code);
                $data = $transactionModel->apiResponse();
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
    
    public function get(Request $request){
        $errCode= 0;
        $errMessage = "";
        $data = [];
        $userModel = $request->user();
        $memberId = $userModel->id;
        if($errCode == 0){
            try {
                $model = new Transaction();
                if(strlen($request->id) > 0){
                    $data = $model->find($request->id)->apiResponse();
                }else{
                    $model =  $model::with(['transactionDetail', 'member', 'memberGroomer', 'transactionDetail.service']);
                    if($userModel->type =='Groomer'){
                        $model = $model->where('member_id_groomer', "=", $memberId);
                    } else {
                        $model = $model->where('member_id', "=", $memberId);
                    }
                    $data = $model->orderBy('transaction_date', 'desc')
                        ->get()->map(function (Transaction $item){
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
    
    public function confirm(Request $request){
        $errCode= 0;
        $errMessage = "";
        $data = [];
        $userModel = $request->user();
        $memberId = $userModel->id;
        $transactionStatus = [
            PConstant::TRANSACTION_STATUS_APPROVED,
            PConstant::TRANSACTION_STATUS_ONTHEWAY,
            PConstant::TRANSACTION_STATUS_PROSES,
            PConstant::TRANSACTION_STATUS_COMPLETED,
            PConstant::TRANSACTION_STATUS_REJECTED,
            PConstant::TRANSACTION_STATUS_CANCELED,
        ];
        $currentTransactionStatus = PConstant::TRANSACTION_STATUS_SUCCESS ;
        $transactionStatusNext = [
            PConstant::TRANSACTION_STATUS_SUCCESS => [PConstant::TRANSACTION_STATUS_APPROVED, PConstant::TRANSACTION_STATUS_REJECTED, PConstant::TRANSACTION_STATUS_CANCELED],
            PConstant::TRANSACTION_STATUS_APPROVED => [PConstant::TRANSACTION_STATUS_ONTHEWAY, PConstant::TRANSACTION_STATUS_CANCELED],
            PConstant::TRANSACTION_STATUS_ONTHEWAY => [PConstant::TRANSACTION_STATUS_PROSES],
            PConstant::TRANSACTION_STATUS_PROSES => [PConstant::TRANSACTION_STATUS_COMPLETED]
        ];
        $rules = array(
            'id'       => 'required',
            'status'       => 'in:'.implode(',', $transactionStatus),
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
            $currentTransactionStatus = $transactionModel->status_transaction;
            if(!key_exists($currentTransactionStatus, $transactionStatusNext)){
                $errCode = 1;
                $errMessage = "Transaksi ini tidak dapat melanjutkan status ".$currentTransactionStatus;
            }
        }
        if ($errCode == 0) {
            $nextTransactionStatus = $transactionStatusNext[$currentTransactionStatus];
            if(!in_array($request->status, $nextTransactionStatus)){
                $errCode = 1;
                $errMessage = "Transaksi ini hanya dapat menuju status ".implode(',', $nextTransactionStatus);
            }
        }
                
        DB::beginTransaction();
        if($errCode == 0){
            try {
                $transactionModel->status_transaction = $request->status;
                $transactionModel->save();

                if($request->status == PConstant::TRANSACTION_STATUS_APPROVED){ 
                    $memberModel = $transactionModel->member;
                    sendNotification($transactionModel->member_id, "Transaksi", "Layanan ".$transactionModel->code." anda telah disetujui");
                }
                if($request->status == PConstant::TRANSACTION_STATUS_ONTHEWAY){ 
                    $memberModel = $transactionModel->member;
                    sendNotification($transactionModel->member_id, "Transaksi", "Layanan ".$transactionModel->code." anda dalam perjalanan");
                }
                if($request->status == PConstant::TRANSACTION_STATUS_PROSES){ 
                    $memberModel = $transactionModel->member;
                    sendNotification($transactionModel->member_id, "Transaksi", "Layanan ".$transactionModel->code." anda dalam proses");
                }
                if($request->status == PConstant::TRANSACTION_STATUS_COMPLETED){
                    $memberGroomer = $transactionModel->memberGroomer;
                    $memberGroomer->inOutBalance($transactionModel->total_price, 0, "Transaction", "Transaction completed with code ".$transactionModel->code);
                    sendNotification($transactionModel->member_id, "Transaksi", "Layanan ".$transactionModel->code." anda telah selesai");
                }
                if($request->status == PConstant::TRANSACTION_STATUS_REJECTED){ 
                    $memberModel = $transactionModel->member;
                    $memberModel->inOutBalance($transactionModel->total_price, 0, "Transaction", "transaksi dengan code ".$transactionModel->code." anda ditolak");
                    sendNotification($transactionModel->member_id, "Transaksi", "Layanan ".$transactionModel->code." anda telah ditolak");
                }
                if($request->status == PConstant::TRANSACTION_STATUS_CANCELED){ 
                    $memberModel = $transactionModel->member;
                    $memberModel->inOutBalance($transactionModel->total_price, 0, "Transaction", "transaksi dengan code ".$transactionModel->code." anda dibatalkan");
                    sendNotification($transactionModel->member_id_groomer, "Transaksi", "Layanan ".$transactionModel->code." anda telah dibatalkan oleh member");
                }
                $data =[
                    "message" => "Success",
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
