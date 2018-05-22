<table class="table boo-table table-striped table-hover">
 <thead>
     <tr>
       <th scope="col">
         <label class="checkbox">
             <input class="checkbox" type="checkbox" value="option1">
         </label>
       </th>
         <th scope="col">@lang('app.table.id') <span class="column-sorter"></span></th>
         <th scope="col">@lang('app.table.photo') <span class="column-sorter"></span></th>
         <th scope="col">@lang('app.table.title')/@lang('app.table.content') <span class="column-sorter"></span></th>
         <th scope="col">@lang('app.table.price')/@lang('app.table.tma') <span class="column-sorter"></span></th>
         <th scope="col">@lang('app.table.date') <span class="column-sorter"></span></th>
         <th scope="col">@lang('app.table.status') <span class="column-sorter"></span></th>
         <th scope="col">@lang('app.table.seller') <span class="column-sorter"></span></th>
         <th scope="col">@lang('app.table.author') <span class="column-sorter"></span></th>
         <th scope="col">@lang('app.table.actions') </th>
     </tr>
 </thead>
 <tbody>
     @foreach($products as $product)
     <tr>
         <td>
           <label class="checkbox">
               <input class="checkbox" type="checkbox" value="option1">
           </label>
         </td>
         <td>{{$product->id}}</td>
         <td>
             <a href="{{route('admin.product.show', ['product'=>$product])}}"><img class="thumb" src="{{$product->imageUrl()}}" width="50"></a>
         </td>
         <td>
             <a href="{{route('admin.product.show', ['product'=>$product])}}">{{$product->title}}</a><br>
             {{$product->excerpt()}}
         </td>
         <td>{{$product->currency}} {{$product->price}} / {{$product->tma}}</td>
         <td>{{$product->created_at->diffForHumans()}}</td>
         <td>
             <a href="{{route('admin.product.list', ['filter'=>$product->status])}}">
                 @if($product->status=='published')
                 <span class="label label-success">{{$product->status}}</span>
                 @else
                 <span class="label label-warning">{{$product->status}}</span>
                 @endif
             </a>
         </td>
         <td>
             @if($product->seller)
             <a href="{{route('admin.user.show', $product->seller)}}">{{$product->seller->name}}</a>
             @endif
         </td>
         <td>
             @if($product->author)
             <a href="{{route('admin.user.show', $product->author)}}">{{$product->author->name}}</a>
             @endif
         </td>
         <td>
         @if($product->status=='pinged' || $product->status=='archived')
            <a href="{{route('admin.product.publish', $product)}}" class="btn btn-small btn-success btn-publish">@lang('app.btn.publish')</a>
            <a href="{{route('admin.product.trash', $product)}}" class="btn btn-small btn-info btn-trash">@lang('app.btn.trash')</a>
         @elseif($product->status=='trashed')
            <a href="{{route('admin.product.restore', $product)}}" class="btn btn-small btn-info btn-restore">Restore</a>
         @endif
         @if($product->status=='published')
            <a href="{{route('admin.product.archive', $product)}}" class="btn btn-small btn-default  btn-archive">@lang('app.btn.archive')</a>
            <a href="{{route('admin.product.trash', $product)}}" class="btn btn-small btn-info btn-trash">@lang('app.btn.trash')</a>
         @endif
            <a href="{{route('admin.product.delete', $product)}}" class="btn btn-small btn-warning btn-delete">@lang('app.btn.delete')</a>
         </td>
     </tr>
     @endforeach
 </tbody>
</table>