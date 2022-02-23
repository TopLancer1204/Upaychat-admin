@extends('backend.app')

@section('content')

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Edit Blog</h1>
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
                <form method="post">
                    <div class="row">
                        <div class="col-md-9">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Blog</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Title</label>
                                        <input type="text" name="blog_title" value="{{$blog->blog_title}}"
                                               class="form-control" placeholder="Your Title">
                                    </div>
                                    <div class="form-group">
                                        <label>Category</label>
                                        <select class="form-control select2" style="width: 100%;"
                                                name="blog_categoryId">
                                            <option value="{{$blog->blog_categoryId}}" selected></option>
                                            <option value="0">Other</option>
                                            @foreach($categories as $category)
                                                <option value="{{$category->id}}">{{$category->category_name}}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Short Description</label>
                                        <input type="text" name="blog_description" value="{{$blog->blog_description}}"
                                               class="form-control" placeholder="Short Description">
                                    </div>
                                   
                                    <div class="form-group">
                                        <label>Contents</label>
                                        <textarea name="blog_content" id="editor">{!! $blog->blog_content !!}</textarea>
                                    </div>
                                </div>

                                <div class="card-footer">
                                    <button id="blogsButton" type="button" class="btn btn-primary">Publish</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Blog Picture</h3>
                                </div>
                                <br>
                                <img id="coverImageShow" src="{{$blog->blog_image}}" width="100%" height="165px">
                                <div class="card-body">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input id="coverImage" name="blog_image" type="file"
                                                       class="custom-file-input"
                                                       id="customFile">
                                                <label class="custom-file-label" for="customFile">Choose file</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Blog Visibility</h3>
                                </div>
                                <br>
                                <div class="card-body">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input type="checkbox" name="blog_status"
                                                   {{$blog->blog_status == 'on' ? 'checked' : ' '}} data-bootstrap-switch>
                                        </div>
                                    </div>
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
    <script src="{{asset('backend/plugins/bs-custom-file-input/bs-custom-file-input.min.js')}}"></script>

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

    <script type="text/javascript">
        $(document).ready(function () {
            bsCustomFileInput.init();
        });
    </script>
    <script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
    <script>
        CKEDITOR.replace('editor',{
        filebrowserBrowseUrl     : "{{ route('ckfinder_browser') }}",
        filebrowserImageBrowseUrl: "{{ route('ckfinder_browser') }}?type=Images&token=123",
        filebrowserFlashBrowseUrl: "{{ route('ckfinder_browser') }}?type=Flash&token=123",
        filebrowserUploadUrl     : "{{ route('ckfinder_connector') }}?command=QuickUpload&type=Files",
        filebrowserImageUploadUrl: "{{ route('ckfinder_connector') }}?command=QuickUpload&type=Images",
        filebrowserFlashUploadUrl: "{{ route('ckfinder_connector') }}?command=QuickUpload&type=Flash",
    });
    </script>
    <script>
        $("#coverImage").change(function () {
            var input = this;
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#coverImageShow').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        });
    </script>

    <script>

        $("#blogsButton").click(function () {

            CKEDITOR.instances['editor'].updateElement();
            var url = "{{route("blog-edit",$blog->id)}}";
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
