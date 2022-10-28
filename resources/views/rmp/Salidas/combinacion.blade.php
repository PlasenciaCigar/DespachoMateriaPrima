@extends("layouts.MenuRMP")
@section("content")
    <div class="container-fluid">
        <h1 class="mt-4">Combinaciones Segun Bultos
            <div class="btn-group" role="group">
            </div>

            @if(Session::has('flash_message'))
        <div class="alert alert-danger" role="alert">
            {{Session::get('flash_message')}}
          </div>
        @endif

        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item" aria-current="page" ><a href="/">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Combinaciones segun Bultos</li>
            </ol>

         </nav>

         <ul class="list-group list-group-horizontal">
            <li class="list-group-item">

            <div>
            <label for="">Marca:</label>
            <select class="marca" name="" id="">
            <option disabled selected value="">Seleccione</option>
                    @foreach($marca as $marcas)
            <option value="{{$marcas->id}}">{{$marcas->name}}</option>
                    @endforeach
            </select>
            </div>

            </li>

            <li class="list-group-item">
            <div>
            <label for="">Vitola:</label>
            <select class="marca" name="" id="">
            <option disabled selected value="">Seleccione</option>
                    @foreach($vitola as $vitolas)
            <option value="{{$vitolas->id}}">{{$vitolas->name}}</option>
                    @endforeach
            </select>
            </div>
            </li>
         </ul>
         <br>

         <label for="">Materia Prima</label>
         <select name="states[]" multiple="multiple" class="marca col-md-10" name="" id="">
                    @foreach($materiaprima as $materiaprimas)
            <option value="{{$materiaprimas->Codigo}}">{{$materiaprimas->Descripcion}}</option>
                    @endforeach
         </select>

        @if(session("exito"))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{session("exito")}}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @if(session("error"))
            <div class="alert alert-danger alert-dismissible" role="alert">
                <span class="fa fa-save"></span> {{session("error")}}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>

            </div>

        @endif

        <table class="table">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Materia Prima</th>
                <th>Peso</th>
                <th><span class="fas fa-info-circle"></span></th>
            </tr>
            </thead>
            <tbody>
                <td>1</td>
                <td>Arapiraca</td>
                <td>69</td>
                <td>
                <button class="btn btn-sm btn-danger"
                                title="Borrar"
                                data-toggle="modal"
                                data-target="#modalBorrarCapaEntrega"
                                data-id="">
                            <span class="fas fa-trash"></span>
                        </button>
                </td>
            </tbody>
        </table>

    </div>
    <style>
table, th, td {
  border: 1px solid black;
}
</style>

    <script>
        
        function send(id, observacion, procedencia, codigo, libras){
            $('#observacion1').val(observacion);
            $('#procedencia1').val(procedencia);
            $('#procedencia1').trigger('change');
            $('#id_entrada').val(id);
            $('#codigo1').val(codigo);
            $('#codigo1').trigger('change');
            $('#libras1').val(libras);
        }


    </script>

    
    <script>
        
        function cambio(tripa){
            $('#nombremp').val(tripa);
            $('#nombremp').trigger('change');
        }
    </script>
    <!-----vista previa imagen------->
<!----------------------------------------------------MODAL NUEVO PRODUCTO------------------------------------------------------->
    <div class="modal fade" id="modalNuevoConsumo" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #2a2a35">
                    <h5 class="modal-title" style="color: white"><span class="fas fa-plus"></span> Agregar Combinacion
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: white">&times;</span>
                    </button>
                </div>
                <form id="nuevoP" method="POST" action="{{route("storeentradarmp")}}" enctype="multipart/form-data">

                    @csrf
                    <div class="modal-body">

                        <div class="form-group">
                            <label for="procedencia">Marca</label>
                            <br>
                            <select name="marca"
                                    style="width: 100%" required="required"
                                    class="marca form-control @error('id_vitolas') is-invalid @enderror" id="procedencia">
                                <option disabled selected value="">Seleccione</option>
                                @foreach($marca as $marcas)
                                <option value="{{$marcas->id}}">{{$marcas->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="codigo">Seleccione la Vitola</label>
                            <br>
                            <select name="vitola"
                                    style="width: 100%" required="required"
                                    class=" marca form-control @error('id_vitolas') is-invalid @enderror" id="vitola">
                                <option disabled selected value="">Seleccione</option>
                                @foreach($vitola as $vitolas)
                                    <option value="{{$vitolas->id}}">
                                        {{$vitolas->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="codigo">Seleccione la Materia Prima</label>
                            <br>
                            <select name="codigo"
                                    style="width: 100%" required="required"
                                    class="marca form-control @error('id_vitolas') is-invalid @enderror" id="codigo"
                                    onchange="cambio(this.value);">
                                    <option disabled selected value="">Seleccione</option>
                                @foreach($materiaprima as $materiaprimas)
                                    <option value="{{$materiaprimas->Codigo}}">
                                        {{$materiaprimas->Descripcion}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="row">
                        <div class="form-group col-md-50">
                            <label for="nombreNuevoProducto">Materia Prima</label>
                            <input class="col-md-7 form-control @error('name') is-invalid @enderror" name="libras" id="nombremp" maxlength="50"
                                   value="{{ old('total')}}" required="required" type="text" readonly>
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        <div class="form-group col-md-50">
                            <label for="nombreNuevoProducto">Peso</label>
                            <input class="col-md-5 form-control @error('name') is-invalid @enderror" name="libras" id="nombreNuevoProducto" maxlength="50"
                                   value="{{ old('total')}}" required="required" type="number">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        <div class="form-group col-md-50">
                            <br>
                            <button onclick="agregar()" id="add" class="btn btn-success">Add</button>
                        </div>
                        <div id="xdd"></div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="nuevoP" class="btn btn-success">Crear</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-----------------------------MODAL EDITAR PRODUCTO------------------------------->
    <div class="modal fade" id="modalEditarCapaEntrega" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #2a2a35">
                    <h5 class="modal-title" style="color: white"><span class="fas fa-plus"></span> Editar Entrada Materia Prima
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: white">&times;</span>
                    </button>
                </div>
                <form id="nuevoP" method="POST" action="{{route("updateentradarmp")}}" >
                    @method("PUT")

                    @csrf
                    <div class="modal-body">
                    <div class="form-group">
                            <label for="observacion1">Observacion</label>
                            <textarea id="observacion1" name="observacion" 
                            class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="procedencia1">Procedencia</label>
                            <br>
                            <select name="procedencia"
                                    style="width: 100%"
                                    class="disponible form-control" id="procedencia1" required="required">
                                <option disabled selected value="">Seleccione</option>
                                <option value="Terminado">Terminado</option>
                                <option value="Rezago">Rezago</option>
                                <option value="Escogida">Escogida</option>
                                <option value="Proceso">Proceso</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="codigo1">Seleccione la Materia Prima</label>
                            <br>
                            <select name="codigo"
                                    style="width: 100%" required="required"
                                    class="prueba form-control @error('id_vitolas') is-invalid @enderror" id="codigo1">
                                <option disabled selected value="">Seleccione</option>
                                @foreach($materiaprima as $materiaprimas)
                                    <option value="{{$materiaprimas->Codigo}}">
                                        {{$materiaprimas->Descripcion}}
                                    </option>
                                @endforeach
                            </select>
                            <!---- Boton para crear un nuevo tipo de categoria- -->
                        </div>

                        <div class="form-group">
                            <label for="libras1">Libras</label>
                            <input class=" form-control @error('name') is-invalid @enderror" name="libras" id="libras1" maxlength="100"
                                   value="{{ old('total')}}" required="required" type="number">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input id="id_entrada" name="id" type="hidden">
                        <button type="submit" class="btn btn-success" id="id_producto" onclick="f()">Editar</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!------------------MODAL BORRAR PRODUCTO---------------------------->
    <div class="modal fade" id="modalBorrarCapaEntrega" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <form method="post" action="{{route("deleteentradarmp")}}" >
                    @method("DELETE")
                    @csrf
                    <div class="modal-header" style="background: #2a2a35">
                        <h5 class="modal-title" style="color: white"><span class="fas fa-trash"></span> Borrar
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span style="color: white" aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>¿Estás seguro que deseas borrar la entrada De Materia Prima <label
                                id="nombreProducto"></label>?</p>

                    </div>
                    <div class="modal-footer">
                        <input id="id_capa_entrega" name="id" type="hidden" value="">
                        <button type="submit" class="btn btn-danger">Borrar</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>

        </div>
    </div>



    <div class="modal fade" id="modalprocesar" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <form method="post" action="{{route("procesarentrada")}}" >
                    @csrf
                    <div class="modal-header" style="background: #2a2a35">
                        <h5 class="modal-title" style="color: white"><span class=""></span> Aplicar Inventario
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span style="color: white" aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>¿Esta seguro que desea aplicar las entradas del dia: 
                            @isset($fecha) {{$fecha}} @endisset <label
                                id="nombreProducto"></label>?</p>

                    </div>
                    <div class="modal-footer">
                        <input id="id_capa_entrega" name="fecha" type="hidden" @isset($fecha) value="{{$fecha}}" @endisset>
                        <button type="submit" class="btn btn-dark">Aplicar</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>

        </div>
    </div>



    <div class="modal fade" id="modaldesaplicar" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <form method="post" action="{{route("desaplicarentrada")}}" >
                    @csrf
                    <div class="modal-header" style="background: #2a2a35">
                        <h5 class="modal-title" style="color: white"><span class=""></span> Desaplicar del Inventario
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span style="color: white" aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>¿Estas seguro que desea desaplicar las entradas del dia: 
                            @isset($fecha) {{$fecha}} @endisset <label
                                id="nombreProducto"></label>?</p>

                    </div>
                    <div class="modal-footer">
                        <input id="id_capa_entrega" name="fecha" type="hidden" @isset($fecha) value="{{$fecha}}" @endisset>
                        <button type="submit" class="btn btn-dark">Desaplicar</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>

        </div>
    </div>




    <!----------------------------------------------------MODAL fecha Exportar Excel------------------------------------------------------->

    <div class="modal fade" id="modalfecha" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #2a2a35">
                    <h5 class="modal-title" style="color: white"><span class="fas fa-plus"></span> Exportar EXCEL
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: white">&times;</span>
                    </button>
                </div>
                <form id="nuevoP" method="POST" action="{{route("exportarEntradaBultos")}}" enctype="multipart/form-data">

                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="fecha1">Fecha</label>
                            <input class="form-control @error('name') is-invalid @enderror" name="fecha1" id="fecha1"
                                   type="datetime-local"
                                   value="{{ old('fecha1')}}" >
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="nuevoP" class="btn btn-success">Exportar</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>

            </div>
        </div>
    </div>



     <style>

         .image-preview-input {
             position: relative;
             overflow: hidden;
             margin: 0px;
             color: #333;
             background-color: #fff;
             border-color: #ccc;
         }
         .image-preview-input input[type=file] {
             position: absolute;
             top: 0;
             right: 0;
             margin: 0;
             padding: 0;
             font-size: 20px;
             cursor: pointer;
             opacity: 0;
             filter: alpha(opacity=0);
         }
         .image-preview-input-title {
             margin-left: 2px;
         }
         .image-border{
             border: black 1px solid;
             padding: 3px;
             justify-content: center;
             align-items: center;
         }
         .transparent-input{
             background-color:rgba(0,0,0,0) !important;
             border:none !important;
             padding-left: 5px;
         }
         .modal-lg {
             max-width: 45% !important;
         }

     </style>



 @endsection
