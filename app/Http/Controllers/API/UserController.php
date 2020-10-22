<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; //for the use of standard SQL statement
use App\User;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::latest()->paginate(1);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $this->validate($request, [
            'name'            => 'required|string|max:15',
            'email'           => 'required|string|email|max:191|unique:users',
            'type'            => 'required',
            'password'        => 'required|string|min:6'
        ],[
            'type.required' => 'Please Select The Role'
        ]            
    );

        return User::create([
            'name'            => $request['name'],
            'email'           => $request['email'],
            'type'            => $request['type'],
            'password'        => Hash::make($request['password'])
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    public function profile(){
        
        return auth('api')->user();
    }
    
    public function updateProfile(Request $request){

        $this->validate($request, [
            'name'            => 'required|string|max:15',
            'photo'           => 'sometimes|required',
            'email'           => 'required|string|email|max:191',
            'type'            => 'required',
            'password'        => 'sometimes|required|string|min:6'
        ],[
            'type.required'   => 'Please Select The Role',
            
        ]            
    );

        $user = auth('api')->user();
        $default_phot = $user->photo;

        if($request->photo != $default_phot){
            $photo_name = time(). '.' . explode('/', explode(':', substr($request->photo,0,strpos($request->photo,';')))[1])[1];
            \Image::make($request->photo)->resize(128,128)->save(public_path('img/profile/').$photo_name);

            $request->merge(['photo' => $photo_name]);

            //find the old photo
            $photo_name_old = public_path('img/profile/').$photo_name;
            if(file_exists($photo_name_old)){
               // @unlink($photo_name_old); 
            }
        }

        if(!empty($request->password)){
            $request->merge(['password' => Hash::make($request['password'])]);
        }

        $user->update($request->all());
        
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        /*add some validations*/
        $user = User::findOrFail($id);
        $user->update($request->all());
        return ['message' => 'User Info Updated'];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->authorize('is_admin');
        $user = User::findOrFail($id);
        //delete the user
        $user->delete();
        return ['message' => 'User Deleted'];
    }

    //search func
    public function search_data(){
        
        if($key = \Request::get('q')){
                $user = User::where(function($query) use ($key){
                    $query->where('name', 'LIKE', "%$key%")
                            ->orWhere('email', 'LIKE', "%$key%")
                            ->orWhere('type', 'LIKE', "%$key%");
             })->paginate(1); 
        }

        return $user;
    }
}
