@extends('themes.admin')
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
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Default box -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Data {{$title}}</h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-auto">
                        <a href="banner/create" class="btn btn-primary btn-sm mr-3"><i class="fa fa-plus"></i> Tambah</a>
                        <a href="" class="btn btn-default btn-sm"><i class="fas fa-sync-alt"></i> Refresh</a>
                    </div>
                    <div class="col">
                    </div>
                </div>
                @include("block.error")
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Image</th>
                            <th>Status</th>
                            <th width="170">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $no = 1;
                        @endphp
                        @foreach ($modelCollection as $model)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td><img src="{{ $model->imageUrl() }}" width="200px" alt=""></td>
                            <td>{!! $model->is_active ? "<span class='badge badge-info'>Active</span>":"<span class='badge badge-warning'>Non Active</span>" !!}</td>
                            <td>
                                <form action="{{ route('banner.destroy', $model->id) }}" method="post">
                                    @method("delete")
                                    @csrf
                                    <a href='{{ URL::to("admin/banner/$model->id/activate")}}' class="btn btn-default btn-sm mb-2"><i
                                            class="fa fa-solid fa-check"></i>
                                        Aktif/Non Aktif</a><br>
                                    <a href='{{ URL::to("admin/banner/$model->id/edit")}}' class="btn btn-warning btn-sm"><i
                                            class="fa fa-solid fa-pencil-alt"></i>
                                        Edit</a>
                                    <button  class="btn btn-danger btn-sm submitdelete"><i class="fa fa-solid fa-trash"></i>
                                        Delete</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer clearfix">
                {{ $modelCollection->links() }}
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
@endsection
