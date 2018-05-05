@extends('layouts.app')

@section('content')
<div class="content corps" style="margin-top: 160px;">
    <div class="container">
        <div class="row">
          <div class="col-md-3">
            <div class="sidebar content-box" style="display: block;">
                <ul class="nav nav-side">
                    <li><a href="{{url(Auth::user()->role)}}"><i class="fa fa-tachometer" aria-hidden="true"></i> @lang('app.dashboard')</a></li>
                    <li><a href="{{route('profile')}}"><i class="fa fa-pencil-square" aria-hidden="true"></i> @lang('app.profile')</a></li>
                    @If(Auth::user()->hasRole('member'))
                    <li><a href="{{url('cart')}}"><i class="fa fa-shopping-cart" aria-hidden="true"></i> @lang('app.cart')</a></li>
                    <li><a href="{{url('member/favorites')}}"><i class="fa fa-gratipay" aria-hidden="true"></i> @lang('app.favorites')</a></li>
                    <li><a href="{{url('member/pins')}}"><i class="fa fa-paperclip" aria-hidden="true"></i> @lang('app.pin')</a></li>
                    <li><a href="{{url('member/orders')}}"><i class="fa fa-shopping-basket" aria-hidden="true"></i> @lang('app.order')</a></li>
                    <li><a href="{{url('member/orders/payed')}}"><i class="fa fa-shopping-bag" aria-hidden="true"></i> @lang('app.purchase')</a></li>
                    @elseif(!Auth::user()->isAdmin())
                    <li><a href="{{url(Auth::user()->role.'/products')}}"><i class="fa fa-gratipay" aria-hidden="true"></i> @lang('app.products')</a></li>
                    <li><a href="{{url(Auth::user()->role.'/favorites')}}"><i class="fa fa-gratipay" aria-hidden="true"></i> @lang('app.favorites')</a></li>
                    <li><a href="{{url(Auth::user()->role.'/pins')}}"><i class="fa fa-paperclip" aria-hidden="true"></i> @lang('app.pin')</a></li>
                    @endif
                    @If(Auth::user()->hasRole('afa'))
                    <li><a href="{{url(Auth::user()->role.'/sales')}}"><i class="fa fa-paperclip" aria-hidden="true"></i> @lang('app.sales')</a></li>
                    @endif
                    @If(Auth::user()->hasRole('apl'))
                    <li><a href="{{url(Auth::user()->role.'/sales')}}"><i class="fa fa-paperclip" aria-hidden="true"></i> @lang('app.sales')</a></li>
                    <li><a href="{{url(Auth::user()->role.'/customers')}}"><i class="fa fa-users" aria-hidden="true"></i> @lang('app.customers')</a></li>
                    @endif
                    <li><a href="{{route('logout')}}"><i class="fa fa-sign-out" aria-hidden="true"></i> @lang('app.logout')</a></li>
                </ul>
             </div>
          </div>
          <div class="col-md-8">
              @include('includes.alerts')
              @yield('subcontent')
          </div>
      </div>
  </div>
</div>
@endsection