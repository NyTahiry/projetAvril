<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class State extends BaseModel
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'states';
	
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['content'];
    
    /**
     * Create a new model instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
}