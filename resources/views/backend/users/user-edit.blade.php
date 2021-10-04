@extends('backend.app')

@section('content')

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Edit User</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Edit User</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <form method="post">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Edit User</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <div class="form-group">
                                            <label>User Picture</label>
                                            <input required type="file" name="avatar" class="form-control">
                                        </div>
                                        <div class="form-group">
                                            <label>First Name</label>
                                            <input required type="text" name="firstname" value="{{$user->firstname}}"
                                                   class="form-control" placeholder="First Name">
                                        </div>
                                        <div class="form-group">
                                            <label>Last Name</label>
                                            <input required type="text" name="lastname" value="{{$user->lastname}}"
                                                   class="form-control" placeholder="First Name">
                                        </div>

                                        <div class="form-group">
                                            <label>Email</label>
                                            <input required type="email" name="email" value="{{$user->email}}"
                                                   class="form-control"
                                                   placeholder=" Email address">
                                        </div>
                                        <div class="form-group">
                                            <label>Mobile no</label>
                                            <input required type="text" name="mobile" value="{{$user->mobile}}"
                                                   class="form-control"
                                                   placeholder=" Mobile no ">
                                        </div>

                                        <div class="form-group">
                                            <label>Password</label>
                                            <input required type="password" name="password" value="{{$user->password}}"
                                                   class="form-control"
                                                   placeholder="Password">
                                        </div>

                                        <label>Rol</label>
                                        <select required class="form-control select2" style="width: 100%;"
                                                name="roll_id">
                                            <option value="{{$user->roll_id}}" selected></option>
                                            @foreach($roles as $role)
                                                <option value="{{$role->id}}">{{$role->rol_name}}</option>
                                            @endforeach
                                        </select>

                                        <div class="form-group">
                                            <label for="">User Status</label><br>
                                            <input type="checkbox" name="user_status"
                                                   {{$user->user_status == 'on' ? 'checked' : ' '}} data-bootstrap-switch>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer">
                                    <button id="usersButton" type="button" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>

@endsection

@section('js')

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

    <script>

        $("#usersButton").click(function () {

            var url = "{{route("user-edit",$user->id)}}";
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

@endsection

@section('css')
    <link rel="stylesheet" href="{{asset('backend/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
    <link rel="stylesheet" href="{{ asset('backend/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@endsection
