<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Auth;

// Eloquent Model to manage blog's comment
class Comment extends BaseModel
{
   /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'comments';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'content', 'blog_id', 
    ];
    
    /**
     * Create a new model instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->author_id = (Auth::check()?Auth::user()->id:0);
    }
    
    /**
     * Excerpt
     *
     * @param int $length
     * @return String
     */
    public function excerpt($length = 100)
    {
        return substr($this->content, 0, $length);
    }
   
    /**
     * Get the blog who owns comment.
     */
    public function blog()
    {
        return $this->belongsTo(Blog::class, 'blog_id', 'id');
    }
    
    /**
     * Get the author who owns comment.
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id', 'id');
    }
}
