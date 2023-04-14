@extends('themes.admin')
@php
if(!isset($model)){
    $model = null;
}
$action = "Tambah";
$route = route('bank.store');
$method = "POST";
$image = $model == null ? "https://dummyimage.com/200x200/ccc/&text=no+image" : $model->imageUrl();
if($isEdit){
    $action = "Edit";
    $route = route('bank.update', ['bank' => $model->id]);
}
@endphp
@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>{{$title}}</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Master Data</a></li>
                        <li class="breadcrumb-item active">{{$title}}</li>
                        <li class="breadcrumb-item active">{{$action}}</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="card ">
            <div class="card-header">
                <h3 class="card-title">{{$action}} {{$title}}</h3>
            </div>
            <form id="bank_form" enctype="multipart/form-data" action="{{ $route }}" method="{{ $method }}">
                @csrf
                @if($isEdit)
                <input type="hidden" name="_method" value="PUT">
                @endif
                <div class="card-body">
                    @include("block.error")
                    <div class="form-group">
                        <label for="name">Nama</label>
                        <input name="name" class="form-control" id="name" placeholder="Masukkan nama"
                            value="{{$model!=null?$model->name:''}}">
                    </div>
                    <div class="form-group">
                        <label for="account_name">Atas Nama Rekening</label>
                        <input name="account_name" class="form-control" id="account_name" placeholder="Masukkan Atas Nama Rekening"
                            value="{{$model!=null?$model->account_name:''}}">
                    </div>
                    <div class="form-group">
                        <label for="account_number">No Rekening</label>
                        <input name="account_number" class="form-control" id="account_number" placeholder="Masukkan No Rekening"
                            value="{{$model!=null?$model->account_number:''}}">
                    </div>
                    <div class="form-group">
                        <input type="hidden" id="image-value" name="image">
                        <label for="image_bank">image</label>
                        <input type="file" accept="image/*" class="form-control modal-cropper d-none" id="image_bank" >
                        <div class="cursor-pointer">
                            <img class="img-preview" src="{{ $image }}">
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary mr-3">Simpan</button>
                    <a href="{{URL::to('admin/bank')}}" class="btn btn-danger">Batal</a>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection

@section('script')
    @include('block.cropper', ["formId"=>"form_bank", "classPreview"=> 'img-preview',"width"=>390, "height"=>"310", "fieldName"=>"image_bank"])
@endsection