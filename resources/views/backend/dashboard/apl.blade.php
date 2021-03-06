@extends('layouts.lte')

@section('content')
<!-- Small boxes (Stat box) -->
<div class="row">
    <div class="col-lg-3 col-xs-6">
       @include('components.small-box', [
          'class' =>'bg-green',
          'count' =>$count['customers'],
          'title' =>__('app.customers'),
          'icon'  =>'ion ion-users',
          'link'  =>route('apl.customers'),
       ])
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
       @include('components.small-box', [
          'class' =>'bg-aqua',
          'count' =>$count['orders'],
          'title' =>__('app.orders'),
          'icon'  =>'ion ion-bag',
          'link'  =>route('apl.orders'),
       ])
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
       @include('components.small-box', [
          'class' =>'bg-aqua',
          'count' =>$count['sales'],
          'title' =>__('app.sales'),
          'icon'  =>'ion ion-bag',
          'link'  =>route('apl.sales'),
       ])
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-xs-6">
       @include('components.small-box', [
          'class' =>'bg-yellow',
          'count' =>$count['favorites'],
          'title' =>__('app.favorites'),
          'icon'  =>'ion ion-person-add',
          'link'  =>url('apl/favorites'),
       ])
    </div>
    <!-- ./col -->
</div>
<!-- /.row -->

<!-- Main row -->
<div class="row">
    <!-- Left col -->
    <section class="col-lg-6 connectedSortable">
      <!-- USER LIST -->
      @component('components.box', ['button'=>true, 'class'=>'box-primary'])
          @slot('title')
              Recently Customers
          @endslot
          
          <ul class="users-list clearfix">
            @each('components.list.user', $recent['customers'], 'user')
          </ul>
          
          @slot('footer')
              <a href="{{route('apl.customers')}}" class="uppercase">View All Customers</a>
          @endslot
      @endcomponent
      <!-- PRODUCT LIST -->
      @component('components.box', ['button'=>true, 'class'=>'box-primary'])
          @slot('title')
              Recently Ordered Products
          @endslot
          
          <ul class="products-list product-list-in-box">
            @each('components.list.product', $recent['orders'], 'product')
          </ul>
          
          @slot('footer')
              <a href="{{route('apl.orders')}}" class="uppercase">View All Orders</a>
          @endslot
      @endcomponent
      
      @component('components.box', ['button'=>true, 'class'=>'box-primary'])
          @slot('title')
              Recently Saled Products
          @endslot
          
          <ul class="products-list product-list-in-box">
            @each('components.list.product', $recent['sales'], 'product')
          </ul>
          
          @slot('footer')
              <a href="{{route('apl.sales')}}" class="uppercase">View All Sales</a>
          @endslot
      @endcomponent
      
    </section>
    <!-- Right col -->
    <section class="col-lg-6 connectedSortable">
      @component('components.box', ['button'=>true, 'class'=>'box-primary'])
          @slot('title')
              Favorites Products
          @endslot
          
          <ul class="products-list product-list-in-box">
            @each('components.list.product', $recent['favorites'], 'product')
          </ul>
          
          @slot('footer')
              <a href="{{url(auth()->user()->role.'/favorites')}}" class="uppercase">View All Favorites</a>
          @endslot
      @endcomponent
      @component('components.box', ['button'=>true, 'class'=>'box-primary'])
          @slot('title')
              Searches
          @endslot
          
          <ul class="products-list product-list-in-box">
            @each('components.list.product', $recent['pins'], 'product')
          </ul>
          
          @slot('footer')
              <a href="{{url(auth()->user()->role.'/searches')}}" class="uppercase">View All Searches</a>
          @endslot
      @endcomponent
    </section>
</div>
@endsection