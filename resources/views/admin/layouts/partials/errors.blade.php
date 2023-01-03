@if ($message = Session::get('success'))
    <p class="alert alert-success">{{ $message }}</p>
@endif

@if ($message = Session::get('error'))
    <p class="alert alert-danger">{{ $message }}</p>
@endif
