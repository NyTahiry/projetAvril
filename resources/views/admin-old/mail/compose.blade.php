@extends('layouts.admin')

@section('content')
<div id="main-content" class="main-content container-fluid">
    <div class="row-fluid page-head">
        <h2 class="page-title">@lang('app.message')</h2>
    </div>
    @include('includes.alerts')
    <div class="">
        <div class="widget widget-simple">
            <div class="widget-content">
                <div class="widget-body">
                    <form action="{{route('admin.mail.compose')}}" method="post" id="commentform" class="contact-form" >
                        {{ csrf_field() }}
                        <ul class="form-list label-left list-bordered dotted" style="padding:0px;">
                            <li class="control-group">
                                <label for="role">Selectionner ROLE</label>
                                <div class="controls">
                                    <select id="role" name="role" class="selecttwo input-block-level">
                                        <option {{old('role')&&old('role')=='0'?'selected':''}} value="0">@lang('app.select_role')</option>
                                        <option {{old('role')&&old('role')=='admin'?'selected':''}} value="admin">Admin</option>
                                        <option {{old('role')&&old('role')=='apl'?'selected':''}} value="apl">APL</option>
                                        <option {{old('role')&&old('role')=='afa'?'selected':''}} value="afa">AFA</option>
                                        <option {{old('role')&&old('role')=='member'?'selected':''}} value="member">Member</option>
                                        <option {{old('role')&&old('role')=='seller'?'selected':''}} value="seller">Seller</option>
                                    </select>
                                </div>
                            </li>
                            <li class="control-group">
                                <label for="users">Selectionner utilisateurs</label>
                                <div class="controls">
                                    <select id="users" name="users[]" class="selecttwo input-block-level" multiple>
                                        <option value="0">@lang('app.select_user')</option>
                                        @foreach($users as $user)
                                            <option value="{{$user->id}}">{{$user->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </li>
                            <li class="control-group">
                                <label for="subject" class="control-label">@lang('app.subject')</label>
                                <div class="controls">
                                    <input id="subject" class="input-block-level" name="subject" type="text" placeholder="@lang('app.subject') *" aria-required="true" required="required" value="{{$item->subject}}">
                                </div>
                            </li>
                            <li class="control-group">
                                <label for="message">@lang('app.message')</label>
                                <div class="controls">
                                    <textarea id="message" class="input-block-level ckeditor" rows="10" name="content" placeholder="@lang('app.message')" >{{$item->content}}</textarea>
                                </div>
                            </li>
                        </ul>
                        <div class="form-actions no-margin-bootom">
                            <button type="submit" class="btn btn-green" name="method" value="send">@lang('app.btn.send')</button>
                            <button type="submit" class="btn pull-right" name="method" value="draft">@lang('app.btn.draft')</button>
                            <button type="submit" class="btn btn-blue pull-right" name="method" value="model">@lang('app.btn.save_as_model')</button>
                        </div> 
                    </form>
                </div>
            </div>
        </div>
        <!-- // Widget -->
    </div>
</div>
@endsection
