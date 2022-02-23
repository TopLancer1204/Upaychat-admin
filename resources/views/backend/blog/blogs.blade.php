@extends('backend.app')

@section('content')

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Blogs</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home Page</a></li>
                            <li class="breadcrumb-item active">Blog</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Blog List</h3>
                    </div>
                    <div class="card-body">
                        <table class="example1 table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Blog Title</th>
                                <th>Writer</th>
                                <th>Release Date</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($blogs  as $blog)
                                <tr>
                                    <td>{{$blog->blog_title}}</td>
                                    <td>{{$blog->userName->name}}</td>
                                    <td>{{$blog->created_at}}</td>
                                    <td>
                                        <a href="{{route('blog-edit',$blog->id)}}" class="btn btn-primary"><i
                                                class="fas fa-edit"></i>&nbsp;Edit</a>
                                        <button class="btn btn-danger" onclick="deleteBlog(this,'{{$blog->id}}')"><i
                                                class="fas fa-trash-alt"></i>&nbsp;Delete
                                        </button>

                                        @if($blog->blog_status == 'on')
                                            <button class="btn btn-success"
                                                    onclick="blogsStatus(this,'{{$blog->id}}','off')"><i
                                                    class="fas fa-eye-slash"></i>Deactive
                                            </button>
                                        @else
                                            <button class="btn btn-success"
                                                    onclick="blogsStatus(this,'{{$blog->id}}','on')"><i
                                                    class="fas fa-eye"></i>Active
                                            </button>
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
    <script src="{{asset('plugins/datatables/jquery.dataTables.js')}}"></script>
    <script src="{{asset('plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}"></script>
    <script>
        $(function () {
            $(".example1").DataTable();
            $('.example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
            });
        });
    </script>

    //blog delete
    <script>
        function deleteBlog(r, id) {
            var list = r.parentNode.parentNode.rowIndex;
            swal({
                title: 'Are you going to Delete?',
                text: "Now deleting selected Category!",
                type: 'warning',
                showCancelButton: true,
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sure, Delete'
            }).then((result) => {
                if (result.value) {
                    $.ajax
                    ({
                        type: "Post",
                        url: '{{route('blogs')}}',
                        data: {
                            'id': id,
                            'delete': 'delete'
                        },
                        beforeSubmit: function () {
                            swal({
                                title: '<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i> <span class="sr-only">Loading...</span>',
                                text: 'Now Deleting, Please wait...',
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

    //blog status
    <script>
        function blogsStatus(r, id, blog_status) {
            $.ajax
            ({
                type: "Post",
                url: '{{route('blogs')}}',
                data: {
                    'id': id,
                    'blog_status': blog_status
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
