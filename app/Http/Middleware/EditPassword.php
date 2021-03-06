<?php

namespace App\Http\Middleware;

use Closure;

use Auth;
use Session;
use App;
use Route;

class EditPassword
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(!$request->is('*profile/password*')
           &&Auth::check()&&Auth::user()->use_default_password == 1){
            return redirect()->route('password.edit');
        }
        return $next($request);
    }
}
