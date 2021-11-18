@extends('backend.app')

@section('content')

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Transactions of <small>{{$user->name}}</small></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Transactions</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>


    <section class="content">
        <div class="card">
            <div class="container-fluid">
                <div class="card-body">
                    <nav>
                        <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link active" id="nav-home-tab" data-toggle="tab" href="#tab-all" role="tab" aria-controls="nav-home" aria-selected="true">All
                                Transactions</a>
                            <a class="nav-item nav-link" id="nav-profile-tab" data-toggle="tab" href="#tab-public" role="tab" aria-controls="nav-profile"
                                aria-selected="false">Public Transactions</a>
                            <a class="nav-item nav-link" id="nav-about-tab" data-toggle="tab" href="#tab-private" role="tab" aria-controls="nav-about" aria-selected="false">Private
                                Transactions</a>
                        </div>
                    </nav>
                    <div class="tab-content py-3 px-3 px-sm-0" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="tab-all" role="tabpanel" aria-labelledby="nav-home-tab">
                            <table class="example1 table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Money Sent from</th>
                                        <th>Money Sent to</th>
                                        <th>Amount</th>
                                        <th>Privacy</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($settings as $setting)
                                    <tr>
                                        <td>{{$setting['from']}}</td>
                                        <td>{{$setting['touser']}}</td>
                                        <td>₦{{ number_format($setting['amount'], 2, '.', ',')}}</td>
                                        <td>{{$setting['privacy']}}</td>
                                        <td>{{$setting['date']}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>
                        <div class="tab-pane fade" id="tab-public" role="tabpanel" aria-labelledby="nav-profile-tab">
                            <table class="example1 table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Money Sent from</th>
                                        <th>Money Sent to</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($public as $setting)
                                    <tr>
                                        <td>{{$setting['from']}}</td>
                                        <td>{{$setting['touser']}}</td>
                                        <td>₦{{ number_format($setting['amount'], 2, '.', ',')}}</td>
                                        <td>{{$setting['date']}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>


                        <div class="tab-pane fade" id="tab-private" role="tabpanel" aria-labelledby="nav-about-tab">
                            <table class="example1 table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Money Sent from</th>
                                        <th>Money Sent to</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($private as $setting)
                                    <tr>
                                        <td>{{$setting['from']}}</td>
                                        <td>{{$setting['touser']}}</td>
                                        <td>₦{{ number_format($setting['amount'], 2, '.', ',')}}</td>
                                        <td>{{$setting['date']}}</td>
                                    </tr>
                                    @endforeach
                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection
<style>
    nav>.nav.nav-tabs {

        border: none;
        color: #fff;
        background: #272e38;
        border-radius: 0;

    }

    nav>div a.nav-item.nav-link,
    nav>div a.nav-item.nav-link.active {
        border: none;
        padding: 18px 25px;
        color: #fff;
        background: #272e38;
        border-radius: 0;
    }

    nav>div a.nav-item.nav-link.active:after {
        content: "";
        position: relative;
        bottom: -60px;
        left: -10%;
        border: 15px solid transparent;
        border-top-color: #e74c3c;
    }

    .tab-content {
        background: #fdfdfd;
        line-height: 25px;
        border: 1px solid #ddd;
        border-top: 5px solid #e74c3c;
        border-bottom: 5px solid #e74c3c;
        padding: 30px 25px;
    }

    nav>div a.nav-item.nav-link:hover,
    nav>div a.nav-item.nav-link:focus {
        border: none;
        background: #e74c3c;
        color: #fff;
        border-radius: 0;
        transition: background 0.20s linear;
    }
</style>
@section('js')
<script src="{{asset('js/sweetalert2.js')}}"></script>
<script src="{{asset('backend/plugins/sweetalert2/sweetalert2.min.js')}}"></script>
<script src="{{asset('backend/plugins/select2/js/select2.full.min.js')}}"></script>
<script src="{{asset('backend/plugins/bootstrap-switch/js/bootstrap-switch.min.js')}}"></script>




<script>
    $("#settingsButton").click(function () {

            var url = "{{route("settings")}}";
            var form = new FormData($("form")[0]);

            $.ajax({
                type: "POST",
                url: url,
                data: form,
                processData: false,
                contentType: false,

                success: function (response) {
                    if (response.status === "success") {
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

@endsection

@section('css')
<link rel="stylesheet" href="{{asset('backend/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
@endsection