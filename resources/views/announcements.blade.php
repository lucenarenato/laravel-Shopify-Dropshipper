@extends('layouts.app')

@section('title', ' | Announcements')

@section('content')
<h1 class="mb-3">Announcements</h1>
@if(count($alerts) > 0)
        @foreach($alerts as $alert)
            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow-sm" id="a{{ $alert['id'] }}">
                        <div class="card-header"><strong>{{ $alert['heading']}}</strong>
                            <div class="card-header-actions"><small class="text-muted">{{ $alert['date']}}</small></div>
                        </div>
                        <div class="card-body">{{ $alert['text']}}</div>
                    </div>
                </div>
            </div>
        @endforeach
@else
    <p>No anouncements have been posted yet.</p>
@endif
@endsection