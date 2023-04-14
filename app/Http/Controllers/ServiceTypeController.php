<?php

namespace App\Http\Controllers;

use App\Models\ServiceType;
use App\Models\ImageResource;
use App\PConstant;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServiceTypeController extends Controller
{
    private $title = "Jenis Layanan";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $modelCollection = ServiceType::orderBy('created_at', 'desc')->paginate(PConstant::PERPAGE);
        $data = [];
        $data["modelCollection"] = $modelCollection;
        $data["title"] = $this->title;
        return view("admin.serviceType.index", $data);
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
        return view("admin.serviceType.edit", $data);
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
        );
        $request->validate($rules);

        DB::beginTransaction();
        if($errCode == 0){
            try {
                $model = new ServiceType();
                $model->name = $request->name;
                $model->save();
            } catch (Exception $ex) {
                $errCode = 1;
                $errMessage = $ex->getMessage();
            }
        }

        if ($errCode == 0) {
            DB::commit();
            return redirect()
                ->route('serviceType.index')
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
     * @param  \App\Models\ServiceType  $serviceType
     * @return \Illuminate\Http\Response
     */
    public function show(ServiceType $serviceType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ServiceType  $serviceType
     * @return \Illuminate\Http\Response
     */
    public function edit(ServiceType $serviceType)
    {
        $data = [];
        $data["isEdit"] = true;
        $data["model"] = $serviceType;
        $data["title"] = $this->title;
        return view("admin.serviceType.edit", $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ServiceType  $serviceType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ServiceType $serviceType)
    {
        $errCode= 0;
        $errMessage = "";

        $rules = array(
            'name'       => 'required',
        );

        $request->validate($rules);
        DB::beginTransaction();
        if($errCode == 0){
            try {
                $serviceType->name = $request->name;
                $serviceType->save();
            } catch (Exception $ex) {
                $errCode = 1;
                $errMessage = $ex->getMessage();
            }
        }

        if ($errCode == 0) {
            DB::commit();
            return redirect()
                ->route('serviceType.index')
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
     * @param  \App\Models\ServiceType  $serviceType
     * @return \Illuminate\Http\Response
     */
    public function destroy(ServiceType $serviceType)
    {
        $delete = $serviceType->delete();
        $serviceTypeImage = $serviceType->image;
        if ($serviceTypeImage) {
            $deletImage = $serviceTypeImage->delete();
        }
        if ($delete) {
            return redirect()
                ->route('serviceType.index')
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
     * @param  \App\Models\ServiceType  $serviceType
     * @return \Illuminate\Http\Response
     */
    public function activate(ServiceType $serviceType)
    {
        if($serviceType->is_active){
            $serviceType->is_active = 0;
        } else {
            $serviceType->is_active = 1;
        }
        $serviceType->save();
        return redirect()
            ->route('serviceType.index')
            ->with([
                'success' => 'Active/Non Active user has been successfully'
            ]);
    }
}
