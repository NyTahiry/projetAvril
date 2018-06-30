<table class="shop_table shop_table_responsive cart table table-striped table-hover">
    <thead>
        <tr>
            <th colspan="2">User</th>
            @if(\Auth::check()&&\Auth::user()->hasRole('apl'))
            <th>Date d'éxpiration</th>
            @endif
            <th class="pull-right">Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
        <tr>
            <td class="product-thumbnail" width="100">
                <img width="100" height="100" src="{{$user->imageUrl()}}" class="attachment-woocommerce_thumbnail size-woocommerce_thumbnail wp-post-image" alt="" />
            </td>
            <td>
                {{$user->name}}<br>
                {{$user->email}}
            </td>
            @if(\Auth::check()&&\Auth::user()->hasRole('apl'))
            <td>
                {{$user->apl_ends_at?ucfirst($user->apl_ends_at->diffForHumans()):''}}
            </td>
            <td class="product-action">
                <a class="btn btn-default pull-right"  href="{{route(\Auth::user()->role.'.user.contact', $user)}}">@lang('apl.contact_customer')</a>
            </td>
            @endif
        </tr>
        @endforeach
    </tbody>
</table>