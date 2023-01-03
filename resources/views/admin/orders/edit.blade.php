@extends('admin.layouts.backend')
@section('content')
    <div class="container-fluid">

        <div class="row">

            <div class="col-lg-12 margin-tb">

                <div class="pull-left">

                    <h2>{{@$records->page_title ? $records->page_title : 'Import Products' }} </h2>

                </div>

                <div class="pull-right">

                    <a class="btn btn-primary" href="{{ route('admin.dashboard') }}"> Back</a>

                </div>

            </div>

        </div>

        @include('admin.layouts.partials.errors')

        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header"><h3 align="center" style="color: #1f6fb2;">Edit {{@$records->page_title ? $records->page_title : 'Import Products' }}</h3>
                    </div>
                    <div class="card-body">

                        <form action="{{ route('admin.orders.update',$records->id) }}" method="POST" name="edit_category" enctype="multipart/form-data">
                            @csrf

{{--                            @method('PUT')--}}

                                @if(count($records->CmsPagesContent)>0)

                                    @foreach($records->CmsPagesContent as $key=>$field)
                                    <div class="row">
                                           @if( $field->field_type=="htmlcontent")
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <strong>{{ $field->field_label }}</strong>
                                                    <textarea  col="4" class="form-control editor" style="height:150px" name="{{ $field->field_slug }}" placeholder="Description" >{{ $field->field_value }}</textarea>
                                                    <span class="text-danger">{{ $errors->first($field->field_slug) }}</span>
                                                </div>
                                            </div>
                                        @elseif ($field->field_type=="file")
                                            <div class="col-md-12">
                                                <div class="row">
                                                    @if($field->field_value!==null && $field->field_value!=="")
                                                        <div class="col-md-2">
                                                            <img src="{{asset($field->field_value)}}" width="60px" height="60px" alt="">
                                                            <button type="button"  onClick="removeImageCMS({{$field->id}})" class="btn btn-primary" style="margin:5px">Remove</button>
                                                        </div>
                                                    @endif
                                                    <div class="col-md-10">
                                                        <div class="form-group">
                                                            <strong>{{ $field->field_label }}</strong>
                                                            <input type="{{ $field->field_type }}" name="{{ $field->field_slug }}" value="{{ $field->field_value }}" class="form-control" placeholder="">
                                                            <span class="text-danger">{{ $errors->first($field->field_slug) }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                        <div class="col-md-12">
                                                <div class="form-group">
                                                    <strong>{{ $field->field_label }}</strong>
                                                    <input type="{{ $field->field_type }}" name="{{ $field->field_slug }}" value="{{ $field->field_value }}" class="form-control" placeholder="" >
                                                    <span class="text-danger">{{ $errors->first($field->field_slug) }}</span>
                                                </div>
                                            </div>
                                           @endif
                                    </div>
                                    @endforeach
                                @endif

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
    </div>
@endsection
