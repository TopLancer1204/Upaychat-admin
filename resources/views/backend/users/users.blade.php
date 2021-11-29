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
                    <button class="btn btn-danger btn-sm" style="float: right" onclick="deleteUsers(this,0)">Delete all</button>
                </div>
                <div class="card-body">
                    <table class="example1 table table-bordered table-striped" id="example1">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile no</th>
                                <th>Birthday</th>
                                <th>Status</th>
                                <th>Login Failed</th>
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
                                <td>{{($user->birthday == null || $user->birthday == "") ? '' : date('d M Y', strtotime($user->birthday))}}</td>
                                <td id="tbl_status_{{$user->id}}">
                                    @if($user->user_status == 'on')
                                    <button class="btn btn-success btn-xs">Active</button>
                                    @else
                                    <button class="btn btn-danger btn-xs">Suspended</button>
                                    @endif
                                </td>
                                <td id="tbl_locked_{{$user->id}}">
                                    @if($user->locked < 5) <button rel="tooltip" title="{{$user->locked}} times failed to login." class="btn btn-success btn-xs"
                                        onclick="userStatus(this,'{{$user->id}}','lock')">Unlocked</button>
                                        @else
                                        <button rel="tooltip" title="Account locked" class="btn btn-danger btn-xs"
                                            onclick="userStatus(this,'{{$user->id}}','unlock')">Locked</button>
                                        @endif
                                </td>
                                <td>
                                    @if ($user->id != 1)
                                    {{-- <a href="{{route('user-edit',$user->id)}}" class="btn btn-primary btn-xs"><i class="fas fa-edit"></i>&nbsp;Edit</a> --}}
                                    <button class="btn btn-danger btn-xs" onclick="deleteUsers(this,'{{$user->id}}')"><i class="fas fa-trash-alt"></i>&nbsp;Delete</button>
                                    <span id="tbl_action_{{$user->id}}">
                                        @if($user->user_status == 'on')
                                        <button class="btn btn-success btn-xs" onclick="userStatus(this,'{{$user->id}}','off')"><i class="fas fa-eye-slash"></i>Suspend </button>
                                        @else
                                        <button class="btn btn-success btn-xs" onclick="userStatus(this,'{{$user->id}}','on')"><i class="fas fa-eye"></i>Activate</button>
                                        @endif
                                    </span>
                                    @endif
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
            var list = id == 0 ? null : r.parentNode.parentNode.rowIndex;
            swal({
                title: `Are you sure you want to delete ${id == 0 ? "All users" : "this user"}?`,
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
                                toastr.success(response.content, response.title);
                                if(list) {
                                    document.getElementById('example1').deleteRow(list);
                                } else {
                                    setTimeout(() => {
                                        location.reload();
                                    }, 1000);
                                }
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
                        if(user_status == 'on') {
                            $(`#tbl_status_${id}`).empty();
                            $(`#tbl_action_${id}`).empty();
                            $(`#tbl_status_${id}`).append('<button class="btn btn-success btn-xs">Active</button>');
                            $(`#tbl_action_${id}`).append(`<button class="btn btn-success btn-xs" onclick="userStatus(this,'${id}','off')"><i class="fas fa-eye-slash"></i>Suspend </button>`);
                        } else if(user_status == "off"){
                            $(`#tbl_status_${id}`).empty();
                            $(`#tbl_action_${id}`).empty();
                            $(`#tbl_status_${id}`).append('<button class="btn btn-danger btn-xs">Suspended</button>');
                            $(`#tbl_action_${id}`).append(`<button class="btn btn-success btn-xs" onclick="userStatus(this,'${id}','on')"><i class="fas fa-eye"></i>Activate </button>`);
                        } else if(user_status == "unlock"){
                            $(`#tbl_locked_${id}`).empty();
                            $(`#tbl_locked_${id}`).append(`<button rel="tooltip" title="{{$user->locked}} times failed to login." class="btn btn-success btn-xs" onclick="userStatus(this,'${id}','lock')">Unlocked</button>`);
                        } else if(user_status == "lock"){
                            $(`#tbl_locked_${id}`).empty();
                            $(`#tbl_locked_${id}`).append(`<button rel="tooltip" title="Account locked" class="btn btn-danger btn-xs" onclick="userStatus(this,'${id}','unlock')">Locked</button>`);
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