<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\ImageResource;
use App\PConstant;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BankController extends Controller
{
    private $title = "Bank";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $modelCollection = Bank::paginate(PConstant::PERPAGE);
        $data = [];
        $data["modelCollection"] = $modelCollection;
        $data["title"] = $this->title;
        return view("admin.bank.index", $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [];
        $data["isEdit"] = false;
        $data["title"] = $this->title;
        return view("admin.bank.edit", $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $errCode= 0;
        $errMessage = "";

        $rules = array(
            'name'       => 'required',
            'account_name'      => 'required',
            'account_number'      => 'required',
            'image_bank'      => 'required',
        );
        $request->validate($rules);

        DB::beginTransaction();
        if($errCode == 0){
            try {
                $model = new Bank();
                $model->name = $request->name;
                $model->account_name = $request->account_name;
                $model->account_number = $request->account_number;
                $model->save();
                $imageResource = new ImageResource();
                $file = $imageResource->saveTemp($request, "image_bank");
                $imageResource->saveImage($file, $model);
            } catch (Exception $ex) {
                $errCode = 1;
                $errMessage = $ex->getMessage();
            }
        }

        if ($errCode == 0) {
            DB::commit();
            return redirect()
                ->route('bank.index')
                ->with([
                    'success' => 'New '.$this->title.' has been created successfully'
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

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function show(Bank $bank)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function edit(Bank $bank)
    {
        $data = [];
        $data["isEdit"] = true;
        $data["model"] = $bank;
        $data["title"] = $this->title;
        return view("admin.bank.edit", $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Bank $bank)
    {
        $errCode= 0;
        $errMessage = "";

        $rules = array(
            'name'       => 'required',
            'account_name'      => 'required',
            'account_number'      => 'required',
        );

        $request->validate($rules);
        DB::beginTransaction();
        if($errCode == 0){
            try {
                $bank->name = $request->name;
                $bank->account_name = $request->account_name;
                $bank->account_number = $request->account_number;
                $bank->save();
                if ($request->has("image_bank")) {
                    $bankImage = $bank->image;
                    if ($bankImage) {
                        $bankImage->delete();
                    }
                    $imageResource = new ImageResource();
                    $file = $imageResource->saveTemp($request, "image_bank");
                    $imageResource->saveImage($file, $bank);
                }
            } catch (Exception $ex) {
                $errCode = 1;
                $errMessage = $ex->getMessage();
            }
        }

        if ($errCode == 0) {
            DB::commit();
            return redirect()
                ->route('bank.index')
                ->with([
                    'success' => 'New '.$this->title.' has been created successfully'
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function destroy(Bank $bank)
    {
        $delete = $bank->delete();
        $bankImage = $bank->image;
        if ($bankImage) {
            $deletImage = $bankImage->delete();
        }
        if ($delete) {
            return redirect()
                ->route('bank.index')
                ->with([
                    'success' => 'Delete user has been successfully'
                ]);
        } else {
            return redirect()
                ->back()
                ->withInput()
                ->with([
                    'error' => 'Some problem occurred, please try again'
                ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Bank  $bank
     * @return \Illuminate\Http\Response
     */
    public function activate(Bank $bank)
    {
        if($bank->is_active){
            $bank->is_active = 0;
        } else {
            $bank->is_active = 1;
        }
        $bank->save();
        return redirect()
            ->route('bank.index')
            ->with([
                'success' => 'Active/Non Active user has been successfully'
            ]);
    }
}
