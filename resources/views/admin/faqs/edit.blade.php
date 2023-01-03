@extends('admin.layouts.backend')
@section('content')
    <div class="container-fluid">

        <div class="row">

            <div class="col-lg-12 margin-tb">

                <div class="pull-left">

                    <h2>FAQ</h2>

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
                    <div class="card-header"><h3 align="center" style="color: #1f6fb2;">Edit FAQ</h3>
                    </div>
                    <div class="card-body">


                        <form action="{{ route('admin.faqs.update') }}" method="POST" name="edit_category" enctype="multipart/form-data">
                            @csrf

{{--                            @method('PUT')--}}
                            @foreach($records as $key=>$record)

                            <div class="form-divider">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <strong>Faq Type : </strong> {{ $record->faq_type }}
                                        <input type="text" name="faq_type[{{$record->faq_slug}}]" value="{{ $record->faq_type }}" class="form-control" placeholder="">
                                        <span class="text-danger">{{ $errors->first($record->faq_slug) }}</span>
                                    </div>
                                </div>
                            </div>

                                @if(count($record->cmsFaqContent)>0)
                                    @foreach($record->cmsFaqContent as $key=>$field)
                                    <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <strong>Faq Question : </strong>{{ $field->faq_question }}
                                                    <input type="{{ $field->field_type }}" name="faq_question[{{$field->id}}]" value="{{ $field->faq_question }}" class="form-control" placeholder="" >
                                                    <span class="text-danger">{{ $errors->first($field->faq_question) }}</span>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <strong>Faq Ans</strong>
                                                    <textarea col="4" class="form-control editor" style="height:150px" name="faq_answer[{{$field->id}}]" placeholder="Description">{{ $field->faq_answer }}</textarea>
                                                    <span class="text-danger">{{ $errors->first($field->id) }}</span>
                                                </div>
                                            </div>
                                    </div>
                                    @endforeach
                                @endif
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
    </div>
@endsection
