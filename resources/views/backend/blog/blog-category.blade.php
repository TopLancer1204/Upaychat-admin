@extends('backend.app')

@section('content')

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Blog Category</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home Page</a></li>
                            <li class="breadcrumb-item active">Blog Category</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Blog Categorys List</h3>
                    </div>
                    <div class="card-body">
                        <table class="example1 table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Category Name</th>
                                <th>Creation Date</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($categories as $category)
                                <tr>
                                    <td>{{$category->category_name}}</td>
                                    <td>{{$category->created_at}}</td>
                                    <td>
                                        <a href="{{route('blog-category-edit',$category->id)}}" class="btn btn-primary"><i
                                                class="fas fa-edit"></i>&nbsp;Edit</a>
                                        <button class="btn btn-danger"
                                                onclick="deleteCategory(this,'{{$category->id}}')"><i
                                                class="fas fa-trash-alt"></i>&nbsp;Delete
                                        </button>

                                        @if($category->category_status == 'on')
                                            <button class="btn btn-success"
                                                    onclick="categoryStatus(this,'{{$category->id}}','off')"><i
                                                    class="fas fa-eye-slash"></i>Disable
                                            </button>
                                        @else
                                            <button class="btn btn-success"
                                                    onclick="categoryStatus(this,'{{$category->id}}','on')"><i
                                                    class="fas fa-eye"></i>Active
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @foreach($category->children as $downCategory)
                                    <tr>
                                        <td>{{$category->category_name}} -> {{$downCategory->category_name}}</td>
                                        <td>{{$downCategory->created_at}}</td>
                                        <td>
                                            <a href="{{route('blog-category-edit',$downCategory->id)}}"
                                               class="btn btn-primary"><i class="fas fa-edit"></i>&nbsp;Edit</a>
                                            <button class="btn btn-danger"
                                                    onclick="deleteCategory(this,'{{$downCategory->id}}')"><i
                                                    class="fas fa-trash-alt"></i>&nbsp;Delete
                                            </button>

                                            @if($downCategory->category_status == 'on')
                                                <button class="btn btn-success"
                                                        onclick="categoryStatus(this,'{{$downCategory->id}}','off')"><i
                                                        class="fas fa-eye-slash"></i>Disable
                                                </button>
                                            @else
                                                <button class="btn btn-success"
                                                        onclick="categoryStatus(this,'{{$downCategory->id}}','on')"><i
                                                        class="fas fa-eye"></i>Active
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                            </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>

@endsection

@section('js')

    <script src="{{asset('js/sweetalert2.js')}}"></script>
    <script src="{{asset('backend/plugins/sweetalert2/sweetalert2.min.js')}}"></script>

    //category delete
    <script>
        function deleteCategory(r, id) {
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
                        url: '{{route('blog-category')}}',
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

    //category status
    <script>
        function categoryStatus(r, id, category_status) {
            $.ajax
            ({
                type: "Post",
                url: '{{route('blog-category')}}',
                data: {
                    'id': id,
                    'category_status': category_status
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
