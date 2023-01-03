<!DOCTYPE html>
<html lang="en">
<head>
    @include('admin.layouts.partials.head')
</head>
<body>

<!-- Page Wrapper -->
<div id="wrapper">
    <!-- Sidebar -->
@include('admin.layouts.partials.sidebar')
<!-- End of Sidebar -->

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">
        @include('admin.layouts.partials.nav')

        <!-- Begin Page Content -->
            <div class="container-fluid" id="page-top">


                @yield('content')


            </div>
            <!-- /.container-fluid -->

        </div>

        @include('admin.layouts.partials.footer')

    </div>
</div>
<!-- End of Page Wrapper -->
@include('admin.layouts.partials.footer-scripts')

</body>
</html>
