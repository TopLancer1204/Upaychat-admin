@extends('backend.app')

@section('content')

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">User Management</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">User management</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Transaction Management</h3>
                    </div>
                    <div class="card-body">
                        <table class="example1 table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile no</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>{{$user->firstname}} {{$user->lastname}}</td>
                                <!--<td>{{$user->roleName->rol_name}}</td>-->
                                    <td>{{$user->email}}</td>
                                    <td>{{$user->mobile}}</td>
                                    <td id="tbl_status_{{$user->id}}">
                                        @if($user->user_status == 'on')
                                            <button class="btn btn-success btn-xs">Active</button>
                                        @else
                                            <button class="btn btn-danger btn-xs">Suspended</button>
                                        @endif
                                    </td>
                                    <td>
                                        {{-- <a href="{{route('user-edit',$user->id)}}" class="btn btn-primary btn-xs"><i class="fas fa-edit"></i>&nbsp;Edit</a>
                                        <button class="btn btn-danger btn-xs" onclick="deleteUsers(this,'{{$user->id}}')"><i class="fas fa-trash-alt"></i>&nbsp;Delete</button> --}}
                                        <span id="tbl_action_{{$user->id}}">
                                            @if($user->user_status == 'on')
                                                <button class="btn btn-success btn-xs" onclick="userStatus(this,'{{$user->id}}','off')"><i class="fas fa-eye-slash"></i>Suspend </button>
                                            @else
                                                <button class="btn btn-success btn-xs" onclick="userStatus(this,'{{$user->id}}','on')"><i class="fas fa-eye"></i>Activate</button>
                                            @endif
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>

                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
            </div>
        </section>
    </div>

@endsection

@section('js')
    <script src="{{asset('js/sweetalert2.js')}}"></script>
    <script src="{{asset('backend/plugins/sweetalert2/sweetalert2.min.js')}}"></script>

    //user delete
    <script>
        function deleteUsers(r, id) {
            var list = r.parentNode.parentNode.rowIndex;
            swal({
                title: 'Are you sure you want to delete it?',
                text: "It will not be recycled when you delete it!",
                type: 'warning',
                showCancelButton: true,
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Delete!'
            }).then((result) => {
                if (result.value) {
                    $.ajax
                    ({
                        type: "Post",
                        url: '{{route('users')}}',
                        data: {
                            'id': id,
                            'delete': 'delete'
                        },
                        beforeSubmit: function () {
                            swal({
                                title: '<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i> <span class="sr-only">Loading...</span>',
                                text: 'Deleting please wait...',
                                showConfirmButton: false
                            })
                        },
                        success: function (response) {
                            if (response.status == 'success') {
                                document.getElementById('example1').deleteRow(list);
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

    //user status
    <script>
        function userStatus(r, id, user_status) {
            $.ajax
            ({
                type: "Post",
                url: '{{route('users')}}',
                data: {
                    'id': id,
                    'user_status': user_status
                },
                success: function (response) {
                    if (response.status == 'success') {
                        $(`#tbl_status_${id}`).empty();
                        $(`#tbl_action_${id}`).empty();
                        if(user_status == 'on') {
                            $(`#tbl_status_${id}`).append('<button class="btn btn-success btn-xs">Active</button>');
                            $(`#tbl_action_${id}`).append(`<button class="btn btn-success btn-xs" onclick="userStatus(this,'${id}','off')"><i class="fas fa-eye-slash"></i>Suspend </button>`);
                        } else{
                            $(`#tbl_status_${id}`).append('<button class="btn btn-danger btn-xs">Suspended</button>');
                            $(`#tbl_action_${id}`).append(`<button class="btn btn-success btn-xs" onclick="userStatus(this,'${id}','on')"><i class="fas fa-eye"></i>Activate </button>`);
                        }
                        toastr.success(response.content, response.title);
                    } else {
                        toastr.error(response.content, response.title);
                        window.location.reload();
                    }
                }
            })

        }
    </script>

@endsection

@section('css')

@endsection
