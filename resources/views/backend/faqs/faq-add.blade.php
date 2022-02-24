@extends('backend.app')

@section('content')

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Add FAQ</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">add FAQ</li>
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
                                    <h3 class="card-title">FAQ</h3>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Question</label>
                                        <textarea name="question" rows="2" cols="75" class="form-control"></textarea>
                                    </div>

                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Answer</label>
                                        <textarea name="answer" rows="8" cols="75" class="form-control"></textarea>
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

    <script>

        $("#pageButton").click(function () {

            // CKEDITOR.instances['editor'].updateElement();
            var url = "{{route('faq-add')}}";
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
