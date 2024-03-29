<?php

namespace App\Http\Controllers;

use App\EmpleadosBanda;
use App\Http\Requests\createEmpleadosRequest;
use App\Empleado;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EmpleadoBandaController extends Controller
{
    //
    //Funcion Lista Empleados
    public function index(Request $request){
        if ($request){
            $query = trim($request->get("search"));
            $empleado = EmpleadosBanda::where("codigo","like","%".$query."%")->orderBy("nombre")->paginate(1000);

           return View('Empleados.EmpleadosBanda')

                ->withNoPagina(1)
                ->withEmpleado($empleado);

        }
    }
    //Funcion Crear Empleado
    public function storeEmpleado(createEmpleadosRequest $request){
        $empleado = new EmpleadosBanda();
        $empleado->nombre = $request->input("nombre");
        $empleado->codigo = $request->input("codigo");
        $empleado->puesto = ('Bonchero');
        $empleado->salon = $request->input("modulo");
        $empleado->save();
        return redirect()->route("empleadosBanda")->withExito("Empleado creada correctamente");
    }
    //Funcion Editar Empleado

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EmpleadoController  $recibirCapa
     * @return \Illuminate\Http\Response
     */
    public function editarEmpleado(Request $request){

            try {
                $this->validate($request, [
                    'codigo' => 'required',
                    'nombre' => 'required|string|max:100',

                ], $messages = [
                    'nombre.required' => 'El name de la nombre es requerido',
                    'codigo.required' => 'El name de la codigo es requerido',
                    'puesto.required' => 'El name de la marca es requerido',
                    'nombre.max:100' => 'El name no puede exceder 30 caracteres',
                    'nombre.string' => 'El name no deben de ser solamente numeros',
                ]);

                $editar = EmpleadosBanda::findOrFail($request->id);

                $editar->codigo= $request->input("codigo");
                $editar->nombre= $request->input("nombre");
                $editar->salon = $request->input("modulo");
                $editar->puesto = ('Bonchero');

                $editar->save();
                return redirect()->route("empleadosBanda")->withExito("Empleado editada correctamente");
            } catch (ValidationException $exception){
                return redirect()->route("empleadosBanda")->with('errors','errors')->with('id_producto',$request->input("id"))->withErrors($exception->errors());
            }




    }
    //Funcion Borrar Marca
    public function borrarEmpleado(Request $request){
        $id = $request->input("id");
        $borrar = EmpleadosBanda::findOrFail($id);
       // $updateProducto = Producto::where("id_marca","=",$id)->get();
        //foreach ($updateProducto as $producto){
          //  $producto->delete();
      //  }
        $borrar->delete();
        return redirect()->route("empleadosBanda")->withExito("Empleado borrada con éxito");
    }
}
