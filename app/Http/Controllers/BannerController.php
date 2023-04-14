<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\ImageResource;
use App\PConstant;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BannerController extends Controller
{
    private $title = "Banner";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $modelCollection = Banner::paginate(PConstant::PERPAGE);
        $data = [];
        $data["modelCollection"] = $modelCollection;
        $data["title"] = $this->title;
        return view("admin.banner.index", $data);
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
        return view("admin.banner.edit", $data);
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
            'image_banner'      => 'required',
        );
        $request->validate($rules);

        DB::beginTransaction();
        if($errCode == 0){
            try {
                $model = new Banner();
                $model->save();
                $imageResource = new ImageResource();
                $file = $imageResource->saveTemp($request, "image_banner");
                $imageResource->saveImage($file, $model);
            } catch (Exception $ex) {
                $errCode = 1;
                $errMessage = $ex->getMessage();
            }
        }

        if ($errCode == 0) {
            DB::commit();
            return redirect()
                ->route('banner.index')
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
     * @param  \App\Models\Banner  $banner
     * @return \Illuminate\Http\Response
     */
    public function show(Banner $banner)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Banner  $banner
     * @return \Illuminate\Http\Response
     */
    public function edit(Banner $banner)
    {
        $data = [];
        $data["isEdit"] = true;
        $data["model"] = $banner;
        $data["title"] = $this->title;
        return view("admin.banner.edit", $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Banner  $banner
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Banner $banner)
    {

        $errCode= 0;
        $errMessage = "";

        $rules = array(
            'image_banner'      => 'required',
        );

        $request->validate($rules);
        DB::beginTransaction();
        if($errCode == 0){
            try {
                $banner->name = $request->name;
                $banner->account_name = $request->account_name;
                $banner->account_number = $request->account_number;
                $banner->save();
                if ($request->has("image_banner")) {
                    $bannerImage = $banner->image;
                    if ($bannerImage) {
                        $bannerImage->delete();
                    }
                    $imageResource = new ImageResource();
                    $file = $imageResource->saveTemp($request, "image_banner");
                    $imageResource->saveImage($file, $banner);
                }
            } catch (Exception $ex) {
                $errCode = 1;
                $errMessage = $ex->getMessage();
            }
        }

        if ($errCode == 0) {
            DB::commit();
            return redirect()
                ->route('banner.index')
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
     * @param  \App\Models\Banner  $banner
     * @return \Illuminate\Http\Response
     */
    public function destroy(Banner $banner)
    {
        $delete = $banner->delete();
        $bannerImage = $banner->image;
        if ($bannerImage) {
            $deletImage = $bannerImage->delete();
        }
        if ($delete) {
            return redirect()
                ->route('banner.index')
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
     * @param  \App\Models\Banner  $banner
     * @return \Illuminate\Http\Response
     */
    public function activate(Banner $banner)
    {
        if($banner->is_active){
            $banner->is_active = 0;
        } else {
            $banner->is_active = 1;
        }
        $banner->save();
        return redirect()
            ->route('banner.index')
            ->with([
                'success' => 'Active/Non Active user has been successfully'
            ]);
    }
}
