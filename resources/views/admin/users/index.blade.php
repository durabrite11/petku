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
                <h3 class="card-title">Data Users</h3>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-auto">
                        <a href="users/create" class="btn btn-primary btn-sm mr-3"><i class="fa fa-plus"></i> Tambah</a>
                        <a href="" class="btn btn-default btn-sm"><i class="fas fa-sync-alt"></i> Refresh</a>
                    </div>
                    <div class="col">
                    </div>
                </div>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th width="170px">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $no = 1;
                        @endphp
                        @foreach ($usersCollection as $user)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <form action="{{ route('users.destroy', $user->id) }}" method="post">
                                    @method("delete")
                                    @csrf
                                    <a href='{{ URL::to("admin/users/$user->id/edit")}}' class="btn btn-warning btn-sm"><i
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
                {{ $usersCollection->links() }}
            </div>
        </div>
    </section>
    <!-- /.content -->
</div>
@endsection