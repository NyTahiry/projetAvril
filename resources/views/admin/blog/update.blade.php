@extends('layouts.admin')

@section('content')
<div id="main-content" class="main-content container-fluid">
    @include('includes.notification')
    <div class="row-fluid page-head">
        <h2 class="page-title"><i class="aweso-icon-list-alt"></i> 
            @if($item->id>0) 
                @lang('app.admin.blog.update') 
            @else 
                @lang('app.admin.blog.add') 
            @endif
            <small>
            @lang('app.admin.blog.gestion') 
            </small>
        </h2>
    </div>
    <!-- // page head -->
    <div id="page-content" class="row-fluid page-content">
        <form method="post" action="{{$action}}" enctype="multipart/form-data" >
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <section>
                <div class="well well-nice">
                    <h4 class="simple-header">@lang('app.admin.title')</h4>
                    <div class="control-group">
                        <input class="input-block-level" value="{{$item->title}}" name="title" placeholder="@lang('app.admin.title.desc')">
                    </div>
                </div>
                <div class="well well-nice">
                    <h4 class="simple-header">@lang('app.admin.content')</h4>
                    <div class="control-group">
                        <textarea id="wysiBooEditor" class="input-block-level" style="height: 560px" name="content" placeholder="@lang('app.admin.content.desc')">{!!$item->content!!}</textarea>
                    </div>
                </div>
                <div class="row-fluid">
                    <div class="span6">
                        <div class="well well-nice">
                            <h4 class="simple-header">@lang('app.admin.metatag')</h4>
                            <div class="control-group">
                                <textarea class="input-block-level" style="height: 120px" name="meta_tag" placeholder="@lang('app.admin.metatag.desc')">{{$item->meta_tag}}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="span6">
                        <div class="well well-nice">
                            <h4 class="simple-header">@lang('app.admin.metadesc')</h4>
                            <div class="control-group">
                                <textarea id="wysiBooEditor" class="input-block-level" style="height: 120px" name="meta_description" placeholder="@lang('app.admin.metadesc.desc')">{{$item->meta_description}}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="well well-nice">
                    <h4 class="simple-header">@lang('app.admin.category')</h4>
                        @foreach($categories as $category)
                        <div class="control-group">
                            <input type="checkbox" name="category[]" value="{{$category->id}}"  {{in_array($category->id, $categoryIds)?'checked':''}}> {{$category->title}}
                        </div>
                        @endforeach
                </div>
                <div class="page-header">
                    <h3><i class="fontello-icon-monitor opaci35"></i> @lang('app.admin.file')</h3>
                </div>
                <div class="row-fluid">
                    <div class="span6">
                        <div class="well well-nice inline">
                            <div class="fileupload fileupload-new" data-provides="fileupload">
                                <div class="fileupload-preview thumbnail" style="width: 200px; height: 120px;">
                                    <img src="{{$item->imageUrl()}}">
                                </div>
                                <div> 
                                    <span class="btn btn-file"> 
                                        <span class="fileupload-new">@lang('app.admin.file.select')</span> 
                                        <span class="fileupload-exists">@lang('app.admin.file.change')</span>
                                        <input type="file" name="image" id="file">
                                    </span> 
                                    <a href="#" class="btn fileupload-exists" data-dismiss="fileupload">@lang('app.admin.file.remove')</a> </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-actions no-margin-bootom">
                    <button type="submit" class="btn btn-green">@lang('app.btn.save')</button>
                    <button class="btn cancel" type="reset">@lang('app.btn.reset')</button>
                    <a href="javascript:history.back()" class="btn btn-green pull-right" type="submit">@lang('app.btn.back')</a>
                </div> 
            </section>
        </form>
    </div>
</div>
@endsection
