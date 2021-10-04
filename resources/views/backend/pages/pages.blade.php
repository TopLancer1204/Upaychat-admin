@extends('backend.app')

@section('content')

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">Static pages/content</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Home</a></li>
                            <li class="breadcrumb-item active">Content</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">All static conetents</h3>
                    </div>
                    <div class="card-body">
                        <table class="example1 table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Page Title</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($pages as $page)
                                <tr>
                                    <td>{{$page->page_name}}</td>
                                    <td>{{$page->page_description	}}</td>
                                    <td>
                                        <a href="{{route('page-edit',$page->id)}}" class="btn btn-primary btn-sm"><i
                                                class="fas fa-edit"></i>&nbsp;Edit</a>

                                    <!-- <button class="btn btn-danger btn-sm" onclick="deletePages(this,'{{$page->id}}','{{$page->page_image}}')"><i class="fas fa-trash-alt"></i>&nbsp;Delete
                                        </button>

                                        @if($page->page_status == 'on')
                                        <button class="btn btn-success btn-sm" onclick="pageStatus(this,'{{$page->id}}','off')"><i class="fas fa-eye-slash"></i>Pasif</button>
                                        @else
                                        <button class="btn btn-success btn-sm" onclick="pageStatus(this,'{{$page->id}}','on')"><i class="fas fa-eye"></i>Aktif</button>-->
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

    //page delete
    <script>
        function deletePages(r, id, page_image) {
            var list = r.parentNode.parentNode.rowIndex;
            swal({
                title: 'Silmek istediğinize emin misiniz?',
                text: "Sildiğinizde geri dönüşümü olmayacaktır!",
                type: 'warning',
                showCancelButton: true,
                cancelButtonText: 'İptal',
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Evet, Sil!'
            }).then((result) => {
                if (result.value) {
                    $.ajax
                    ({
                        type: "Post",
                        url: '{{route('pages')}}',
                        data: {
                            'id': id,
                            'page_image': page_image,
                            'delete': 'delete'
                        },
                        beforeSubmit: function () {
                            swal({
                                title: '<i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i> <span class="sr-only">Loading...</span>',
                                text: 'Siliniyor lütfen bekleyiniz...',
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

    //page status
    <script>
        function pageStatus(r, id, page_status) {
            $.ajax
            ({
                type: "Post",
                url: '{{route('pages')}}',
                data: {
                    'id': id,
                    'page_status': page_status
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
