<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

use App\Models\Category;
use App\Models\Product;
use App\Models\Search;

class SearchController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Perform global search.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Category $category = null)
    {
        $items = Product::ofStatus('published');
        
        $search = new Search();
        
        if($request->state){
            $items = $items->where('state_id', $request->state);
        }
        
        if($request->type){
            $items = $items->where('type_id', $request->type);
        }
        
        if($request->location_type){
            $items = $items->where('location_type_id', $request->location_type);
        }
        
        if($request->price){
            switch($request->price){
                case 1:
                    $sign = '<';
                    $price = 100000;
                    break;
                case 2:
                    $sign = '<';
                    $price = 200000;
                    break;
                case 3:
                    $sign = '<';
                    $price = 300000;
                    break;
                case 4:
                    $sign = '>';
                    $price = 100000;
                    break;
                case 5:
                    $sign = '>';
                    $price = 200000;
                    break;
                case 6:
                    $sign = '>';
                    $price = 300000;
                    break;
                default:
                    $sign = '>';
                    $price = 0;
                    break;
            }
            $items = $items->where('price', $sign, $price);
        }
        
        if($request->area){
            switch($request->area){
                case 1:
                    $sign = '<';
                    $area = 100;
                    break;
                case 2:
                    $sign = '<';
                    $area = 250;
                    break;
                case 3:
                    $sign = '<';
                    $area = 500;
                    break;
                case 4:
                    $sign = '>';
                    $area = 100;
                    break;
                case 5:
                    $sign = '>';
                    $area = 250;
                    break;
                case 6:
                    $sign = '>';
                    $area = 500;
                    break;
                default:
                    $sign = '>';
                    $area = 0;
                    break;
            }
            $items = $items->where('area', $sign, $area);
        }
        
        if($request->q){
            $items = $items->where('title', 'LIKE', '%'.$request->q.'%');
            $search->keyword = $request->q;
        }

        $items = $items->paginate(20);
        
        $search->content = serialize($request->all());
        $search->save();
        
    	return view('search.index')
            ->with('items', $items);
    }

    
    public function edit(Request $request){
        $this->middleware('auth');
        
        $id = $request->search;
        $search = Search::find($id);
        if(!$search){
            return response()->json([
                'state'=>0,
                'code' => 'invalid_param',
                'message' => 'Object not found'
            ]);
        }
        
        if($search->author && ($search->author->id != \Auth::user()->id)){
            return response()->json([
                'state'=>0,
                'code' => 'unauthorized',
                'message' => 'Vous n\'etes pas autorise a modifier cette recherche.'
            ]);
        }
        
        $title = $request->title;
        if(empty($title)){
            return response()->json([
                'state'=>0,
                'code' => 'invalid_param',
                'message' => 'Le titre ne peut pas etre vide.'
            ]);
        }
        
        if(!$search->author){
            $search->author_id = \Auth::user()->id;
        }

        $search->title = $title;
        $search->save();
        
        return response()->json([
                'state'=>1,
                'code' => 'success',
                'message' => 'Modification avec succes.'
        ]);
    }
    
    public function delete(Request $request){
        $this->middleware('auth');
        
        $id = $request->search;
        $search = Search::find($id);
        if(!$search){
            return response()->json([
                'state'=>0,
                'code' => 'invalid_param',
                'message' => 'Object not found'
            ]);
        }
        
        if(!$search->author || ($search->author->id != \Auth::user()->id)){
            return response()->json([
                'state'=>0,
                'code' => 'unauthorized',
                'message' => 'Vous n\'etes pas autorise a modifier cette recherche.'
            ]);
        }
        
        $search->delete();
        
        return response()->json([
                'state'=>1,
                'code' => 'success',
                'message' => 'Suppression avec succes.'
        ]);
    }
}
