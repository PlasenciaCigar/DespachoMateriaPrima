@extends("layouts.MenuRMP")
@section("content")
    <div class="container-fluid">
        <h1 class="mt-4">Entrada De Materia Prima
            <div class="btn-group" role="group">
            @if($validacionproceso==false)
                <button class="btn btn-sm btn-success"
                        id="botonAbrirModalNuevoConsumo"
                        data-toggle="modal" data-target="#modalNuevoConsumo">
                    <span class="fas fa-plus"></span> Nueva
                </button>
                @endif
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
                <li class="breadcrumb-item active" aria-current="page">Entrada De Materia Prima</li>

                <form  class="d-none d-md-inline-block form-inline
                           ml-auto mr-0 mr-md-2 my-0 my-md-0 mb-md-2">
                    <div class="input-group" style="width: 300px">
                        <input class="form-control" name="fecha" type="date" placeholder="fecha"
                               aria-label="Search" @isset($fecha)
                               value="{{$fecha}}"

                               @endisset>
                        <div class="input-group-append">
                            <a id="borrarBusqueda" class="btn btn-danger hideClearSearch" style="color: white"
                               href="{{route("EntradaBultos")}}">&times;</a>
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </ol>

            @if($validacionproceso)
            <div class="alert alert-warning" role="alert">
                <h3>FECHA APLICADA</h3>
            </div>
            @endif

            <div class="pagination pagination-sm">

            @if($validacionproceso==false)
                <a class="btn btn-dark hideClearSearch" style="color: white"
                   id="botonAbrirModal"
                   data-toggle="modal" data-target="#modalprocesar">Aplicar
                </a>
                @endif

                <a class="btn btn-success hideClearSearch" style="color: white"
                   id="botonAbrirModalExcel"
                   data-toggle="modal" data-target="#modalfecha">Excel
                </a>
                @if(Auth::user()->is_admin==1 && $validacionproceso)
                <a class="btn btn-warning hideClearSearch" style="color: white"
                   id="botonDesaplicar"
                   data-toggle="modal" data-target="#modaldesaplicar">Desaplicar
                </a>
                @endif

                @if($validacionproceso==false)
                <a class="btn btn-primary hideClearSearch" style="color: white"
                   id="botonAbrir"
                   data-toggle="modal" data-target="#Modaldev">Dev. Bultos
                </a>
                @endif

                @isset($total)
                    <label  class="d-none d-md-inline-block form-inline
                           ml-auto mr-0 mr-md-2 my-0 my-md-0 mb-md-2"
                            style="align-content: center">Total Entrada: {{$total}}
                    </label>
                @endisset
            </div>
         </nav>

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

                <th>Codigo</th>
                <th>Nombre</th>
                <th>Observacion</th>
                <th>Libras</th>
                <th>Procedencia</th>
                <th>Fecha</th>

                <th><span class="fas fa-info-circle"></span></th>
            </tr>
            </thead>
            <tbody>
            @if($entrada->count()<= 0)
                <tr>
                    <td colspan="4" style="align-items: center">No hay productos</td>
                </tr>
            @endif
            @foreach($entrada as $productos)
                <tr>
                    <td>{{$noPagina++}}</td>
                    <td>{{$productos->codigo_materia_prima}}</td>
                    <td>{{$productos->nombre}}</td>
                    <td>{{$productos->observacion}}</td>
                    <td>{{$productos->Libras}}</td>
                    <td>{{$productos->procedencia}}</td>
                    <td>{{$productos->created_at}}</td>
                    <td>
                        @if($productos->procedencia != 'Despacho')
                        @if($validacionproceso==false && $productos->desdeinventario!=1)
                        <button onclick="send('{{$productos->id}}','{{$productos->observacion}}','{{$productos->procedencia}}',
                        '{{$productos->codigo_materia_prima}}', '{{$productos->Libras}}')" class="btn btn-sm btn-success"
                                data-toggle="modal"
                                data-target="#modalEditarCapaEntrega"
                                title="Editar">
                            <span class="fas fa-pencil-alt"></span>
                        </button>
                        <button class="btn btn-sm btn-danger"
                                title="Borrar"
                                data-toggle="modal"
                                data-target="#modalBorrarCapaEntrega"
                                data-id="{{$productos->id}}">
                            <span class="fas fa-trash"></span>
                        </button>
                        @endif
                        @endif
                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>

    </div>

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
    <!-----vista previa imagen------->
<!----------------------------------------------------MODAL NUEVO PRODUCTO------------------------------------------------------->
    <div class="modal fade" id="modalNuevoConsumo" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #2a2a35">
                    <h5 class="modal-title" style="color: white"><span class="fas fa-plus"></span> Agregar Entrada  De Bulto
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: white">&times;</span>
                    </button>
                </div>
                <form id="nuevoP" method="POST" action="{{route("storeentradarmp")}}" enctype="multipart/form-data">

                    @csrf
                    <div class="modal-body">


                        <div class="form-group">
                            <label for="fecha">Fecha</label>
                            <input class="form-control @error('onzas') is-invalid @enderror" name="fecha" id="fecha"  @isset($fecha)
                            value="{{$fecha}}"
                        @endisset
                                   value="{{ old('fecha')}}" type="date"
                                  >
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="nombreNuevoProducto">Observacion</label>
                            <textarea name="observacion" class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="procedencia">Procedencia</label>
                            <br>
                            <select name="procedencia"
                                    style="width: 100%" required="required"
                                    class="marca form-control @error('id_vitolas') is-invalid @enderror" id="procedencia">
                                <option disabled selected value="">Seleccione</option>
                                <option value="Terminado">Terminado</option>
                                <option value="Rezago">Rezago</option>
                                <option value="Escogida">Escogida</option>
                                <option value="Proceso">Proceso</option>
                                <option value="Despacho">Despacho</option>
                                <option value="Otro">Otro</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="codigo">Seleccione la Materia Prima</label>
                            <br>
                            <select name="codigo"
                                    style="width: 100%" required="required"
                                    class=" marca form-control @error('id_vitolas') is-invalid @enderror" id="codigo">
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
                            <label for="nombreNuevoProducto">Libras</label>
                            <input class=" form-control @error('name') is-invalid @enderror" name="libras" id="nombreNuevoProducto" maxlength="100"
                                   value="{{ old('total')}}" required="required" type="">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
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
                                <option value="Despacho">Despacho</option>
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
                                   value="{{ old('total')}}" required="required" type="">
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


    
    <div class="modal fade" id="Modaldev" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <form method="post" action="{{route("generarentradamp")}}" >
                    @csrf
                    <div class="modal-header" style="background: #2a2a35">
                        <h5 class="modal-title" style="color: white"><span class=""></span> Registrar Devolucion
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span style="color: white" aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                    <div class="form-group">
                            <label for="marca">Seleccione la Marca</label>
                            <br>
                            <select name="marca"
                                    style="width: 100%" required="required"
                                    class=" marca form-control @error('id_vitolas') is-invalid @enderror" id="marca">
                                <option disabled selected value="">Seleccione</option>
                                @foreach($marcas as $marcas)
                                    <option value="{{$marcas->id}}">
                                        {{$marcas->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="vitola">Seleccione la Vitola</label>
                            <br>
                            <select name="vitola"
                                    style="width: 100%" required="required"
                                    class=" marca form-control @error('id_vitolas') is-invalid @enderror" id="vitola">
                                <option disabled selected value="">Seleccione</option>
                                @foreach($vitolas as $vitolas)
                                    <option value="{{$vitolas->id}}">
                                        {{$vitolas->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="vitola">Cantidad de bultos devueltos</label>
                            <br>
                            <input class="form-control" name="total" type="number">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <input id="" name="fecha" type="hidden" @isset($fecha) value="{{$fecha}}" @endisset>
                        <button type="submit" class="btn btn-dark">Generar MP</button>
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
                    <h5 class="modal-title" style="color: white"><span class="fas fa-plus"></span> Exportar Devoluciones
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: white">&times;</span>
                    </button>
                </div>
                <form id="nuevoP" method="POST" action="{{route("exportentradarmp")}}" enctype="multipart/form-data">

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
