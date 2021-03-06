<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Models\PostalCode;

class PostalCodeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }
    
    
    /**
     * Show the list of PostalCode
     * Admin Only
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  String $filter
     * @return \Illuminate\Http\Response
     */
    public function all(Request $request)
    {
        $postalcode = new PostalCode();
        if($value = $request->old('content')) $postalcode->content = $value;

        $action = route('admin.postalcode.store');
        
        return $this->_listAll($request)
            ->with('item', $postalcode)
            ->with('action', $action);
    }
    
    
    /**
     * Render form to create a PostalCode
     *
     * @param  Illuminate\Http\Request  $request
     * @return Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return $this->all($request);
    }

    /**
     * Store a postalcode
     *
     * @param  Illuminate\Http\Request  $request
     * @return Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate request
        $datas = $request->all();
        $validator = Validator::make($datas,[
                            'content' => 'required',
                        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)
                        ->withInput();
        }

        $postalcode = new PostalCode();
        $postalcode->content = $request->content;
        $postalcode->save();
        
        return back()->with('success',"Le code postal a été bien enregistré.");
    }

    /**
     * Render form to edit a $postalcode
     *
     * @param  Illuminate\Http\Request  $request
     * @param  App\Models\PostalCode $postalcode
     * @return Illuminate\Http\Response
     */
    public function edit(Request $request, PostalCode $postalcode)
    {
        if($value = $request->old('content')) $postalcode->content = $value;
        $action = route('admin.postalcode.update', ['postalcode'=>$postalcode]);
        
        return $this->_listAll($request)
            ->with('item', $postalcode)
            ->with('action', $action);
    }

    /**
     * Update $postalcode
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PostalCode $postalcode
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PostalCode $postalcode)
    {
        // Validate request
        $validator = Validator::make($request->all(),[
                            'content' => 'required',
                        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)
                        ->withInput();
        }
        
        $postalcode->content = $request->content;
        $postalcode->save();

        return back()->with('success',"Le code postal a été bien modifié.");
    }

    /**
    * Delete PostalCode $postalcode
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function action(Request $request)
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
        
        // Validate request
        $this->validate($request, [
            'action' => 'required|max:10',
            'data_id'   => 'required|numeric'
        ]);
        
        $postalcode = PostalCode::findOrFail($request->data_id);
        
        $postalcode->delete();
        
        if($request->ajax()){
            return response()->json([
                'status'=>1,
                'message' => "Le code postal a été supprimé avec succés"
            ]);
        }
        
        return redirect()->route('admin.dashboard')
            ->with('success',"Le code postal a été supprimé avec succés");
    }

    /**
     * return view to show list of postal code
     * Admin Only
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    private function _listAll(Request $request)
    {
        $title = __('app.admin.postalcode.list');
        
        $items = new PostalCode();
        
        $record = $request->get('record');
        if(!$record) $record = $this->pageSize;
        
        $q = $request->get('q');
        $q = trim($q);
        if($q){
            $items = $items->where('content', 'LIKE', '%'.$q.'%');
        }
        
        $items = $items->paginate($record);
        
        return view('admin.postalcode.all')
            ->with('q', $q) 
            ->with('record', $record) 
            ->with('title', $title)
            ->with('items', $items); 
    }

}
