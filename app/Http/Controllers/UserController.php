<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;

use App\Models\User;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the user dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(User $user)
    {
        return view('user.'.$user->role, ['item'=>$user]);
    }

    /**
     * Show the user info in Admin Panel.
     *
     * @param  App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
        if(!in_array($user->role, ['admin', 'member', 'afa', 'apl', 'seller'])){
            return back()->with('error', 'Un probleme a survenu');
        }
        return view('admin.user.'.$user->role, ['item'=>$user]);
    }

    /**
     * Show the user profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        $this->middleware('auth');
        
        return view('user.profile');
    }
    
    /**
     * Render form to edit a User
     *
     * @param  Illuminate\Http\Request  $request
     * @param  App\Models\User  $user
     * @return Illuminate\Http\Response
     */
    public function edit(Request $request, User $user)
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
        
        if($name = $request->old('name')){
            $user->name = $name;
        }
        if($email = $request->old('email')){
            $user->email = $email;
        }
        $action = route('admin.user.update', ['user'=>$user]);
        return view('admin.user.update', ['item'=>$user, 'action'=>$action]);
    }
    
    /**
     * Update User
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
        
        // Validate request
        $validator = Validator::make($request->all(),[
                            'password' => 'confirmed|max:100',
                            'firstname' => 'nullable|max:100',
                            'lastname' => 'nullable|max:100',
                            'genre' => 'nullable|max:100',
                            'phone' => 'nullable|max:100',
                            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                        ]);
        
        if ($validator->fails()) {
            return redirect()->route('admin.user.edit', ['user'=>$user])
                        ->withErrors($validator)
                        ->withInput();
        }
        
        if($firstname = $request->input('firstname'))   $user->update_meta("firstname", $firstname);
        if($lastname = $request->input('lastname'))     $user->update_meta("lastname", $lastname);
        if($genre = $request->input('genre'))           $user->update_meta("genre", $genre);
        if($phone = $request->input('phone'))           $user->update_meta("phone", $phone);
        
        if($file=$request->file('image')){
            $image = $file->store('uploads');
            $user->update_meta("image", $image);
        }
        $user->save();
        return back()->with('success',"L'utilisateur a été bien modifié.");
    }
    
    /**
     * Show the list of user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  String $filter
     * @return \Illuminate\Http\Response
     */
    public function all(Request $request, $filter='all')
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
        
        switch($filter){
            case 'admin':
            case 'apl':
            case 'afa':
            case 'member':
            case 'seller':
                $items = User::ofRole($filter)
                    ->isActive()
                    ->paginate($this->pageSize);
                break;
            case 'person':
            case 'organization':
                $items = User::ofType($filter)
                    ->isActive()
                    ->paginate($this->pageSize);
                break;
            case 'active':
            case 'pinged':
            case 'disabled':
            case 'blocked':
                $items = User::ofStatus($filter)
                    ->paginate($this->pageSize);
                break;
            default:
            case 'all':
                $items = User::paginate($this->pageSize);
                break;
        }
        
        return view('admin.user.all', compact('items')); 
    }
    
    /**
    * Active User
    *
    * @param  \Illuminate\Http\Request  $request
     * @param  App\Models\User  $user
    * @return \Illuminate\Http\Response
    */
    public function active(Request $request,User $user)
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
        if($user->id==1){
            return back()->with('error',"Cette action ne peut pas etre réalisée.");
        }
        
        $user->status = 'active';
        $user->delete();
        return redirect()->route('admin.dashboard')
            ->with('success',"L'utilsateur a été supprimé avec succés");
    }
    
    /**
    * Block User
    *
    * @param  \Illuminate\Http\Request  $request
     * @param  App\Models\User  $user
    * @return \Illuminate\Http\Response
    */
    public function block(Request $request,User $user)
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
        if($user->id==1){
            return back()->with('error',"Cette action ne peut pas etre réalisée.");
        }
        
        $this->middleware('auth');
        $this->middleware('role:admin');
        $user->status = 'blocked';
        $user->save();
        return back()->with('success',"L'utilsateur a été blocké avec succés");
    }
    
    /**
    * Disable User
    *
    * @param  \Illuminate\Http\Request  $request
     * @param  App\Models\User  $user
    * @return \Illuminate\Http\Response
    */
    public function disable(Request $request,User $user)
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
        if($user->id==1){
            return back()->with('error',"Cette action ne peut pas etre réalisée.");
        }
        
        $user->status = 'disabled';
        $user->save();
        return redirect()->route('admin.dashboard')
            ->with('success',"L'utilsateur a été desactivé avec succés");
    }
    
    /**
    * Delete User
    *
    * @param  \Illuminate\Http\Request  $request
     * @param  App\Models\User  $user
    * @return \Illuminate\Http\Response
    */
    public function delete(Request $request,User $user)
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
        if($user->id==1){
            return back()->with('error',"Cette action ne peut pas etre réalisée.");
        }
        
        $user->delete();
        return redirect()->route('admin.dashboard')
            ->with('success',"L'utilsateur a été supprimé avec succés");
    }
}
