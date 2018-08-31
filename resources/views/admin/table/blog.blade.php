<table class="table table-striped table-hover items-list">
    <thead>
        <tr>
            <th scope="col">@lang('app.table.blogs') <span class="column-sorter"></span></th>
            <th scope="col">@lang('app.table.comment') <span class="column-sorter"></span></th>
            <th scope="col">@lang('app.table.meta_tag') <span class="column-sorter"></span></th>
            <th scope="col">@lang('app.table.meta_desc') <span class="column-sorter"></span></th>
            <th scope="col">@lang('app.table.status') <span class="column-sorter"></span></th>
            <th scope="col">@lang('app.table.date') <span class="column-sorter"></span></th>
            <th scope="col" width="200px" class="text-right">@lang('app.table.actions')</th>
        </tr>
    </thead>
    <tbody>
      @foreach($blogs as $blog) 
        <tr class="data-item-{{$blog->id}} item">
            <td>
             <a  href="{{route('blog.index', $blog)}}">
              <div class="item-img">
                <img src="{{$blog->imageUrl()}}" alt="blog Image">
              </div>
              <div class="item-info">
                <span class="item-title">
                    {{$blog->title}}
                </span>
                <span class="item-description">
                  {{$blog->excerpt()}}
                </span>
              </div>
             </a>
            </td>
            <td><a href="{{route('admin.comment.list', $blog)}}">{{$blog->comments_count}}</a></td>
            <td>{{$blog->meta_tag}}</td>
            <td>{{$blog->meta_description}}</td>
            <td>
                 <a class="data-item-status-{{$blog->id}}" href="{{route('admin.blog.list', ['filter'=>$blog->status])}}">
                     @if($blog->status=='published')
                     <span class="label label-success">{{$blog->status}}</span>
                     @else
                     <span class="label label-warning">{{$blog->status}}</span>
                     @endif
                 </a>
            </td>
            <td>{{$blog->created_at->diffForHumans()}}</td>
            <td>
               
                <div class="btn-group pull-right">
                  <a class="btn btn-default btn-status"
                      data-action="{{$blog->status=='published'?'archive':'publish'}}" 
                      data-id="{{$blog->id}}" 
                      data-href="{{route('admin.blog.list')}}">
                          @if($blog->status=='published') 
                              @lang('app.btn.archive') 
                          @else
                            @lang('app.btn.publish') 
                          @endif
                  </a>
                  <a class="btn btn-danger btn-delete" 
                      data-action="delete" 
                      data-id="{{$blog->id}}" 
                      data-href="{{route('admin.blog.list')}}"><i class="fa fa-trash-o"></i></a>
                </div>
            </td>
        </tr>
       @endforeach
    </tbody>
</table>
