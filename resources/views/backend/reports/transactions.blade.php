@extends('backend.app')

@section('content')

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Reports</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Reports</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>


        <section class="content">
            <div class="card">
                <div class="container-fluid">
                    <div class="card-body">

                        <div class="tab-pane fade show active" id="tab-all" role="tabpanel"
                             aria-labelledby="nav-home-tab">
                            <form method="post">

                                {{ csrf_field() }}
                                <table class="table table-bordered " style=" border:0px !important">
                                    <tbody>
                                    <tr>
                                        <td id="processing"
                                            style="color:green; font-size:18px; text-align:center; display:none">
                                            Preparing data please wait....
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border:0px !important"></td>
                                    </tr>
                                    <tr>
                                        <td style=" border:0px !important">
                                            <select id="reportType" name="reportType" class="form-control"
                                                    onchange="userfilter(this.value)">
                                                <option value="">Select report type</option>
                                                <option value="WholeTransaction">Whole Transaction</option>
                                                <option value="UserTransaction">User Transaction</option>
                                            </select>
                                        </td>
                                    <tr>
                                        <td id="userlist" style="border:0px !important; display:none">
                                            <select id='selUser' name="selUser" class="form-control"
                                                    style='width: 100%; padding-bottom:3px !important'>
                                                <option value='0'>-- Search user --</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="border:0px !important">
                                            <select id="duration" name="duration" class="form-control"
                                                    onchange="datefilter(this.value)">
                                                <option value="">Select duration</option>
                                                <option value="Monthly">Monthly</option>
                                                <option value="custom">Custom</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td id="thedate" style="border:0px !important; display:none">
                                            <input class="datepicker form-control" style="width:30%" name="dates"
                                                   id="dates" data-date-format="yyyy-mm-dd">

                                            <input type="hidden" name="startdt" value="" id="startdt"/>
                                            <input type="hidden" name="enddt" value="" id="enddt"/>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style=" border:0px !important">

                                            <input id="ReportsButton" type="submit" class="btn btn-success btn-lg"
                                                   value="download"/></td>

                                    </tr>
                                    </tbody>
                                </table>
                            </form>
                        </div>


                    </div>

                </div>
            </div>
    </div>

    </div>
    </section>
    </div>
    <style>
        .select2-container .select2-selection--single {
            padding: 3px !important;
            height: calc(2.25rem + 2px) !important;
        }
    </style>
@endsection
@section('js')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script src="{{asset('backend/plugins/select2/js/select2.min.js')}}"></script>

    <script>
        var startDate;
        var endDate;

        $('input[name="dates"]').daterangepicker(
            {
                startDate: moment().subtract('days', 29),
                endDate: moment(),
                function(start, end) {
                    console.log("Callback has been called!");
                    $('#reportrange span').html(start.format('D MMMM YYYY') + ' - ' + end.format('D MMMM YYYY'));
                    startDate = start;
                    endDate = end;

                }

            }
        );

        /*
         $('input[name="dates"]').on('apply.daterangepicker', function(ev, picker)
         {alert(picker.endDate.format('YYYY-MM-DD'));
              $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
          });
        */
        function datefilter(sel) {
            if (sel == 'custom') {
                $("#thedate").show();
            } else {
                $("#thedate").hide();
            }
        }

        function userfilter(sel) {
            if (sel == 'UserTransaction') {
                $("#userlist").show();
            } else {
                $("#userlist").hide();
            }
        }


        $("#ReportsButton").click(function () {

            $("#startdt").val($('#dates').data('daterangepicker').startDate.format('YYYY-MM-DD'));
            $("#enddt").val($('#dates').data('daterangepicker').endDate.format('YYYY-MM-DD'));

            //$("#processing").show();
            var url = "{{route('reports')}}";
            var form = new FormData($("form")[0]);

            if ($("#reportType").val() == "") {
                toastr.error("", "Please select report type");
                return false;
            }


        })

        ///////////////

        $("#selUser").select2({
            ajax: {

                url: "{{route('getuser')}}",
                type: "post",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        "_token": "{!! csrf_token() !!}",
                        search: params.term // search term
                    };
                },
                processResults: function (response) {
                    return {
                        results: response
                    };
                },
                cache: true
            }

        });

    </script>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <link rel="stylesheet" type="text/css" href="/backend/plugins/select2/css/select2.min.css">
@endsection
