<?php

namespace App\Http\Controllers;

use App\Models\Topup;
use App\Models\Member;
use App\Models\Transaction;
use App\PConstant;
use Exception;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    private $title = "Transaksi";
    //
    public function index(){
        $modelCollection = Transaction::with(['member', 'memberGroomer'])->orderBy("id", "desc")->paginate(PConstant::PERPAGE);
        $data = [];
        $data["modelCollection"] = $modelCollection;
        $data["title"] = $this->title;
        return view("admin.transaction.index", $data);
    }

}
