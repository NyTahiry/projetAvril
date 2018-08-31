@extends('layouts.lte')

@section('content')
    <div class="row">
        <div class="col-md-4">
            @if($item->id>0)
            <a href="{{route('admin.plan.create')}}" class="btn btn-primary btn-block margin-bottom">@lang('app.admin.plan.create')</a>
            @endif
            <form role="form" method="post" action="{{$action}}" enctype="multipart/form-data">
                {{csrf_field()}}
                <div class="box box-primary">
                    <div class="box-header with-border">
                      @if($item->id>0)
                      <h3 class="box-title">@lang('app.admin.plan.update')</h3>
                      @else
                      <h3 class="box-title">@lang('app.admin.plan.create')</h3>
                      @endif
                      <div class="box-tools">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                        </button>
                      </div>
                    </div>
                    <div class="box-body">
                        <!-- name input -->
                        <div class="form-group">
                          <label>@lang('app.admin.name')</label>
                          <input name="name" type="text" class="form-control" value="{{$item->name}}" placeholder="@lang('app.admin.name.desc')">
                          <span class="help-block">@lang('app.admin.name.desc')</span>
                        </div>
                        <!-- price input -->
                        <div class="form-group">
                          <label>@lang('app.admin.price')</label>
                          <input name="cost" type="number" class="form-control" value="{{$item->cost}}" placeholder="@lang('app.admin.price.desc')">
                          <span class="help-block">@lang('app.admin.price.desc')</span>
                        </div>
                        <!-- description -->
                        <div class="form-group">
                          <label>@lang('app.admin.description')</label>
                          <textarea name="description" class="form-control" rows="3" placeholder="@lang('app.admin.description.desc')">{!!$item->description!!}</textarea>
                          <span class="help-block">@lang('app.admin.description.desc')</span>
                        </div>
                        <!-- role -->
                        <div class="form-group">
                            <label>@lang('app.select_role')</label>
                            <select id="role" name="role" class="select2 form-control">
                                <option value="0">@lang('app.select_role')</option>
                                <option {{$item->role=='admin'?'selected':''}} value="admin">Admin</option>
                                <option {{$item->role=='apl'?'selected':''}} value="apl">APL</option>
                                <option {{$item->role=='afa'?'selected':''}} value="afa">AFA</option>
                                <option {{$item->role=='member'?'selected':''}} value="member">Member</option>
                                <option {{$item->role=='seller'?'selected':''}} value="seller">Seller</option>
                            </select>
                        </div>
                        <!-- type -->
                        <div class="form-group">
                            <label>@lang('app.select_frequency')</label>
                            <select id="type" name="type" class="select2 form-control">
                                <option value="0">@lang('app.select_frequency')</option>
                                <option value="daily" {{$item->type=='daily'?'selected':''}}>@lang('app.frequency.daily')</option>
                                <option value="bimonthly" {{$item->type=='bimonthly'?'selected':''}}>@lang('app.frequency.bimonthly')</option>
                                <option value="monthly" {{$item->type=='monthly'?'selected':''}}>@lang('app.frequency.monthly')</option>
                                <option value="yearly" {{$item->type=='yearly'?'selected':''}}>@lang('app.frequency.yearly')</option>
                            </select>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer">
                      <div class="pull-right">
                          <button type="submit" class="btn btn-info" name="method" value="draft"><i class="fa fa-database"></i> @lang('app.btn.save')</button>
                      </div>
                      <button type="reset" class="btn btn-default"><i class="fa fa-times"></i> @lang('app.btn.discard')</button>
                    </div>
                    <!-- /.box-footer -->
               </div>
           </form>
        </div>
        <div class="col-md-8">
          @if(count($items)>0)
              <div class="box box-primary">
                <div class="box-header">
                  <div class="row">
                      <div class="col-md-12 pull-right">
                        <form method="get" action="{{url()->current()}}">
                            <div class="input-group input-group-sm">
                              <div class="col-md-3 input-group-sm pull-right" style="padding-right: 0; padding-left: 0;">
                                  <input type="text" name="q" class="form-control pull-right" placeholder="@lang('app.search')" value="{{$q}}">
                              </div>
                              <div class="col-md-3 input-group-sm pull-right" style="padding-right: 0; padding-left: 0;">
                                  <input class="form-control" type="number" name="record" min="10" value="{{$record}}" placeholder="Nombre par page">
                              </div>
                              <div class="input-group-btn">
                                <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                              </div>
                            </div>
                        </form>
                      </div>
                  </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body no-padding">
                   <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th scope="col">Name<span class="column-sorter"></span></th>
                            <th scope="col">Prix<span class="column-sorter"></span></th>
                            <th scope="col">Type<span class="column-sorter"></span></th>
                            <th scope="col">Role<span class="column-sorter"></span></th>
                            <th scope="col">Date <span class="column-sorter"></span></th>
                            <th scope="col" class="pull-right">Actions </th>
                        </tr>
                    </thead>
                    <tbody>
                      @foreach($items as $item) 
                        <tr class="data-item-{{$item->id}} item">
                            <td>
                            {{$item->name}}<br>
                            {{$item->description}}</td>
                            <td>{{$item->cost}}</td>
                            <td>{{$item->type}}</td>
                            <td>{{$item->role}}</td>
                            <td>{{$item->created_at->diffForHumans()}}</td>
                            <td>
                                <div class="btn-group pull-right">
                                    <a href="{{route('admin.plan.edit', $item)}}" class="btn btn-small btn-default btn-update">@lang('app.btn.edit')</a>
                                    <a href="#" class="btn btn-small btn-danger btn-delete"
                                      data-action="delete" 
                                      data-id="{{$item->id}}" 
                                      data-href="{{route('admin.plan.list')}}"><i class="fa fa-trash-o"></i></a>
                                </div>
                            </td>
                        </tr>
                       @endforeach
                    </tbody>
                  </table>
                </div>
                <!-- /.box-body -->
                <div class="box-footer clearfix">
                  {{$items->links()}}
                </div>
              </div>
              <!-- /.box -->
          @else
          <div class="row">
            <div class="col-xs-12">
                <div class="callout callout-info">
                  <h4>@lang('app.empty')</h4>
                </div>
            </div>
         </div>
        @endif
        </div>
    </div>
@endsection

@section('script')
@parent
@include('admin.inc.sweetalert-delete')
@endsection