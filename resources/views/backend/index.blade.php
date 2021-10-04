@extends('backend.app')

@section('content')
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
    <style>
        body {
            margin-top: 20px;
            background: #FAFAFA;
        }

        .order-card {
            color: #fff;
        }

        .bg-c-blue {
            background: linear-gradient(45deg, #4099ff, #73b4ff);
        }

        .bg-c-green {
            background: linear-gradient(45deg, #2ed8b6, #59e0c5);
        }

        .bg-c-yellow {
            background: linear-gradient(45deg, #FFB64D, #ffcb80);
        }

        .bg-c-pink {
            background: linear-gradient(45deg, #FF5370, #ff869a);
        }


        .card {
            border-radius: 5px;
            -webkit-box-shadow: 0 1px 2.94px 0.06px rgba(4, 26, 55, 0.16);
            box-shadow: 0 1px 2.94px 0.06px rgba(4, 26, 55, 0.16);
            border: none;
            margin-bottom: 30px;
            -webkit-transition: all 0.3s ease-in-out;
            transition: all 0.3s ease-in-out;
        }

        .card .card-block {
            padding: 25px;
        }

        .order-card i {
            font-size: 26px;
        }

        .f-left {
            float: left;
        }

        .f-right {
            float: right;
        }
    </style>
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Dashboard</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        </ol>
                    </div>

                </div>

                <div class="container">
                    <div class="row">
                        <div class="col-md-4 col-xl-4">
                            <div class="card bg-c-blue order-card">
                                <div class="card-block">
                                    <h6 class="m-b-20">Total Users</h6>
                                    <h2 class="text-right"><i
                                            class="nav-icon fas fa-users f-left"></i><span>{{$totalUsers}}</span></h2>
                                    <p class="m-b-0"><span class="f-right"></span></p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 col-xl-4">
                            <div class="card bg-c-green order-card">
                                <div class="card-block">
                                    <h6 class="m-b-20">Total Active Users</h6>
                                    <h2 class="text-right"><i
                                            class="nav-icon fas fa-users f-left"></i><span>{{$totalActiveUsers}}</span>
                                    </h2>
                                    <p class="m-b-0"><span class="f-right"></span></p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4 col-xl-4">
                            <div class="card bg-c-yellow order-card">
                                <div class="card-block">
                                    <h6 class="m-b-20">Total Earnings</h6>
                                    <h2 class="text-right"><i
                                            class="fa fa-money f-left"></i><span>{{ $balance->balance }}</span></h2>
                                    <p class="m-b-0"><span class="f-right"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">


                    </div>


                </div>
            </div>

        </div>

@endsection

@section('js')

@endsection

@section('css')

@endsection
