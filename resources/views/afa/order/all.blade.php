@extends('layouts.lte')

@section('content')
<div class="row">
    <div class="col-md-12">
      @component('components.box', ['button'=>true, 'class'=>'box-primary'])
          <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th colspan="2">Produits</th>
                    <th>Prix</th>
                    <th>Commission PC</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @if(count($items)>0)
                    @each('afa.order.tr', $items, 'order')
                @else
                    @include('afa.tr-empty', ['col'=>7])
                @endif
            </tbody>
          </table>
          
          @slot('footer')
              {{$items->links()}}
          @endslot
          
      @endcomponent
    </div>
</div>
@endsection
