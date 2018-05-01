<div id="property-sidebar">
    @foreach($pubs as $pub)
    <section class="widget property-meta-wrapper clearfix">
        <h2 class="title wow slideInLeft">{{$pub->title}}</h2>
        <div class="content-box-large box-with-header">
            <a target="_blank" href="{{$pub->links?$pub->links:'#'}}"><img src="{{$pub->imageUrl()}}" class="img-rounded" alt="Cinque Terre" width="604" height="236"></a>
            <br><br>
        </div>
    </section>
    @endforeach
    <section class="widget recent-properties clearfix">
        <h5 class="title">Récents</h5>
        @foreach($products as $product)
        <div class="property clearfix">
            <a href="#" class="feature-image zoom">
                <img data-action="zoom" src="{{asset('images/property/1.jpg')}}" alt="Property Image">
            </a>
            <div class="property-contents">
                <h6 class="entry-title"> <a href="{{route('product.index',['product'=>$product])}}">{{$product->title}}</a></h6>
                <span  class="btn btn-price">${{$product->price}}</span>
            </div>
        </div>
        @endforeach
    </section>
    
    <section class="widget property-taxonomies clearfix">
        <h5 class="title">Types récents</h5>
        <ul class="clearfix">
            @foreach($categories as $category)
            <li><a href="#">{{$category->title}} </a><span class="pull-right">{{count($category->products)}}</span></li>
            @endforeach
        </ul>
    </section>
</div>