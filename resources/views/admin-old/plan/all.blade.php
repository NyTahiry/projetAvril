@extends('layouts.admin')

@section('content')
<div id="main-content" class="main-content container-fluid">
    <div class="row-fluid page-head">
        <h2 class="page-title">{{$title}}</h2>
    </div>
    <div>
        <h4>@lang('app.search.filter')</h4>
        <form method="get" action="">
            <div class="col-md-3">
                <input id="q" type="text" class="form-control" name="q" placeholder="@lang('app.search')" title="@lang('app.search')" value="{{$q}}">
            </div>
            <div class="col-md-3">
                <input id="number" type="number" class="form-control" name="record" title="Nombre par page" placeholder="Nombre par page" min="10" value="{{$record}}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-success">@lang('app.btn.search')</button>
            </div>
        </form>
    </div>
    <br>
    <br>
    <!-- // page head -->
    <div id="page-content" class="page-content">
        <section>
            <div class="row-fluid">
                <div class="span12">
                    @include('includes.alerts')
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th scope="col">ID <span class="column-sorter"></span></th>
                                <th scope="col">Name<span class="column-sorter"></span></th>
                                <th scope="col">Description<span class="column-sorter"></span></th>
                                <th scope="col">Prix<span class="column-sorter"></span></th>
                                <th scope="col">Type<span class="column-sorter"></span></th>
                                <th scope="col">Role<span class="column-sorter"></span></th>
                                <th scope="col">Date <span class="column-sorter"></span></th>
                                <th scope="col">Actions </th>
                            </tr>
                        </thead>
                        <tbody>
                          @foreach($items as $item) 
                            <tr>
                                <td>{{$item->id}}</td>
                                <td>{{$item->name}}</td>
                                <td>{{$item->description}}</td>
                                <td>{{$item->cost}}</td>
                                <td>{{$item->type}}</td>
                                <td>{{$item->role}}</td>
                                <td>{{$item->created_at->diffForHumans()}}</td>
                                <td>
                                    <a href="{{route('admin.plan.edit', $item)}}" class="btn btn-small btn-info btn-update">@lang('app.btn.edit')</a>
                                    <a href="{{route('admin.plan.delete', $item)}}" class="btn btn-small btn-warning btn-delete">@lang('app.btn.delete')</a>
                                </td>
                            </tr>
                           @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            {{$items->links()}}
        </section>
    </div>
</div>
@endsection
