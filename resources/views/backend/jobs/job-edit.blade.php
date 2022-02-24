@extends('backend.app')

@section('content')

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Edit Job</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Edit Job</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <form method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">Edit Job</h3>
                            </div>
                            <div class="card-body">

                                <div class="form-group">
                                    <label for="exampleInputEmail1">Title</label>
                                    <input type='text' name="title" rows="2" cols="75" class="form-control" value="{{$pages->title}}"></input>
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1">Description</label>
                                    <textarea name="description" rows="8" cols="75" class="form-control">{{$pages->description}}</textarea>
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1">Location</label>
                                    <input type="text" name="location" rows="2" cols="75" class="form-control" value="{{$pages->location}}"></input>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Tech</label>
                                    <input type="text" name="tech" rows="2" cols="75" class="form-control" value="{{$pages->tech}}"></input>
                                </div>
                                <div class="form-group">
                                    <label for="exampleInputEmail1">Type</label>
                                    <input type="text" name="type" rows="2" cols="75" class="form-control" value="{{$pages->type}}"></input>
                                </div>

                                <div class="form-group">
                                    <label for="exampleInputEmail1">Remote</label>
                                    <div class="input-group">
                                        <input type="checkbox" name="remote" checked data-bootstrap-switch>
                                    </div>
                                </div>
                            </div>

                            <div class="card-footer">
                                <button id="pageButton" type="button" class="btn btn-primary">Save</button>
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
    $(function() {
        //Initialize Select2 Elements
        $('.select2').select2()

        //Initialize Select2 Elements
        $('.select2bs4').select2({
            theme: 'bootstrap4'
        })

        $("input[data-bootstrap-switch]").each(function() {
            $(this).bootstrapSwitch('state', $(this).prop('checked'));
        });
    });
</script>

<script>
    $("#pageButton").click(function() {

        // CKEDITOR.instances['editor'].updateElement();
        var url = "{{route('job-edit',$pages->id)}}";
        var form = new FormData($("form")[0]);


        $.ajax({
            type: "POST",
            url: url,
            data: form,
            processData: false,
            contentType: false,

            success: function(response) {
                if (response.status == "success") {
                    toastr.success(response.content, response.title);
                } else {
                    toastr.error(response.content, response.title);
                }
            },
            error: function() {

            }
        });
    })
</script>

@endsection

@section('css')
<link rel="stylesheet" href="{{asset('backend/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css')}}">
<link rel="stylesheet" href="{{ asset('backend/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
@endsection