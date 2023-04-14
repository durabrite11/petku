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
                            <th>Code</th>
                            <th>Name</th>
                            <th>Groomer</th>
                            <th class="text-right">Nominal</th>
                            <th width="100">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $no = 1;
                        @endphp
                        @foreach ($modelCollection as $model)
                        <tr>
                            <td>{{ $no++ }}</td>
                            <td>{{ $model->code }}</td>
                            <td>{{ $model->member->name }}</td>
                            <td>{{ $model->memberGroomer->name }}</td>
                            <td class="text-right">{{ formatCurrency($model->total_price) }}</td>
                            <td>{!! statusBadgeTransaction($model->status_transaction) !!}</td>
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
