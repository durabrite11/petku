@extends('themes.admin')
@php
if(!isset($model)){
    $model = null;
}
$action = "Tambah";
$route = route('pet.store');
$method = "POST";
if($isEdit){
    $action = "Edit";
    $route = route('pet.update', ['pet' => $model->id]);
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
            <form id="pet_form" enctype="multipart/form-data" action="{{ $route }}" method="{{ $method }}">
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
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary mr-3">Simpan</button>
                    <a href="{{URL::to('admin/pet')}}" class="btn btn-danger">Batal</a>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection
