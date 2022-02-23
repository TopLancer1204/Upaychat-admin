@extends('backend.app')

@section('content')

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Blog Category Edit</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home Page</li>
                            <li class="breadcrumb-item active">Blog Category</li>
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
                                    <h3 class="card-title">Blog Category</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Categories</label>
                                        <select class="form-control select2" style="width: 100%;" name="up_categoryId">
                                            <option value="0" selected="selected">Top Category</option>
                                            @foreach($categories as $categoryy)
                                                <option
                                                    value="{{$categoryy->id}}">{{$categoryy->category_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Category Name</label>
                                        <input type="text" name="category_name" value="{{$category->category_name}}"
                                               class="form-control" placeholder="Category Name">
                                    </div>

                                    <div class="form-group">
                                        <label for="">Category Visibility<br>
                                        <input type="checkbox" name="category_status"
                                               {{$category->category_status == 'on' ? 'checked' : ' ' }} data-bootstrap-switch>
                                    </div>
                                </div>

                                <div class="card-footer">
                                    <button id="categoryButton" type="button" class="btn btn-primary">Save</button>
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

        $("#categoryButton").click(function () {

            var url = "{{route("blog-category-edit",$category->id)}}";
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
