<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class AplController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:apl');
    }
    
    /**
     * Liste des ventes en cours
     *
     * @return \Illuminate\Http\Response
     */
    public function orders()
    {
        $items = Auth::user()->orders()
            ->where('status', 'ordered')
            ->paginate($this->pageSize);
        
        return view('apl.order.all')
            ->with('title', __('apl.orders'))
            ->with('items', $items);
    }
    
    /**
     * Liste des ventes effectuées
     *
     * @return \Illuminate\Http\Response
     */
    public function sales()
    {
        $items = Auth::user()->orders()
            ->where('status', 'paid')
            ->paginate($this->pageSize);
        
        return view('apl.order.all')
            ->with('title', __('apl.sales'))
            ->with('items', $items);
    }
    
    /**
     * Liste des clients acheteurs
     *
     * @return \Illuminate\Http\Response
     */
    public function customers()
    {
        $items = Auth::user()->customers()
            ->paginate($this->pageSize);
        
        return view('apl.user.all')
            ->with('title', __('app.customers'))
            ->with('items', $items);
    }
    
    /**
     * Liste des commissions payées ou non
     *
     * @return \Illuminate\Http\Response
     */
    public function commissions($filter = 'paid')
    {
        $items = \Auth::user()->orders()
            ->where(function($query){
                return $query->orWhere('status', 'ordered')
                    ->orWhere('status', 'paid');
            });
        
        switch($filter){
            case 'received':
                $items = $items->whereNotNull('apl_paid_at');
                $title = __('apl.commissions.received');
                break;
            case 'not-received':
                $items = $items->whereNull('apl_paid_at');
                $title = __('apl.commissions.not-received');
                break;
            default:
                abort(404);
                break;
        }
        
        $items = $items->paginate($this->pageSize);
        
        return view('apl.order.all')
            ->with('title', $title)
            ->with('items', $items);
    }
    
}
