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
                            <th>Phone</th>
                            <th>Bank</th>
                            <th class="text-right">Nominal</th>
                            <th width="100">Status</th>
                            <th width="250">Action</th>
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
                            <td>{{ $model->member->phone }}</td>
                            <td>{!! $model->bank_account_name ."-". $model->bank_account_number."(".$model->bank.")"!!}</td>
                            <td class="text-right">{{ formatCurrency($model->nominal) }}<br><a target="_blank" href="{{ $model->getImage() }}">bukti transfer</a></td>
                            <td>{!! statusBadgeTopup($model->status) !!}</td>
                            <td >
                                @if($model->status == PConstant::TOPUP_STATUS_PENDING)
                                <a href='{{ URL::to("admin/topup/confirm/1/$model->id")}}' class="btn btn-warning btn-sm btn-approved">
                                    <i class="fa fa-solid fa-check"></i> APPROVED
                                </a>&nbsp;&nbsp;
                                <a href='{{ URL::to("admin/topup/confirm/2/$model->id")}}' class="btn btn-danger btn-sm btn-reject">
                                    <i class="fa fa-solid fa-ban"></i> REJECTED
                                </a>
                                @endif
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

@section('script')
    <script>
        $('.btn-approved').click(function() {
            var link = $(this).attr("href");
            var confirm = Swal.fire({
                title: 'Do you want approve this topup?',
                showCancelButton: true,
                confirmButtonText: 'Confirm',
                }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = link;
                }
            })
            return false;
        });
    </script>
@endsection