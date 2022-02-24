<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fav Icon  -->
    <link href="{{ asset('favicon.png') }}" rel="shortcut icon" type="image/png">
    <link href="{{ asset('favicon.png') }}" rel="icon" type="image/png">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="stylesheet" href="{{asset('backend/plugins/datatables-bs4/css/dataTables.bootstrap4.css')}}">
    <link rel="stylesheet" href="{{asset('backend/plugins/select2/css/select2.min.css')}}">
    <link rel="stylesheet" href="{{asset('backend/plugins/fontawesome-free/css/all.min.css')}}">
    <link rel="stylesheet" href="{{asset('backend/plugins/overlayScrollbars/css/OverlayScrollbars.min.css')}}">
    <link rel="stylesheet" href="{{ asset('backend/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{asset('backend/plugins/toastr/toastr.min.css')}}">

    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    @yield("css")
</head>

<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
    <div class="wrapper">
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
                </li>
            </ul>
        </nav>

        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="/" class="brand-link">
                <b>Administrator</b>
                <span class="brand-text font-weight-light">{{$title ?? ''}}</span>
            </a>

            <div class="sidebar">
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="{{\Illuminate\Support\Facades\Auth::user()->avatar}}" class="img-circle elevation-2" alt="User Image">
                    </div>
                    <div class="info">
                        <a href="/" class="d-block">{{\Illuminate\Support\Facades\Auth::user()->username}}</a>
                    </div>
                </div>

                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <a href="{{route('home')}}" class="nav-link {{ request()->is('home') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>


                        <li class="nav-item {{\Illuminate\Support\Facades\Auth::user()->roll_id == 1 ? ' ' : 'd-none'}}">
                            <a href="{{route('users')}}" class="nav-link {{ request()->is('users') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-users"></i>
                                <p>User Management</p>
                            </a>
                        </li>


                        <li class="nav-item {{\Illuminate\Support\Facades\Auth::user()->roll_id == 1 ? ' ' : 'd-none'}}">
                            <a href="{{route('posts')}}" class="nav-link {{ request()->is('posts') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-images"></i>
                                <p>Post Management</p>
                            </a>
                        </li>

                        <li class="nav-item {{\Illuminate\Support\Facades\Auth::user()->roll_id == 1 ? ' ' : 'd-none'}}">
                            <a href="{{route('identity.index')}}" class="nav-link {{ request()->is('identity*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-id-card"></i>
                                <p>Identity Management</p>
                            </a>
                        </li>

                        <li class="nav-item {{\Illuminate\Support\Facades\Auth::user()->roll_id == 1 ? ' ' : 'd-none'}}">
                            <a href="{{route('transactions')}}" class="nav-link {{ request()->is('transactions') ? 'active' : '' }} {{ request()->is('transactions/users-transactions/*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-images"></i>
                                <p>Transaction Management</p>
                            </a>
                        </li>

                        <li class="nav-item {{\Illuminate\Support\Facades\Auth::user()->roll_id == 1 ? ' ' : 'd-none'}}">
                            <a href="{{route('withdraws')}}" class="nav-link {{ request()->is('withdraws') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-images"></i>
                                <p>Withdraw Requests</p>
                            </a>
                        </li>

                        <li class="nav-item {{\Illuminate\Support\Facades\Auth::user()->roll_id == 1 ? ' ' : 'd-none'}}">
                            <a href="{{route('pages')}}" class="nav-link {{ request()->is('pages') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Content Management</p>
                            </a>
                        </li>
                        <li class="nav-item {{\Illuminate\Support\Facades\Auth::user()->roll_id == 2 ? ' ' : 'd-none'}}">
                            <a href="{{route('faq')}}" class="nav-link {{ request()->is('faq') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>FAQ Management</p>
                            </a>
                        </li>


                        <li class="nav-item {{\Illuminate\Support\Facades\Auth::user()->roll_id == 1 ? ' ' : 'd-none'}}">
                            <a href="{{route('reports')}}" class="nav-link {{ request()->is('reports') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-cogs"></i>
                                <p>Report</p>
                            </a>
                        </li>


                        <li class="nav-item {{\Illuminate\Support\Facades\Auth::user()->roll_id == 1 ? ' ' : 'd-none'}}">
                            <a href="{{route('settings')}}" class="nav-link {{ request()->is('settings') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-cogs"></i>
                                <p>Settings</p>
                            </a>
                        </li>
                        <li class="nav-item {{\Illuminate\Support\Facades\Auth::user()->roll_id == 1 ? ' ' : 'd-none'}}">
                            <a href="{{route('profile')}}" class="nav-link {{ request()->is('profile') ? 'active' : '' }}">
                                <i class="nav-icon fa fa-user"></i>
                                <p>Profile</p>
                            </a>
                        </li>
                        <li class="nav-item {{\Illuminate\Support\Facades\Auth::user()->roll_id == 1 ? ' ' : 'd-none'}}">
                            <a href="{{route('password')}}" class="nav-link {{ request()->is('password') ? 'active' : '' }}">
                                <i class="nav-icon fa fa-key"></i>
                                <p>Password</p>
                            </a>
                        </li>


                        <li class="nav-item has-treeview {{\Illuminate\Support\Facades\Auth::user()->roll_id == 2 ? ' ' : 'd-none'}} {{ request()->is('blogs','blogs/blog-add','blogs/blog-edit/*') ? 'menu-open' : '' }}">
                            <a href="" class="nav-link">
                                <i class="nav-icon fas fa-images"></i>
                                <p>
                                    Blogs
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>

                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{route('blogs')}}" class="nav-link {{ request()->is('blogs') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>All Blogs</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('blog-add')}}" class="nav-link {{ request()->is('blogs/blog-add') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Add Blog</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{route('blog-category')}}" class="nav-link {{ request()->is('blogs/blog-category') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Blog Categories</p>
                                    </a>
                                </li>

                                <li class="nav-item">
                                    <a href="{{route('blog-category-add')}}" class="nav-link {{ request()->is('blogs/blog-category-add') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Add Category</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item {{\Illuminate\Support\Facades\Auth::user()->roll_id == 2 ? ' ' : 'd-none'}}">
                            <a href="{{route('jobs')}}" class="nav-link {{ request()->is('faq') ? 'active' : '' }}">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Job Management</p>
                            </a>
                        </li>
                        <li class="nav-item has-treeview {{\Illuminate\Support\Facades\Auth::user()->roll_id == 1 ? ' ' : 'd-none'}} {{ request()->is('sliders','sliders/slider-add','sliders/slider-edit/*') ? 'menu-open' : '' }}">
                            <a href="" class="nav-link">
                                <i class="nav-icon fas fa-images"></i>
                                <p>
                                    Slider
                                    <i class="fas fa-angle-left right"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item">
                                    <a href="{{route('sliders')}}" class="nav-link {{ request()->is('sliders') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>All Slider Images</p>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('slider-add')}}" class="nav-link {{ request()->is('sliders/slider-add') ? 'active' : '' }}">
                                        <i class="far fa-circle nav-icon"></i>
                                        <p>Add Slider Images</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="{{route('backend-logout')}}" class="nav-link ">
                                <i class="nav-icon fas fa-sign-out-alt"></i>
                                <p>Logout</p>
                            </a>
                        </li>

                    </ul>
                </nav>
            </div>
        </aside>

        @yield("content")

        <aside class="control-sidebar control-sidebar-dark">
        </aside>

        <footer class="main-footer">
            <strong>Copyright &copy; {{date("Y")}} <a href="#"></a>.</strong>
            All rights reserved.
        </footer>
    </div>

    <script src="{{asset('backend/plugins/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('backend/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('backend/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js')}}"></script>
    <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js" integrity="sha256-eGE6blurk5sHj+rmkfsGYeKyZx3M4bG+ZlFyA7Kns7E=" crossorigin="anonymous"></script>
    <script src="{{asset('backend/dist/js/adminlte.js')}}"></script>
    <script src="{{asset('backend/dist/js/demo.js')}}"></script>
    <script src="{{asset('backend/plugins/toastr/toastr.min.js')}}"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    <script>
        var options = {
            filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
            filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token=',
            filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
            filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&_token='
        };
    </script>

    <script src="{{asset('backend/plugins/datatables/jquery.dataTables.js')}}"></script>
    <script src="{{asset('backend/plugins/datatables-bs4/js/dataTables.bootstrap4.js')}}"></script>
    <script>
        $(function() {
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


    @yield("js")
</body>

</html>