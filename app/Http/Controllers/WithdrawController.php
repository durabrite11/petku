<?php

namespace App\Http\Controllers;

use App\Models\Withdraw;
use App\Models\Member;
use App\PConstant;
use Exception;
use Illuminate\Support\Facades\DB;

class WithdrawController extends Controller
{
    private $title = "Penarikan Saldo";
    //
    public function index(){
        $modelCollection = Withdraw::with(['member'])->orderBy("id", "desc")->paginate(PConstant::PERPAGE);
        $data = [];
        $data["modelCollection"] = $modelCollection;
        $data["title"] = $this->title;
        return view("admin.withdraw.index", $data);
    }

    public function confirm($status, Withdraw $withdraw){
        $errCode= 0;
        $errMessage = "";
        $statusWithdraw = PConstant::TOPUP_STATUS_REJECTED;
        if ($status == 1) {
            $statusWithdraw = PConstant::TOPUP_STATUS_APPROVED;
        }
        
        if($errCode == 0){
            if($withdraw->status != PConstant::TOPUP_STATUS_PENDING){
                $errCode = 1;
                $errMessage = "Transaction approved/rejected";
            }
        }
        DB::beginTransaction();
        
        if($errCode == 0){
            try {
                $withdraw->status = $statusWithdraw;
                $withdraw->save();
                if ($statusWithdraw == PConstant::TOPUP_STATUS_APPROVED) {
                    /**
                     *  @var Member $member
                     * */
                    $member = $withdraw->member;
                    sendNotification($member->id, "Penarikan Saldo", "Penarikan Saldo dengan code:  ".$withdraw->code ." telah di setujui");
                }
                if ($statusWithdraw == PConstant::TOPUP_STATUS_REJECTED) {
                    /**
                     *  @var Member $member
                     * */
                    $member = $withdraw->member;
                    $member->inOutBalance($withdraw->nominal, 0, "Penarikan saldo", "Penarikan saldo  dengan code ". $withdraw->code ." telah di tolak");
                    sendNotification($member->id, "Penarikan Saldo", "Penarikan Saldo dengan code:  ".$withdraw->code ." telah di tolak");
                }
            } catch (Exception $ex) {
                $errCode = 1;
                $errMessage = $ex->getMessage();
            }
        }

        if ($errCode == 0) {
            DB::commit();
            return redirect()
                ->route('withdraw')
                ->with([
                    'success' => 'withdraw has been '. $statusWithdraw
                ]);
        } else {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with([
                    'error' => $errMessage
                ]);
        }
    }
}
