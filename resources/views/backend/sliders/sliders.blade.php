@extends('backend.app')

@section('content')

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Slider</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Slider</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Slider Image List</h3>
                    </div>
                    <div class="card-body">
                        <table class="example1 table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Image</th>
                                <th>Image title</th>
                                <th>Added on</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($sliders as $slider)
                                <tr>
                                    <td><img width="170" src="{{asset($slider->slider_image)}}" height="100"></td>
                                    <td>{{$slider->slider_name}}</td>
                                    <td>{{$slider->created_at}}</td>
                                    <td>
                                        <a href="{{route('slider-edit',$slider->id)}}" class="btn btn-primary btn-sm"><i
                                                class="fas fa-edit"></i>&nbsp;Edit</a>
                                        <button class="btn btn-danger btn-sm"
                                                onclick="deleteSlider(this,'{{$slider->id}}')"><i
                                                class="fas fa-trash-alt"></i>&nbsp;Delete
                                        </button>

                                        @if($slider->slider_status == 'on')
                                            <button class="btn btn-success btn-sm"
                                                    onclick="sliderStatus(this,'{{$slider->id}}','off')"><i
                                                    class="fas fa-eye-slash"></i>OFF
                                            </button>
                                        @else
                                            <button class="btn btn-success btn-sm"
                                                    onclick="sliderStatus(this,'{{$slider->id}}','on')"><i
                                                    class="fas fa-eye"></i>ON
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

    //slider delete
    <script>
        function deleteSlider(r, id) {
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
                        url: '{{route('sliders')}}',
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

    //slider status
    <script>
        function sliderStatus(r, id, slider_status) {
            $.ajax
            ({
                type: "Post",
                url: '{{route('sliders')}}',
                data: {
                    'id': id,
                    'slider_status': slider_status
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
