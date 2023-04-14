<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\Request;

class SummaryController extends Controller
{
    public function dashboard(Request $request){
        $errCode= 0;
        $errMessage = "";
        $memberModel = $request->user();
        $data = [];
        try {
            //dapatkan total transaksi
            $sumTotalTransaction = Transaction::selectRaw("count(id) as total_transaksi, sum(total_price) total_income")
            ->where("member_id_groomer", $memberModel->id)
            ->where("status_transaction", "COMPLETED")
                ->first();
            
            $countTotalTransaction = Transaction::selectRaw("count(id) as total_transaksi")
            ->where("member_id_groomer", $memberModel->id)
                ->first();
            
            //dapatkan total transaksi berdasarkan status
            $transaction = Transaction::selectRaw("count(id) as total_transaksi, status_transaction")->where("member_id_groomer", $memberModel->id)
                ->groupBy("status_transaction")
                ->get();
            $totalTransaction = [];
            foreach ($transaction as $row){
                $dataTemp = [];
                $dataTemp["status"] = $row["status_transaction"];
                $dataTemp["total"] = $row["total_transaksi"];
                $totalTransaction[] = $dataTemp;
            } 
            $dataInterval = [];
            for ($i=0; $i<7; $i++) {
                $now = date("Y-m-d");
                $newDate = date('Y-m-d', strtotime($now. " - $i days"));
                $transactionSuccessModel = Transaction::selectRaw("count(id) as total_transaksi")
                    ->whereRaw("date(transaction_date) = '$newDate'")
                    ->where("member_id_groomer", $memberModel->id)
                    ->where("status_transaction", "SUCCESS")
                    ->first();
                
                $transactionCompleted = Transaction::selectRaw("count(id) as total_transaksi")
                ->whereRaw("date(transaction_date) = '$newDate'")
                ->where("member_id_groomer", $memberModel->id)
                ->where("status_transaction", "COMPLETED")
                ->first();
                    
                $transactionrRejectModel = Transaction::selectRaw("count(id) as total_transaksi")
                ->whereRaw("date(transaction_date) = '$newDate'")
                ->where("member_id_groomer", $memberModel->id)
                ->where("status_transaction", "REJECTED")
                ->first();
                $dataTemp = [];
                $dataTemp["success"] = $transactionSuccessModel["total_transaksi"]??0;
                $dataTemp["reject"] = $transactionrRejectModel["total_transaksi"]??0;
                $dataTemp["completed"] = $transactionCompleted["total_transaksi"]??0;
                $dataTemp["date"] = $newDate;
                $dataInterval[] = $dataTemp;
            }
                
            $data["totalTransaction"] = $countTotalTransaction["total_transaksi"];
            $data["totalIncome"] = $sumTotalTransaction["total_income"];
            $data["totalTransactionByStatus"] = $totalTransaction;
            $data["lineData"] =$dataInterval;
        } catch (Exception $ex) {
            $errCode = 1;
            $errMessage = $ex->getMessage();
        }
        
        
        $response = ["errCode"=> $errCode, "errMessage"=>$errMessage, "data"=> $data];
        return response()->json($response);
    }
    
    public function report(Request $request){
        $errCode= 0;
        $errMessage = "";
        $memberModel = $request->user();
        $dateStart = $request->dateStart;
        $dateEnd = $request->dateEnd;
        $data = [];
        try {
            //dapatkan total transaksi
            $sumTotalTransaction = Transaction::selectRaw("count(id) as total_transaksi, sum(total_price) total_income")
            ->where("member_id_groomer", $memberModel->id)
            ->whereBetween("created_at", [$dateStart, $dateEnd])
            ->where("status_transaction", "COMPLETED")
                ->first();
            
            $countTotalTransaction = Transaction::selectRaw("count(id) as total_transaksi")
            ->whereBetween("created_at", [$dateStart, $dateEnd])
            ->where("member_id_groomer", $memberModel->id)
                ->first();
            
            //dapatkan total transaksi berdasarkan status
            $transaction = Transaction::selectRaw("count(id) as total_transaksi, status_transaction")
            ->whereBetween("created_at", [$dateStart, $dateEnd])
                ->where("member_id_groomer", $memberModel->id)
                ->groupBy("status_transaction")
                ->get();
            $totalTransaction = [];
            foreach ($transaction as $row){
                $dataTemp = [];
                $dataTemp["status"] = $row["status_transaction"];
                $dataTemp["total"] = $row["total_transaksi"];
                $totalTransaction[] = $dataTemp;
            } 
            $data["totalTransaction"] = $countTotalTransaction["total_transaksi"];
            $data["totalIncome"] = $sumTotalTransaction["total_income"];
            $data["totalTransactionByStatus"] = $totalTransaction;
        } catch (Exception $ex) {
            $errCode = 1;
            $errMessage = $ex->getMessage();
        }
        
        
        $response = ["errCode"=> $errCode, "errMessage"=>$errMessage, "data"=> $data];
        return response()->json($response);
    }
}
