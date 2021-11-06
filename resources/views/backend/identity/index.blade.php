@extends('backend.app')

@section('content')

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Identity Verification</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Identity Verification</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Identity Verification Status</h3>
                </div>
                <div class="card-body">
                    <table class="example1 table table-bordered table-striped" id="example1">
                        <thead>
                            <tr>
                                <th>Avatar</th>
                                <th>User Name</th>
                                <th>Goverment</th>
                                <th>Proof</th>
                                <th>ID card</th>
                                <th>Code</th>
                                <th>Status</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $item)
                            <tr>
                                @php
                                $link = route('identity.show', $item);
                                @endphp
                                <td onclick="location.href='{{$link}}'"><img src="{{@$item->user->avatar}}" alt="{{@$item->user->name}}" style="width: 50px; height:50px"></td>
                                <td onclick="location.href='{{$link}}'">{{@$item->user->name}}</td>
                                <td onclick="location.href='{{$link}}'"><img src="{{@$item->goverment[0]->path}}" alt="goverment" style="width: 50px; height:50px"></td>
                                <td onclick="location.href='{{$link}}'"><img src="{{@$item->proof[0]->path}}" alt="proof" style="width: 50px; height:50px"></td>
                                <td onclick="location.href='{{$link}}'"><img src="{{@$item->id_card[0]->path}}" alt="id card" style="width: 50px; height:50px"></td>
                                <td onclick="location.href='{{$link}}'">{{@$item->verify_code}}</td>
                                <td>
                                    @if ($item->status == 0)
                                    <button class="btn btn-warning btn-xs">Submitted</button>
                                    @elseif($item->status == 1)
                                    <button class="btn btn-primary btn-xs">Reviewing</button>
                                    @elseif($item->status == 2)
                                    <button class="btn btn-success btn-xs">Verified</button>
                                    @elseif($item->status == 3)
                                    <button class="btn btn-danger btn-xs">Rejected</button>
                                    @endif
                                </td>
                                <td><button class="btn btn-danger btn-xs" onclick="deleteUsers(this, '{{route('identity.destroy', $item)}}')"><i class="fas fa-trash-alt"></i>&nbsp;Delete</button></td>
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
<script>
    function deleteUsers(r, url) {
        const list = r.parentNode.parentNode.rowIndex;
        Swal.fire({
            title: 'Are you sure you want to delete it?',
            text: "It will not be recycled when you delete it!",
            type: 'warning',
            showCancelButton: true,
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Delete!',
            showLoaderOnConfirm: true,
            preConfirm: (login) => {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        type: "delete",
                        url: url,
                        success: function (response) {
                            console.log("success");
                            resolve(response);
                        },
                        error: function (error) {
                            toastr.error(error.toString(), "Request failed");
                            reject(error);
                        }
                    });
                })
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.value.status == 'success') {
                toastr.success(result.value.content, result.value.title);
                document.getElementById("example1").deleteRow(list);
            } else {
                toastr.error(result.value.content, result.value.title);
            }
        });
    }
</script>
@endsection

@section('css')

@endsection