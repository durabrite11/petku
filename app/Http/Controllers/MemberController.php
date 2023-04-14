<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\ImageResource;
use App\PConstant;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{
    private $title = "Member";
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $modelCollection = Member::paginate(PConstant::PERPAGE);
        $data = [];
        $data["modelCollection"] = $modelCollection;
        $data["title"] = $this->title;
        return view("admin.member.index", $data);
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
        return view("admin.member.edit", $data);
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
            'image_member'      => 'required',
        );
        $request->validate($rules);

        DB::beginTransaction();
        if($errCode == 0){
            try {
                $model = new Member();
                $model->name = $request->name;
                $model->account_name = $request->account_name;
                $model->account_number = $request->account_number;
                $model->save();
                $imageResource = new ImageResource();
                $file = $imageResource->saveTemp($request, "image_member");
                $imageResource->saveImage($file, $model);
            } catch (Exception $ex) {
                $errCode = 1;
                $errMessage = $ex->getMessage();
            }
        }

        if ($errCode == 0) {
            DB::commit();
            return redirect()
                ->route('member.index')
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
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Http\Response
     */
    public function show(Member $member)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Http\Response
     */
    public function edit(Member $member)
    {
        $data = [];
        $data["isEdit"] = true;
        $data["model"] = $member;
        $data["title"] = $this->title;
        return view("admin.member.edit", $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Member $member)
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
                $member->name = $request->name;
                $member->account_name = $request->account_name;
                $member->account_number = $request->account_number;
                $member->save();
                if ($request->has("image_member")) {
                    $memberImage = $member->image;
                    if ($memberImage) {
                        $memberImage->delete();
                    }
                    $imageResource = new ImageResource();
                    $file = $imageResource->saveTemp($request, "image_member");
                    $imageResource->saveImage($file, $member);
                }
            } catch (Exception $ex) {
                $errCode = 1;
                $errMessage = $ex->getMessage();
            }
        }

        if ($errCode == 0) {
            DB::commit();
            return redirect()
                ->route('member.index')
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
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Http\Response
     */
    public function destroy(Member $member)
    {
        $delete = $member->delete();
        $memberImage = $member->image;
        if ($memberImage) {
            $deletImage = $memberImage->delete();
        }
        if ($delete) {
            return redirect()
                ->route('member.index')
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
     * @param  \App\Models\Member  $member
     * @return \Illuminate\Http\Response
     */
    public function activate(Member $member)
    {
        if($member->is_active){
            $member->is_active = 0;
        } else {
            $member->is_active = 1;
        }
        $member->save();
        return redirect()
            ->route('member.index')
            ->with([
                'success' => 'Active/Non Active user has been successfully'
            ]);
    }
}
