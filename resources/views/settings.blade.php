@extends('layouts.app')

@section('title', ' | Settings')

@section('content')
<h1> {{isset($page_title) ? $page_title : "Settings" }}</h1>
<settings-manager :prop-plan="{{ $settings->plan_id }}" prop-amazon-associate-btn="{{ $settings->amazon_associate_btn }}" :prop-diagnostics="Boolean({{ $settings->diagnostics }})" :page_contents="{{json_encode($page_contents)}}"></settings-manager>
@endsection
