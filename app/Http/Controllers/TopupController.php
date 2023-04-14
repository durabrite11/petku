<?php

namespace App\Http\Controllers;

use App\Models\Topup;
use App\Models\Member;
use App\PConstant;
use Exception;
use Illuminate\Support\Facades\DB;

class TopupController extends Controller
{
    private $title = "Topup";
    //
    public function index(){
        $modelCollection = Topup::with(['member', 'image'])->orderBy("id", "desc")->paginate(PConstant::PERPAGE);
        $data = [];
        $data["modelCollection"] = $modelCollection;
        $data["title"] = $this->title;
        return view("admin.topup.index", $data);
    }

    public function confirm($status, Topup $topup){
        $errCode= 0;
        $errMessage = "";
        $statusTopup = PConstant::TOPUP_STATUS_REJECTED;
        if ($status == 1) {
            $statusTopup = PConstant::TOPUP_STATUS_APPROVED;
        }
        
        if($errCode == 0){
            if($topup->status != PConstant::TOPUP_STATUS_PENDING){
                $errCode = 1;
                $errMessage = "Transaction approved/rejected";
            }
        }
        DB::beginTransaction();
        
        if($errCode == 0){
            try {
                $topup->status = $statusTopup;
                $topup->save();
                if ($statusTopup == PConstant::TOPUP_STATUS_APPROVED) {
                    /**
                     *  @var Member $member
                     * */
                    $member = $topup->member;
                    $member->inOutBalance($topup->nominal, 0, "Topup", "Topup  using code ". $topup->code);

                    sendNotification($member->id, "Transaksi", "Topup dengan code:  ".$topup->code ." telah di setujui");
                }
            } catch (Exception $ex) {
                $errCode = 1;
                $errMessage = $ex->getMessage();
            }
        }

        if ($errCode == 0) {
            DB::commit();
            return redirect()
                ->route('topup')
                ->with([
                    'success' => 'topup has been '. $statusTopup
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
