<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use App\Models\role;
use Spatie\Permission\Models\Role;
class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */ /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */ 
    public function index(Request $request)
    {
        $categoria = $request->get('filtro');
        if($categoria==null){
            $categoria="id";
        }
        $caracteres = $request->get('busqueda');
        $role = role::where("$categoria", 'like', "%$caracteres%")->get();
        return view('Role.rolemost', compact('role'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('Role.role');
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
            'name' => 'required|max:25',
           // 'guard_name' => 'required|max:15',
        ]);

    /*$role = new role;
    $role->name = $request->input('name');
    $role->guard_name = $request->input('guard_name');
    $role->save(); */
    $role = Role::create(['name' => $request->input('name')]);

    return redirect('/role/index'); 
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\role  $role
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $role = Role::findOrFail($id);
        return view('Role.role')->with('role', $role);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\role  $role
     * @return \Illuminate\Http\Response
     */
    public function edit(role $role)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\role $role
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $role)
    {
        
        $role = Role::findOrFail($role);
        $this->validate($request, [
            'name' => 'required|max:25',
           // 'guard_name' => 'required|max:15',
        ]);
        $role->name = $request->input('name');
        $role->save();
       // $role = Role::update(['name' => $request->input('name')]);
    return redirect('/role/index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\role  $role
     * @return \Illuminate\Http\Response
     */
    public function destroy($role)
    {
        $role =role::where('id','=', $role)->first();
        $role->delete();
        return redirect('/role/index')->with('Eliminar', 'Ok.');//
   
    }
}
