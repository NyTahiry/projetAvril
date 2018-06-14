<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use Auth;

use App\Notifications\OrderPinged;

use App\Models\Product;
use App\Models\CartItem;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Page;
use App\Models\Pub;
use App\Models\User;
use App\Models\Type;
use App\Models\State;

class ShopController extends Controller
{
    
    /**
     * Show the list of product.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  String $category
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Category $category = null)
    {
        $page = $request->get('page');
        if(empty($page)) $page = 1;
        
        $orderBy = $request->get('orderBy');
        if(!in_array($orderBy, ['price', 'created_at', 'view_count'])) $orderBy = 'price';
        
        $order = $request->get('order');
        if(!in_array($order, ['desc', 'asc'])) $order = 'desc';
        
        $items = Product::ofStatus('published')
                ->where('quantity', '>', 0);
        
        if($category&&$category->id>0){
            $items = $items->where("category_id", $category->id);
        }
        
        $q = $request->q;
        if($q){
            $items = $items->where(function($query) use ($q){
                return $query->where('content', 'LIKE', '%'.$q.'%')
                    ->orWhere('title', 'LIKE', '%'.$q.'%');
            });
        }
        
        $items = $items->orderBy($orderBy, $order);

        $items = $items->paginate($this->pageSize);
        
        if($request->ajax()){
            return response()->json(array(
                'html' => view('ajax.product.all', compact('items'))->render()
            ));
        }
        
        $products = Product::orderBy('created_at','desc')
            ->ofStatus('published')
            ->take($this->recentSize)
            ->get();
        
        $categories = Category::orderBy('created_at', 'desc')
            ->has('products')
            ->withCount('products')
            ->take($this->recentSize)
            ->get();
        
        $page2 = Page::where('path', '=', '/products*')->first();
        if($page2){$pubs = $page2->pubs;}else{$pubs=[];}

        
        $types = Type::orderBy('title', 'asc')
            ->where('object_type', 'type')
            ->get();
        
        $locationTypes = Type::orderBy('title', 'asc')
            ->where('object_type', 'location')
            ->get();
        
        $states = State::orderBy('content', 'asc')
            ->get();
        
        return view('shop.index')
            ->with('items', $items)
            ->with('q', $q)
            ->with('orderBy', $orderBy)
            ->with('order', $order)
            ->with('page', $page)
            ->with('pubs', $pubs)
            ->with('products', $products)
            ->with('types', $types)
            ->with('locationTypes', $locationTypes)
            ->with('states', $states)
            ->with('category', $category)
            ->with('categories', $categories); 
    }
    
    /**
     * Select Apl for an product
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product
     * @return \Illuminate\Http\Response
     */
    public function selectApl(Request $request, Product $product){
        $this->middleware('auth');
        $this->middleware('role:member');
        
        $distance = $request->get('distance');
        if(empty($distance)) $distance = 100;
        
        $data = [];
        if($product && $product->id>0){
            if(!$product->isDisponible()){
               return redirect()->route('product.index', $product)
                   ->with('error','Stock en rupture');
            }

            if(!$product->location){
               return redirect()->route('product.index', $product)
                    ->with('error','Le systeme ne peut pas localiser le produit');
            }
            
            $data[] = [
              'id' => $product->id,
              'lat' => $product->location?$product->location->latitude:0,
              'lng' => $product->location?$product->location->longitude:0,
              'title' => $product->title,
              'type' => 'product',
            ];
        }else{
            
        }
        
        $apls = User::ofRole('apl')
            ->isActive()
            ->has('location')
            ->with('location')
            ->get();
        
        $userApl = Auth::user()->apl;
        
        $selected = null;
        
        foreach($apls as $item){
            $dataTemp = [
              'id' => $item->id,
              'lat' => $item->location?$item->location->latitude:0,
              'lng' => $item->location?$item->location->longitude:0,
              'title' => $item->name,
              'content' => $item->get_meta('orga_description')?$item->get_meta('orga_description')->value:'',
              'type' => $item->role,
            ];
            
            $data[] = $dataTemp;
            
            if($userApl && ($item->id == $userApl->id)){
                $selected = $dataTemp;
            }
        }
        
        
        $action = route('shop.add', $product);
    	return view('backend.apl.select')
            ->with('action', $action)
            ->with('location', Auth::user()->location)
            ->with('items', $apls)
            ->with('item', $product)
            ->with('distance', $distance)
            ->with('distances', $this->distances)
            ->with('selected', json_encode($selected))
            ->with('data', json_encode($data));
    }
    
    /**
     * Add product in cart
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request, Product $product){
        $this->middleware('auth');
        $this->middleware('role:member');
        
        if(!$product->isDisponible()){
    	   return redirect()
               ->route('product.index', $product)
               ->withInput()->with('error','Stock en rupture');
        }
        
        $apl = null;
        if($request->has('apl')){
            $apl = User::ofRole('apl')
                ->isActive()
                ->where('id', '=', $request->apl)
                ->first();
        }
        
        // No APL selected
        if(!$apl && !Auth::user()->apl){
    	   return redirect()->route('product.index', $product)
               ->withInput()
               ->with('error','Vous devez choisir un apl.');
        }
        
        // Update APL
        if($apl && $request->input('is_default')){
            Auth::user()->apl_id = $apl->id;
            Auth::user()->save();
        }
        
        // Get Default APL if no APL chosen
        if(!$apl || $apl->id==0){
            $apl = Auth::user()->apl;
        }
        
        // Get AFA
        if($product->location){
            $afas = User::ofRole('afa')->isActive()
                ->hasLocation()->get();
            
            $dists = [];
            $value = 10000000000;
            foreach($afas as $item){
                if($item->location){
                    $dist = $product->location->getDistance($item->location);
                    if($dist<=$value){
                        $value = $dist;
                        $afa = $item;
                    }
                }
            }
        }
        
        if(!isset($afa) || $afa->id==0){
    	   return redirect()->route('product.index', $product)
               ->withInput()
               ->with('error','Vous ne pouvez pas encore faire cet achat. Il n\'y a pas d\'agence dans la base');
        }
        
    	$currentCart = Session::has('cart') ? Session::get('cart') : null;
    	$cart = Cart::getInstance($currentCart);
        
        try{
            $cart->add($product, $apl, $afa);
        }catch(\Exception $e){
            return redirect()->route('product.index', $product)
                ->with('error', $e->getMessage());
        }
        

    	Session::put('cart', $cart);
    	Session::save();

    	return redirect()->route('product.index', $product)
            ->with('success', 'Nouvel article ajoute au panier!');
    }

    /**
     * Show cart
     *
     * @return \Illuminate\Http\Response
     */
    public function cart(){
        $this->middleware('auth');
        $this->middleware('role:member');
        
        $currentCart = Session::has('cart') ? Session::get('cart') : null;
        $cart = Cart::getInstance($currentCart);

        return view('shop.cart')->with(['item' => $cart]);
    }

    public function getCheckout(){
        $this->middleware('auth');
        $this->middleware('role:member');
        
        if (!Session::has('cart')) {
            return redirect()->route('profile')
                ->with('error', 'Votre carte est encore vide.');
        }
        
        return view('shop.checkout');
    }

    public function postCheckout(Request $request){
        $this->middleware('auth');
        $this->middleware('role:member');
        
        $this->validate($request, [
            'stripe_token' => 'required',
        ]);

        
        $user = Auth::user();
        
        if (!Session::has('cart')) {
            return redirect()->route('profile')
                ->with('error', 'Votre carte est encore vide.');
        }
        
        $currentCart = Session::has('cart') ? Session::get('cart') : null;
        $cart = Cart::getInstance($currentCart);
        
        if (count($cart->items)<=0) {
            return redirect()->route('profile')
                ->with('error', 'Votre carte est encore vide.');
        }

        $total = $cart->totalTma;
        $currency = $cart->currency;
        
        // Get the submitted Stripe token
        $token = $request->stripe_token;

        // If empty stripe_id then create new customer
        if (empty($user->strip_id)) {
            // Create a new Stripe customer
            try {
                $customer = \Stripe\Customer::create([
                'source' => $token,
                'email' => $user->email,
                'metadata' => [
                    "First Name" => $user->name,
                    "Last Name" => $user->name
                ]
                ]);
            } catch (\Stripe\Error\Card $e) {
                return redirect()->to($action)
                    ->withErrors($e->getMessage())
                    ->withInput();
            }

            // Update user in the database with Stripe
            $user->stripe_id = $customer->id;
            $user->save();

        }

        try{
            // Create the charge
            $result = \Stripe\Charge::create(array(
                "amount" => $total,
                "currency" => "eur",
                "customer" => $user->stripe_id,
                "description" => 'Purchase'
            ));
            
        }catch(\Exception $e){
            return back()->with('error', $e->getMessage());
        }
        
        if ($result->status != 'succeeded') {
          return back()->with('error', "Votre commande n'a pas été éffectué. ".$result->message);
        }
    
        // Set as order and notify user
        $cart->setAsOrdered();

        Session::forget('cart');
        
        //do some other stuffs
        return redirect()->route('subscription.success');
    }

    public function reduceByOne(Product $product){
        $this->middleware('auth');
        $this->middleware('role:member');

        $currentCart = Session::has('cart') ? Session::get('cart') : null;
        $cart = Cart::getInstance($currentCart);
        $cart->reduceByOne($product);

        Session::put('cart', $cart);
        Session::save();

        if (count($cart->items) <= 0) {
            Session::forget('cart');
        }

        return redirect()->route('shop.cart')->with('success', "L'article a bien été supprimé !");
    }

    public function deleteAll(Product $product){
        $this->middleware('auth');
        $this->middleware('role:member');
        
        $currentCart = Session::has('cart') ? Session::get('cart') : null;
        $cart = Cart::getInstance($currentCart);
        $cart->deleteAll($product);

        Session::put('cart', $cart);
        Session::save();

        if (count($cart->items) <= 0) {
            $cart->delete();
            Session::forget('cart');
        }

        return redirect()->route('shop.cart')->with('success', "L'article a bien été supprimé !");
    }

}
