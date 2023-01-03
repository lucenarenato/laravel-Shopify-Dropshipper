@extends('admin.layouts.backend')
@section('content')
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
{{--        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i--}}
{{--                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>--}}
    </div>


    <div class="container-fluid">

       @include('admin.layouts.partials.errors')


   <!-- START::CmsGlobalContent=========================================================================================== -->

        @if(@$CmsGlobalContent)

            @php
                $field = $CmsGlobalContent;
            @endphp

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header"><h3 align="center" style="color: #1f6fb2;">Edit General Contents</h3>
                    </div>
                    <div class="card-body">

                        <form action="{{ route('admin.save.globalContent',$field->id)}}," method="POST" name="edit_category" enctype="multipart/form-data">
                            @csrf
                                    <div class="row">

                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <img width="200px" src="{{asset($field->side_bar_logo)}}"/>
                                                    <strong>SideBar Logo</strong>
                                                    <input type="file" name="side_bar_logo" class="form-control" placeholder="">
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <strong>SideBar Logo Text1</strong>
                                                    <input type="text" name="side_bar_logo_text1" value="{{ $field->side_bar_logo_text1 }}" class="form-control" placeholder="" >
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <strong>SideBar Logo Text2</strong>
                                                    <input type="text" name="side_bar_logo_text2" value="{{ $field->side_bar_logo_text2 }}" class="form-control" placeholder="" >
                                                </div>
                                            </div>

                                        <fieldset class="border p-2 col-md-12">
                                            <legend  class="w-auto">Footer Section</legend>
                                            <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <strong>Footer cpr text1</strong>
                                                    <input type="text" name="footer_cpr_text1" value="{{ $field->footer_cpr_text1 }}" class="form-control" placeholder="" >
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <strong>Footer cpr link</strong>
                                                    <input type="text" name="footer_cpr_link" value="{{ $field->footer_cpr_link }}" class="form-control" placeholder="" >
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <strong>Footer cpr text2</strong>
                                                    <input type="text" name="footer_cpr_text2" value="{{ $field->footer_cpr_text2 }}" class="form-control" placeholder="" >
                                                </div>
                                            </div>
                                            </div>
                                        </fieldset>


                                    </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
       @endif

     <!-- END::CmsGlobalContent=========================================================================================== -->




     <!-- START::CmsSideBarMenu=========================================================================================== -->

        @if(@$CmsSideBarMenu && count($CmsSideBarMenu)>0)
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header"><h3 align="center" style="color: #1f6fb2;">Edit SideBar Menu</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.save.sideBarMenu')}}" method="POST" name="edit_category" enctype="multipart/form-data">
                                @csrf
                                    @foreach($CmsSideBarMenu as $key=>$field)
                                        <div class="row">
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <strong>Menu Title {{$key+1}}</strong>
                                                        <input type="text" name="menu_title[{{$field->menu_slug}}]" value="{{ $field->menu_title }}" class="form-control" placeholder="">
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                <img width="50px" style="background: #f8f9fc;" src="{{asset($field->menu_icon)}}"/>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <strong>Menu Icon {{$key+1}}</strong>
                                                        <input type="file" name="menu_icon[{{$field->menu_slug}}]" class="form-control" placeholder="">
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <strong>Menu Redirect to Link {{$key+1}}</strong>
                                                        <input type="text" name="menu_link_redirect_to[{{$field->menu_slug}}]" value="{{ $field->menu_link_redirect_to }}" class="form-control" placeholder="" required>
                                                    </div>
                                                </div>
                                        </div>
                                    @endforeach

                                <div class="row">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
       <!-- END::CmsSideBarMenu=========================================================================================== -->
    </div>

@endsection
