@if (count($errors) > 0)
<div class="alert alert-danger">
    <ul>
    @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
    @endforeach
    </ul>
</div>
@endif

@if(Session::has('warning')) 
<div class="alert alert-warning">
    <strong>Attention ! </strong> {!!Session::get('warning')!!}
</div>
@endif

@if(Session::has('success')) 
<div class="alert alert-success">
    <strong>Succès ! </strong> {!!Session::get('success')!!}
</div>
@endif

