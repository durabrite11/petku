@extends('themes.admin')
@php
if(!isset($model)){
$model = null;
}
$action = "Tambah";
$route = route('users.store');
$method = "POST";
if($isEdit){
    $action = "Edit";
    $route = route('users.update', ['user' => $model->id]);
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
                <h3 class="card-title">{{$action}} User</h3>
            </div>
            <form action="{{ $route }}" method="{{ $method }}">
                @csrf
                <div class="card-body">
                    @if(count($errors->all())>0)
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        Please fix
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    <div class="form-group">
                        <label for="name">Nama</label>
                        <input name="name" class="form-control" id="name" placeholder="Masukkan nama"
                            value="{{$model!=null?$model->name:''}}">
                    </div>
                    @if($isEdit)
                    <input type="hidden" name="_method" value="PUT">
                    <div class="form-group">
                        <label for="password">Password Lama</label>
                        <input name="old_password" class="form-control" id="password" placeholder="Masukkan password lama">
                    </div>
                    @else
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input name="username" class="form-control" id="username" placeholder="Masukkan username"
                            value="{{$model!=null?$model->email:''}}">
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input name="email" class="form-control" id="email" placeholder="Masukkan email"
                            value="{{$model!=null?$model->email:''}}">
                    </div>
                    @endif
                    <div class="form-group">
                        <label for="password">Password Baru</label>
                        <input name="password" class="form-control" id="password" placeholder="Masukkan password baru"
                            value="{{$model!=null?$model->name:''}}">
                    </div>
                    <div class="form-group">
                        <label for="confirmpassword">Konfirmasi Password</label>
                        <input name="password_confirmation" class="form-control" id="password_confirmation"
                            placeholder="Masukkan konfirmasi password">
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary mr-3">Simpan</button>
                    <a href="{{URL::to('admin/users')}}" class="btn btn-danger">Batal</a>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection