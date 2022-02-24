@extends('backend.app')

@section('content')

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Job Management</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Job Management</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-sm-10"><h3 class="card-title">All Job</h3></div>

                            <div class="col-sm-2">
                                <a href="{{route('job-add')}}" class="btn btn-primary" style="padding-bottom:4px">Add
                                    Job </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="example1 table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Title</th>
                                <th>Dscription</th>
                                <th>Location</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($pages as $page)
                                <tr>
                                    <td>{{$page->title}}</td>
                                    <td>{{$page->description}}</td>
                                    <td>{{$page->location}}</td>
                                    <td>{{$page->updated_at}}</td>
                                    <td>
                                        <a href="{{route('job-edit',$page->id)}}" class="btn btn-primary btn-sm"><i
                                                class="fas fa-edit"></i>&nbsp;Edit</a>

                                        <button class="btn btn-danger btn-sm"
                                                onclick="deletePage(this,'{{$page->id}}')"><i
                                                class="fas fa-trash-alt"></i>&nbsp;Delete
                                        </button>


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

    //page delete
    <script>
        function deletePage(r, id) {
            var list = r.parentNode.parentNode.rowIndex;
            swal({
                title: 'Are you sure to delete this?',
                text: "It will not be recovered when you delete it!",
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
                        url: '{{route("faq")}}',
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

@endsection

@section('css')

@endsection
