<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\ImageResource;
use App\Models\Service;
use App\PConstant;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PetController extends Controller
{
    private $title = "Hewan";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $modelCollection = Pet::orderBy('created_at', 'desc')->paginate(PConstant::PERPAGE);
        $data = [];
        $data["modelCollection"] = $modelCollection;
        $data["title"] = $this->title;
        return view("admin.pet.index", $data);
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
        return view("admin.pet.edit", $data);
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
                $model = new Pet();
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
                ->route('pet.index')
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
     * @param  \App\Models\Pet  $pet
     * @return \Illuminate\Http\Response
     */
    public function show(Pet $pet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Pet  $pet
     * @return \Illuminate\Http\Response
     */
    public function edit(Pet $pet)
    {
        $data = [];
        $data["isEdit"] = true;
        $data["model"] = $pet;
        $data["title"] = $this->title;
        return view("admin.pet.edit", $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pet  $pet
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pet $pet)
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
                $pet->name = $request->name;
                $pet->save();
            } catch (Exception $ex) {
                $errCode = 1;
                $errMessage = $ex->getMessage();
            }
        }

        if ($errCode == 0) {
            DB::commit();
            return redirect()
                ->route('pet.index')
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
     * @param  \App\Models\Pet  $pet
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pet $pet)
    {
        $serviceModel = Service::where('pet_id', $pet->id)->first();
        if($serviceModel != null){
            return redirect()
                ->back()
                ->withInput()
                ->with([
                    'error' => 'Hewan digunakan dalam layanan '
                ]);
        }

        $delete = $pet->delete();
        if ($delete) {
            return redirect()
                ->route('pet.index')
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
     * @param  \App\Models\Pet  $pet
     * @return \Illuminate\Http\Response
     */
    public function activate(Pet $pet)
    {
        if($pet->is_active){
            $pet->is_active = 0;
        } else {
            $pet->is_active = 1;
        }
        $pet->save();
        return redirect()
            ->route('pet.index')
            ->with([
                'success' => 'Active/Non Active user has been successfully'
            ]);
    }
}
