@extends('backend.app')

@section('content')

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Withdraw Requests</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Withdraw Requests</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>


        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Withdraw Requests</h3>
                    </div>
                    <div class="card-body">
                        <table class="example1 table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Amount</th>
                                <th>Bank Details</th>
                                <th>Requested at</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($settings as $setting)
                                <tr>
                                    <td>{{$setting['name']}}</td>
                                    <td>₦{{ number_format($setting['amount'], 2, '.', ',')}}</td>
                                    <td>
                                        Account no : {{$setting['bankdetail']['accountno']}} <br/>
                                        Account holder : {{$setting['bankdetail']['holdername']}} <br/>
                                        Bank name : {{$setting['bankdetail']['bankName']}}
                                    </td>
                                    <td>{{$setting['reqDate']}}</td>
                                    <td>
                                        @if($setting['status'] == 0)
                                            <button class="btn btn-success btn-sm"
                                                    onclick="acceptSettings(this,'{{$setting['id']}}')"><i
                                                    class="fas fa-check"></i>&nbsp;Accept
                                            </button>
                                        @elseif($setting['status'] == 1) Completed
                                        @else Cancelled
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection

@section('js')
    <script src="{{asset('js/sweetalert2.js')}}"></script>
    <script src="{{asset('backend/plugins/sweetalert2/sweetalert2.min.js')}}"></script>
    <script src="{{asset('backend/plugins/select2/js/select2.full.min.js')}}"></script>
    <script src="{{asset('backend/plugins/bootstrap-switch/js/bootstrap-switch.min.js')}}"></script>


    <script>
        $(function () {
            //Initialize Select2 Elements
            $('.select2').select2()

            //Initialize Select2 Elements
            $('.select2bs4').select2({
                theme: 'bootstrap4'
            })

            $("input[data-bootstrap-switch]").each(function () {
                $(this).bootstrapSwitch('state', $(this).prop('checked'));
            });
        });

    </script>

    //setting delete
    <script>
        function acceptSettings(r, id) {
            var list = r.parentNode.parentNode.rowIndex;
            swal({
                title: 'Are you sure you want to accept it?',
                text: "",
                type: 'warning',
                showCancelButton: true,
                cancelButtonText: 'Canel',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes,Accept'
            }).then((result) => {
                if (result.value) {
                    $.ajax
                    ({
                        type: "Post",
                        url: '{{route('withdraws')}}',
                        data: {
                            'id': id,
                            'accept': 'accept'
                        },
                        beforeSubmit: function () {
                            swal({
                                title: '<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i> <span class="sr-only">Loading...</span>',
                                text: 'Processing please wait...',
                                showConfirmButton: false
                            })
                        },
                        success: function (response) {
                            if (response.status === 'success') {
                                // document.getElementById('example1').deleteRow(list);
                                toastr.success(response.content, response.title);
                            } else {
                                toastr.error(response.content, response.title);
                            }
                        }
                    })
                } else {
                }
            })
        }

    </script>

@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('backend/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
@endsection
