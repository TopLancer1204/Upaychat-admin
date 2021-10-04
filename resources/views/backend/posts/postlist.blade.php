@extends('backend.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Post Management</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Post Management</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Post Management</h3>
                    </div>
                    <div class="card-body">
                        <table class="example1 table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Money From user</th>
                                <th>Money sent to</th>
                                <th> Amount</th>
                                <th>Post contant</th>
                                <th>Likes</th>
                                <th>Comments</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($posts as $post)
                                <tr>
                                    <td>{{$post['fromuser']}}</td>
                                    <td>{{$post['touser']}}</td>
                                    <td>₦{{$post['amount']}}</td>
                                    <td>{{$post['caption']}}</td>
                                    <td>{{$post['likes']}}</td>
                                    <td>
                                        @if($post['comments'] >0)
                                            <a href="{{route('getcomment',$post['id'])}}"
                                               class="btn btn-primary btn-sm">
                                                <i class="fas fa-search"></i>&nbsp; {{$post['comments']}} Comments</a>
                                        @else
                                            No comment
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
                        toastr.success(response.content, response.title);
                        setInterval(function () {
                            window.location.reload();
                        }, 5000);
                    } else {
                        toastr.error(response.content, response.title);
                        setInterval(function () {
                            window.location.reload();
                        }, 5000);
                    }
                }
            })
        }
    </script>

@endsection

@section('css')

@endsection
