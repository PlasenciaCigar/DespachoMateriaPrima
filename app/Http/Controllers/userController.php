<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\role;
use App\Models\Procedencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class userController extends Controller
{
    public function create()
    {

    }


   

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:25',
            'email' => 'required|unique:users,email|max:100',
            'password' => 'required|max:25',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'sucursal' => $request->procedencia,
        ]);

       /* $verusuario = new User;
        $verusuario->name = $request->input('name');
        $verusuario->email = $request->input('email');
        $verusuario->password = $request->input('password');
        $verusuario->sucursal = $request->input('procedencia');

        $nuevo =$verusuario->save();*/
        $parametro = $request->roles;
        if( $parametro == 'Analista'){
            $user->assignRole('Analista');
        }else if($parametro == 'Admin'){
            $user->assignRole('Admin'); 
        }else if($parametro == 'Sub-Admin'){
            $user->assignRole('Sub-Admin'); 
        }else if($parametro == 'Pilonero'){
            $user->assignRole('Pilonero'); 
        }else if($parametro == 'Sub-Pilonero'){
            $user->assignRole('Sub-Pilonero'); 
        }
        
    
        return redirect('/verusuario/index');
        
        }
    /**
     * Display a listing of the resource. 
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $categoria = $request->get('filtro');
        if($categoria==null){
            $categoria="users.name";
        }
       
       // $verusuario = User::all();
        //$rol = User::find(2)->role;
       // $suc= Auth::user()->sucursal;
        $caracteres = $request->get('busqueda');
        $verusuario = DB::table('users')
        ->join('model_has_roles', 'model_has_roles.model_id','=',
         'users.id')
         ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
         ->join('procedencias', 'procedencias.id', '=', 'users.sucursal')
         ->where("$categoria", 'like', "%$caracteres%")
         ->select('users.*','users.id as sd', 'model_has_roles.*', 'roles.name as rol', 'procedencias.*')->get();
        return view ('Usuarios.Verususarios',['Usuarios'=>$verusuario]); 
         
    }
    public function destroy( $verusuario)
    {
        {

            try {
        $verusuario =User:: where ('email','=', $verusuario)->first();
        $verusuario->delete();
        return redirect('/verusuario/index')->with('Eliminar', 'Ok.');
    }
 
    catch (\Throwable $th) {
    return redirect('/verusuario/index')->with('Eliminar', 'No.');
      }
    }
  }
    public function show($verusuario)
    {
      //  $id= Crypt::decrypt($id);
      $role = Role::all();
      $procedencia = Procedencia::all();
        $verusuarios = DB::table('users')
        ->join('procedencias', 'procedencias.id', '=', 'users.sucursal')
        ->join('model_has_roles', 'model_has_roles.model_id','=', 'users.id')
        ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
        ->where('users.id', '=', $verusuario)->select('users.*', 'procedencias.id as procedencia_id', 'procedencias.nombre as procedencia', 'roles.name as rol')
        ->first();
        return view('auth.register', ['register'=>$verusuarios, 'procedencia'=>$procedencia, 'role'=>$role]);
        //return view('Auth.register')->with('register',$verusuarios) ('procedencia',$procedencia) ('role',$role);
    } 
 

    public function ver()
    {
        $role = Role::all();
        $procedencia = Procedencia::all();
        return view('auth.register', ['procedencia'=>$procedencia, 'role'=>$role]);
       // return view('Auth.register')->with('procedencia',$procedencia);
        
    }

    public function update(Request $request, $verusuario)
    {
        $verusuario =User:: where ('id','=', $verusuario)->first();
        $this->validate($request, [
            'name' => 'required|max:25',
            'email'=>'required|max:100',
            'password'=>'required|max:100',
        ]);
        $verusuario->name = $request->input('name');
        $verusuario->email = $request->input('email');
        if (strlen($request->input('password'))>25) {
            # code...
        }else{
            $verusuario->password= $request->input('password');
        }
        $verusuario->sucursal= $request->input('procedencia');
        $verusuario->save();
       // DB::table('model_has_roles')->where('model_id','=', $verusuario)->delete();

        $parametro = $request->roles;
        $parametro2 = $request->roles2;
        $verusuario->removeRole($parametro2);
            $verusuario->assignRole($parametro);
        

        return redirect('/verusuario/index');
    }





}
