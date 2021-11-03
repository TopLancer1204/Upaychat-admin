@extends('backend.app')

@section('content')

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark" style="display: inline">Identity Info</h1>
                        &nbsp;
                        @if ($data->status == 0)
                            <button class="btn btn-warning btn-sm">Submitted</button>
                        @elseif($data->status == 1)
                            <button class="btn btn-secondary btn-sm">Reviewing</button>
                        @elseif($data->status == 2)
                            <button class="btn btn-success btn-sm">Verified</button>
                        @elseif($data->status == 3)
                            <button class="btn btn-danger btn-sm">Rejected</button>
                        @endif
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('identity.index')}}">Home</a></li>
                            <li class="breadcrumb-item active">identity</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card card-primary">
                    <div class="card-header">
                        <h2 class="card-title">Identity Info </h2>
                    </div>
                    <div class="card-body row">
                        <div class="col-md-4" style="font-size: 20px">
                            <h4>Address</h4><hr>
                            <p>Street: {{@$data->street}}</p>
                            <p>City: {{@$data->city}}</p>
                            <p>State: {{@$data->state}}</p>
                            <p>Zip Code: {{@$data->zipcode}}</p>
                            <p>Country: {{@$data->country}}</p>
                            <p>Status: {{@$data->status}}</p>
                            <p>Result: {{@$data->result}}</p>
                        </div>
                        <div class="col-md-8 row">
                            <div class="col-md-6">
                                <h4>Goverment</h4><hr>
                                <div class="scroll">
                                        @foreach ($data->metadata()->where('type', 0)->get() as $item)
                                        <a href="#" class="pop">
                                            <img src="{{$item->path}}" alt="" srcset="" style="width: 120px; height:120px; object-fit:cover; display:inline">
                                        </a>
                                        @endforeach
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h4>Proof</h4><hr>
                                <div class="scroll">
                                    @foreach ($data->metadata()->where('type', 1)->get() as $item)
                                        <a href="#" class="pop">
                                            <img src="{{$item->path}}" alt="" srcset="" style="width: 120px; height:120px; object-fit:cover">
                                        </a>
                                    @endforeach
                                </div>
                                </div>
                            <div class="col-md-12">
                                <br>
                                <h4>ID Card</h4><hr>
                                <div class="scroll">
                                    @foreach ($data->metadata()->where('type', 2)->get() as $item)
                                        <a href="#" class="pop">
                                            <img src="{{$item->path}}" alt="" srcset="" style="width: 120px; height:120px; object-fit:cover">
                                        </a>
                                    @endforeach
                                </div>
                                <h5 style="color: red">Verify Code: {{$data->verify_code}}</h5>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div style="float: right">
                            @if ($data->status == 0 || $data->status == 3)
                                <button class="btn btn-primary btn-lg" onclick="updateStatus({{$data->id}}, 1, 'Reviewing')">Review</button>
                            @elseif($data->status == 1)
                                <button class="btn btn-success btn-lg" onclick="updateStatus({{$data->id}}, 2, 'Verified')">Accept</button>
                                <button class="btn btn-danger btn-lg" onclick="rejectState({{$data->id}})">Reject</button>
                            @elseif($data->status == 2)
                                <button class="btn btn-warning btn-lg" onclick="rejectState({{$data->id}})">Reject</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">              
            <div class="modal-body">
              <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
              <img src="" class="imagepreview" style="width: 100%;" >
            </div>
          </div>
        </div>
      </div>
      <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
          <div class="modal-dialog">
            <div class="modal-content">              
                <div class="modal-header">
                    <h3>Identity Reject reason</h3>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                </div>
                <div class="modal-body">
                    <textarea class="form-control" name="reject_reason" id="reject_reason" rows="10" placeholder="Please input the reason of reject identity verification."></textarea>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button class="btn btn-warning" onclick="confirmReject()">Reject</button>
                </div>
            </div>
          </div>
        </div>
@endsection

@section('js')

    <script src="{{asset('backend/plugins/select2/js/select2.full.min.js')}}"></script>
    <script src="{{asset('backend/plugins/bootstrap-switch/js/bootstrap-switch.min.js')}}"></script>
    <script src="{{asset('backend/plugins/bs-custom-file-input/bs-custom-file-input.min.js')}}"></script>

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

    <script type="text/javascript">
        $(document).ready(function () {
            bsCustomFileInput.init();
        });
    </script>

    <script>
        $("#coverImage").change(function () {
            var input = this;
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#coverImageShow').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        });
    </script>

    <script>

        $("#slidersButton").click(function () {

            var url = "{{route("slider-add")}}";
            var form = new FormData($("form")[0]);

            $.ajax({
                type: "POST",
                url: url,
                data: form,
                processData: false,
                contentType: false,

                success: function (response) {
                    if (response.status == "success") {
                        toastr.success(response.content, response.title);
                    } else {
                        toastr.error(response.content, response.title);
                    }
                },
                error: function () {

                }
            });
        })
    </script>
    <script>
        $('.pop').on('click', function() {
            $('.imagepreview').attr('src', $(this).find('img').attr('src'));
            $('#imagemodal').modal('show');   
        });
        var rejectid = "";
        const rejectState = (id) => {
            rejectid = id;
            $("#reject_reason").val("")
            $("#rejectModal").modal('show');
        }
        const confirmReject = () => {
            $("#rejectModal").modal('hide');
            updateStatus(rejectid, 3, 'Rejected');
        }
        const updateStatus = (id, status, txt_status) => {
            var reason = $("#reject_reason").val();
            $.ajax
            ({
                type: "put",
                url: `${id}`,
                data: {status, reason},
                success: function (response) {
                    console.log(response);
                    if (response.status == 'success') {
                        toastr.success(`${response.content} ${txt_status}`, response.title);
                    } else {
                        toastr.error(response.content, response.title);
                    }
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                }
            })
        }
    </script>
@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('backend/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{ asset('backend/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <style>
        .scroll {
            margin: 4px, 4px;
            padding: 4px;
            overflow-x: auto;
            overflow-y: hidden;
            white-space: nowrap;
        }
        #imagemodal .modal-body {
            padding: 0;
        }
        #imagemodal button {
            background: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            position: absolute;
            right: -20px;
            top: -20px;
            color: black;
            opacity: 1;
        }
    </style>
@endsection
