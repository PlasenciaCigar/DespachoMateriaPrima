<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class Usuario extends Controller
{

    public function agregarUsuario(Request $request){
        $admin  =false;
     $capa = false;
     $banda = false;
     $rmp = false;

        if ( $request->get('is_admin') == '0'){
            $admin = true;
        }
        if ($request->get('is_admin') == '1'){
            $capa = true;
        }
        if ($request->get('is_admin') == '2'){
            $banda = true;
        }
        if ($request->get('is_admin') == '3'){
            $rmp = true;
        }
        try {
            $this->validate($request, [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:8', 'confirmed'],

            ]);

            $usuario = new User();

            $usuario->name= $request->input("name");
            $usuario->email= $request->input("email");
            $usuario->password = bcrypt($request->input("password"));
            $usuario->is_admin= $admin;
            $usuario->is_capa= $capa;
            $usuario->is_banda=$banda;
            $usuario->is_rmp=$rmp;
            $usuario->save();


            return redirect()->route("index")->with("Usuario Agrregado correctamente");
        } catch (ValidationException $exception){
            return redirect()->route("index")->with('errors','errors')->with('id_producto',$request->input("id"))->withErrors($exception->errors());
        }




    }

    //
}
