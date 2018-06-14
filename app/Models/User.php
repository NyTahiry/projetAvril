<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use AstritZeqiri\Metadata\Traits\HasManyMetaDataTrait;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    use Notifiable;
    use Billable;
    use HasManyMetaDataTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'image_id', 'location_id', 'status', 'type', 'role', 
        'activation_code', 
        'use_default_password',
        'trial_ends_at',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be a date
     *
     * @var array
     */
    protected $dates = ['trial_ends_at', 'subscription_ends_at'];
    
    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $events = [
        //'saved' => UserSaved::class,
        //'deleted' => UserDeleted::class,
    ];
    
    /**
     * Route notifications for the mail channel.
     *
     * @return string
     */
    public function routeNotificationForMail()
    {
        return 'joelinjatovo@gmail.com';
    }

    /**
     * The channels the user receives notification broadcasts on.
     *
     * @return string
     */
    /*
    public function receivesBroadcastNotificationsOn()
    {
        return 'users.'.$this->id;
    }
    */
    
    /**
     * Scope a query to only include users of a given $role.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $role
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfRole($query, $role)
    {
        return $query->where('role', $role);
    }
    
    /**
     * Scope a query to only include users of a given $type.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $type
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }
    
    /**
     * Scope a query to only include users of a given $status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfStatus($query, $status)
    {
        return $query->where('status', $status);
    }
    
    /**
     * Scope a query to only include users is active
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsActive($query)
    {
        return $query->where('status', 'active');
    }
    
    /**
     * Scope a query to only include users has Location
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeHasLocation($query)
    {
        return $query->where('location_id', '>', '0');
    }
    
    /**
     * Is current user can contact $user
     *
     * @return Boolean
     */
    public function canContact(User $user)
    {
       if($this->isAdmin())
           return true;
        
        if(!$user->active())
            return false;
        
        
        if($this->hasRole('afa')){
            return !$user->hasRole('member');
        }
        
        if($this->hasRole('seller')){
            return !$user->hasRole('member');
        }
        
        
        if($this->hasRole('member')){
            if($user->hasRole('apl')){
                return $this->apl && ($this->apl->id == $user->id);
            }
            
            return $user->hasRole('admin');
        }
        
        if($this->hasRole('apl')){
            if($user->hasRole('member')){
                return $user->apl && ($user->apl->id == $this->id);
            }
            
            return true;
        }
    }
    
    /**
     * Is user active
     *
     * @return Boolean
     */
    public function active()
    {
      return ($this->status == 'active');
    }
    
    /**
     * Is user online
     *
     * @return Boolean
     */
    public function isOnline()
    {
      return $this->sessions()->activity()->exists();
    }
    
    /**
     * Is user admin
     *
     * @return Boolean
     */
    public function isAdmin()
    {
      return $this->hasRole('admin');
    }
    
    /**
     * A user is admin || AFA || APL || member
     *
     * @return Boolean
     */
    public function hasRole($role)
    {
      return ($this->role == $role);
    }
    
    /**
     * A user is person
     *
     * @return Boolean
     */
    public function isPerson()
    {
      return $this->hasRole('member')&&($this->type=='person');
    }
    
    /**
     * A user is member and has apl
     *
     * @return Boolean
     */
    public function hasApl()
    {
      return ($this->hasRole('member')&&$this->apl);
    }
    
    /**
     * Get Url of Attached Image OR Default Image
     *
     * @param Boolean $thumb
     * @return String
     */
    public function imageUrl($thumb=false)
    {
        // Image is setted
        if($this->image){
            if($thumb) return thumbnail($this->image->filepath);
            return storage($this->image->filepath);
        } 
        return asset('images/avatar.png');
    }
    
    /**
     * A user can have one image
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function image()
    {
      return $this->hasOne(Image::class, 'id', 'image_id');
    }
    
    /**
     * A user can have one parent
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function author()
    {
      return $this->hasOne(User::class, 'id', 'author_id');
    }
    
    /**
     * A user can have one default APL
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function apl()
    {
      return $this->hasOne(User::class, 'id', 'apl_id');
    }
    
    /**
     * A user can have one location
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function location()
    {
      return $this->hasOne(Localisation::class, 'id', 'location_id');
    }
    
    /**
     * A user can have many session
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sessions()
    {
      return $this->hasMany(Session::class, 'user_id', 'id');
    }
    
    /**
     * A user can have many observation
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function observations()
    {
      return $this->hasMany(Observation::class, 'user_id', 'id');
    }
    
    /**
     * A user can have many messages
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages()
    {
      return $this->hasMany(Message::class, 'user_id', 'id');
    }
    
    /**
     * An admin user can have many blogs
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function blogs()
    {
      return $this->hasMany(Blog::class, 'author_id', 'id');
    }
    
    
    /**
     * A user can have many comments
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
      return $this->hasMany(Comment::class, 'author_id', 'id');
    }
    
    /**
     * An admin can have many products
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function adminProducts()
    {
      return $this->hasMany(Product::class, 'author_id', 'id');
    }
    
    /**
     * A seller can have many products to sell
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
      return $this->hasMany(Product::class, 'seller_id', 'id');
    }
    
    /**
     * An APL can have many clients
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function customers()
    {
      return $this->hasMany(User::class, 'apl_id', 'id');
    }
    
    /**
     * An Client can have many carts
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function carts()
    {
      return $this->hasMany(Cart::class, 'author_id', 'id');
    }
    
    /**
     * An many user can have many products from labels table
     *
     * @return \Illuminate\Database\Eloquent\Relations\ManyToMany
     */
    public function pins()
    {
      return $this->belongsToMany(Product::class, 'labels', 'author_id', 'product_id')
          ->wherePivot('label', 'saved');
    }
    
    /**
     * An many user can have many products from labels table
     *
     * @return \Illuminate\Database\Eloquent\Relations\ManyToMany
     */
    public function favorites()
    {
      return $this->belongsToMany(Product::class, 'labels', 'author_id', 'product_id')
          ->wherePivot('label', 'starred');
    }
    
    /**
     * An many afa/apl can have many products from carts_items table
     *
     * @return \Illuminate\Database\Eloquent\Relations\ManyToMany
     */
    public function sales()
    {
        if($this->hasRole('afa')){
            return $this->belongsToMany(Product::class, 'carts_items', 'afa_id', 'product_id');
        }
        // else APL
        return $this->belongsToMany(Product::class, 'carts_items', 'apl_id', 'product_id');
    }
    
    /**
     * An many clients can buy many products from carts_items table
     *
     * @return \Illuminate\Database\Eloquent\Relations\ManyToMany
     */
    public function purchases()
    {
      return $this->belongsToMany(Product::class, 'carts_items', 'author_id', 'product_id');
    }
    
    /**
     * An user can have many mails with mails_users pivot table
     *
     * @return \Illuminate\Database\Eloquent\Relations\ManyToMany
     */
    public function mails()
    {
      return $this->belongsToMany(Mail::class, 'mails_users', 'user_id', 'mail_id');
    }
    
    /*
    * Alias to get_meta()
    *
    */
    public function meta($key, $default = ''){
        $meta = $this->get_meta($key);
        if(!$meta) return $default;

        return $meta->value;
        
    }
    
}
