<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

class Cart extends BaseModel
{

   /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'carts';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'quantity', 'price', 'currency', 'cart_id', 'product_id', 'author_id'
    ];
    
    protected static $instance = null;
    
	/*
	Si le panier contien déjà quelque chose, on initialise avec les
	actuels
	*/
	public static function getInstance($currentCard){
		if ($currentCard) {
            self::$instance = Cart::find($currentCard->id);
		}
        
        if (!self::$instance) {
            self::$instance = new Cart();
            self::$instance->author_id = (Auth::check()?Auth::user()->id:0);
            self::$instance->status = 'pinged';
            self::$instance->save();
        }
        
        return self::$instance;
	}
    
	/*
	*id du produit et le produit lui même
	*/
	public static function add($product, $apl, $afa){        
        // One product item
        $storedItem = new CartItem();
        $storedItem->quantity = 0;
        $storedItem->price = $product->price;
        $storedItem->cart_id = self::$instance->id;
        $storedItem->afa_id = $afa->id;
        $storedItem->apl_id = $apl->id;
        $storedItem->product_id = $product->id;
        $storedItem->author_id = (Auth::check()?Auth::user()->id:0);
        
        foreach(self::$instance->items as $item){
            if($item->product_id==$product->id){
                if($product->quantity==1){
                    throw new \Exception("Votre carte contient deja ce produit.");
                }else{
                    $storedItem = $item;
                }
                break;
            }
        }
        
		$storedItem->quantity++;
		$storedItem->price = $storedItem->quantity * $product->price;
		$storedItem->tma = $storedItem->price*option(Config::$RESERVATION, 0.10);
		$storedItem->currency = $product->currency;
        $storedItem->save();
        
		self::$instance->totalQuantity++;
		self::$instance->totalPrice += $product->price;
		self::$instance->totalTma += $product->price**option(Config::$RESERVATION, 0.10);
        self::$instance->save();
	}

	public static function reduceByOne($product){
		self::$instance->totalQuantity--;
		self::$instance->totalPrice -= $product->price;
        
        foreach(self::$instance->items as $item){
            if($item->product_id==$product->id){
                $item->quantity--;
                $item->price -= $product->price;
                if($item->quantity>0)
                    $item->save();
                else
                    $item->delete();
                return true;
            }
        }
        
        return false;
	}

	public static function deleteAll($product){
        foreach(self::$instance->items as $item){
            if($item->product_id==$product->id){
                self::$instance->totalQuantity-=$item->quantity;
                self::$instance->totalPrice-=$item->price;
                self::$instance->delete();
                return true;
            }
        }
        return false;
	}
    
    
    /**
     * Set status as ordered
     *
     */
    public function setAsOrdered(Order $order)
    {
        $this->status = 'ordered';
        $this->save();
        
        foreach($this->items as $item){
            $item->status = 'ordered';
            $item->save();
            $item->product->quantity--;
            $item->product->save();
        }
        
        if($this->author){
            $this->author->notify(new OrderPinged($this->author, $order));
        }
        
    }
    
    
    /**
     * Set status as ordered
     *
     */
    public function setAsPaid()
    {
        $this->status = 'paid';
        $this->save();
        
        foreach($this->items as $item){
            $item->status = 'paid';
            $item->save();
        }
    }
    
    /**
     * An many user can have many products from carts_items table
     *
     * @return \Illuminate\Database\Eloquent\Relations\ManyToMany
     */
    public function products()
    {
      return $this->belongsToMany(Product::class, 'carts_items', 'cart_id', 'product_id');
    }
    
    /**
     * A cart can have many items
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
      return $this->hasMany(CartItem::class, 'cart_id', 'id');
    }
    
    /**
     * Get the author record associated with the cart.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }
}