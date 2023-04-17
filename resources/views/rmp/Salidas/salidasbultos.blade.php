@extends("layouts.MenuRMP")
@section("content")
    <div class="container-fluid">
        <h1 class="mt-4">Salida De Materia Prima por Bultos
            <div class="btn-group" role="group">
            @if($validacionproceso==false)
                <button class="btn btn-sm btn-success"
                        id="botonAbrirModalNuevoConsumo"
                        data-toggle="modal" data-target="#modalNuevoConsumo">
                    <span class="fas fa-plus"></span> Nueva
                </button>
                @endif
            </div>

        </h1>
        @if(Session::has('flash_message'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>{{Session::get('flash_message')}}</strong> 
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
            </div>
        @endif
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item" aria-current="page" ><a href="/">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Salida De Materia Prima</li>

                <form  class="d-none d-md-inline-block form-inline
                           ml-auto mr-0 mr-md-2 my-0 my-md-0 mb-md-2">
                           <div class="row">
                    <div class="input-group" style="width: 300px">
                        <input class="form-control" name="fecha" type="date" placeholder="fecha"
                               aria-label="Search" @isset($fecha)
                               value="{{$fecha}}"

                               @endisset>
                        <div class="input-group-append">
                            <a id="borrarBusqueda" class="btn btn-danger hideClearSearch" style="color: white"
                               href="{{route("salidarmp")}}">&times;</a>
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                    <input id="fechahidden" type="hidden" value="{{$fecha}}">
                    <div class="input-group" style="width: 300px">
                        <input class="form-control" name="marca" type="search" placeholder="Buscar Por Marca"
                               aria-label="Search">
                        <div class="input-group-append">
                            <a id="borrarBusqueda" class="btn btn-danger hideClearSearch" style="color: white"
                               href="{{route("salidarmp")}}">&times;</a>
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
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
                <a onclick="versalidaprevia()" class="btn btn-dark hideClearSearch" style="color: white"
                   id="botonAbrirModal"
                   data-toggle="modal" data-target="#modalprocesar">Aplicar
                </a>
                @endif

                <a class="btn btn-success hideClearSearch" style="color: white"
                   id="botonAbrirModalExcel" hidden
                   data-toggle="modal" data-target="#modalfecha">Excel
                </a>

                <a class="btn btn-secondary hideClearSearch" style="color: white"
                   id="generarmarcasa" data-target="#generarmarcas"
                   data-toggle="modal"
                   >G. Marcas
                </a>

                <form id="" method="POST" action="{{route("reportesalidabultodetalladoexport")}}" enctype="multipart/form-data">
                    @csrf 
                    <input type="hidden" name="fecha" value="{{$fecha}}">
                    <button type="submit" class="btn btn-success hideClearSearch" style="color: white">
                        Detallado
                    </button>
                </form>

                <a class="btn btn-primary hideClearSearch" style="color: white"
                   id="botonAbrirModalExcel"
                   data-toggle="modal"
                   onclick="mostrarDiferencias('{{$fecha}}')">Diferencias
                </a>

                @if(Auth::user()->is_admin==1 && $validacionproceso)
                <a class="btn btn-warning hideClearSearch" style="color: white"
                   id="botonDesaplicar"
                   data-toggle="modal" data-target="#modaldesaplicar">Desaplicar
                </a>
                @endif
                @if($validacionproceso)
                <a onclick="versalida()" class="btn btn-success hideClearSearch" style="color: white"
                   id="botonDesaplicar"
                   data-toggle="modal" data-target="#salidamateriaprima">Salida. MP
                </a>
                @endif

                @isset($total)
                    <label  class="d-none d-md-inline-block form-inline
                           ml-auto mr-0 mr-md-2 my-0 my-md-0 mb-md-2"
                            style="align-content: center">Total Salida: {{$total}}
                    </label>
                @endisset

                @isset($salidapesototal)
                    <label  class="d-none d-md-inline-block form-inline
                           ml-auto mr-0 mr-md-2 my-0 my-md-0 mb-md-2"
                            style="align-content: center">Total Salida Peso: {{$salidapesototal}}
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
                <th>Marca</th>
                <th>Vitola</th>
                <th>Combinacion</th>
                <th>Cantidad</th>
                <th>Onzas</th>
                <th>Libras</th>
                <th>Fecha</th>
                <th>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" 
                class="bi bi-check2-circle" viewBox="0 0 16 16">
                <path d="M2.5 8a5.5 5.5 0 0 1 8.25-4.764.5.5 0 0 0 .5-.866A6.5 6.5 0 1 
                0 14.5 8a.5.5 0 0 0-1 0 5.5 5.5 0 1 1-11 0z"/>
                <path d="M15.354 3.354a.5.5 0 0 0-.708-.708L8 9.293 5.354 6.646a.5.5 0 
                1 0-.708.708l3 3a.5.5 0 0 0 .708 0l7-7z"/>
            </svg>
                </th>
                <th><span class="fas fa-info-circle"></span></th>
                <th><span class="fas fa-info-circle"></span></th>
            </tr>
            </thead>
            <tbody>
                @php
                $tCantidad=0;
                $tOnzas=0;
                $tLibras=0;
                @endphp
            @if($salida->count()<= 0)
                <tr>
                    <td colspan="4" style="align-items: center">No hay productos</td>
                </tr>
            @endif
            @foreach($salida as $productos)
                <tr>
                @php
                $tCantidad+=$productos->cantidad;
                $tOnzas+=$productos->totalpeso;
                $tLibras+=$productos->cantidad*$productos->totalpeso/16;
                @endphp
                    <td>{{$noPagina++}}</td>
                    <td>{{$productos->marca}}</td>
                    <td>{{$productos->vitola}}</td>
                    <td>{{$productos->combinacion}}</td>
                    <td class="table-info">{{$productos->cantidad}}</td>
                    <td>{{$productos->totalpeso}}</td>
                    <td class="table-info">{{$productos->cantidad*$productos->totalpeso/16}}</td>
                    <td>{{$productos->created_at}}</td>
                    <td>
                        <form id="nuevoP" method="POST" action="{{route('rmpverify',[$productos->salida]) }}" 
                        enctype="multipart/form-data">
                    @csrf
                    @if($productos->verify)
                        <input class="form-check-input" value="0" checked type="checkbox" name="verify" id="flexRadioDefault1"
                        onchange="this.form.submit();">
                        @else
                        <input class="form-check-input" value="1" type="checkbox" name="verify" id="flexRadioDefault1"
                        onchange="this.form.submit();">
                        @endif
                    </form>
                    </td>
                    <td>
                    <button class="btn btn-sm btn-info"
                                title="Ver"
                                data-toggle="modal"
                                onclick="verdetalle({{$productos->combinacion}})">
                            <span class="fas fa-eye"></span>
                    </button>
                    @if($validacionproceso==false)
                    <button class="btn btn-sm btn-success"
                                data-toggle="modal"
                                data-target="#modalSumar"
                                data-id="{{$productos->id}}"
                                title="Agregar">
                            <span class="fas fa-plus"></span>  
                    </button>
                    </td>
                    <td>
                        
                        <button onclick="show('{{$productos->m_id}}',
                         '{{$productos->v_id}}',
                         '{{$productos->combinacion}}', '{{$productos->cantidad}}',
                         '{{$productos->salida}}')" class="btn btn-sm btn-success"
                                data-toggle="modal"
                                data-target="#modalEditarCapaEntrega"
                                title="Editar">
                            <span class="fas fa-pencil-alt"></span>
                        </button>
                        <button class="btn btn-sm btn-danger"
                                title="Borrar"
                                data-toggle="modal"
                                data-target="#modalBorrarSalida"
                                onclick="showdelete({{$productos->salida}})">
                            <span class="fas fa-trash"></span>
                        </button>
                        @endif
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="4" style="text-align:center"><b> TOTALES</b></td>
                <td>{{$tCantidad}}</td>
                <td>{{$tOnzas}}</td>
                <td>{{$tLibras}}</td>
            </tr>

            </tbody>
        </table>

    </div>
    <!-----vista previa imagen------->
<!----------------------------------------------------MODAL NUEVO PRODUCTO------------------------------------------------------->
    <div class="modal fade" id="modalNuevoConsumo" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #2a2a35">
                    <h5 class="modal-title" style="color: white"><span class="fas fa-plus"></span> Traslado de Bulto a RMP
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: white">&times;</span>
                    </button>
                </div>
                <form id="nuevoP" method="POST" action="{{route("storesalidarmp")}}" enctype="multipart/form-data">

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
                            <label for="marca">Seleccione la Marca</label>
                            <br>
                            <select name="marca"
                                    style="width: 100%" required="required"
                                    class=" marca form-control @error('id_vitolas') is-invalid @enderror" id="marca"
                                    onchange="peticion();">
                                <option disabled selected value="">Seleccione</option>
                                @foreach($marca as $marcas)
                                    <option value="{{$marcas->id}}">
                                        {{$marcas->name}}
                                    </option>
                                @endforeach
                            </select>
                            <!---- Boton para crear un nuevo tipo de categoria- -->
                        </div>

                        <div class="form-group">
                            <label for="vitola">Seleccione la Vitola</label>
                            <br>
                            <select name="vitola" onchange="peticion()"
                                    style="width: 100%" required="required"
                                    class=" marca form-control @error('id_vitolas') is-invalid @enderror" id="vitola">
                                <option disabled selected value="">Seleccione</option>
                                @foreach($vitola as $vitolas)
                                    <option value="{{$vitolas->id}}">
                                        {{$vitolas->name}}
                                    </option>
                                @endforeach
                            </select>
                            <!---- Boton para crear un nuevo tipo de categoria- -->
                        </div>

                        
                        <div class="form-group">
                            <label for="combinacion">Seleccione la Combinacion</label>
                            <br>
                            <select disabled name="combinacion"
                                    style="width: 100%" required="required"
                                    class="marca form-control" id="combinacion">
                                <option disabled selected value="">Seleccione</option>
                                @foreach($dato as $comb)
                                    <option value="{{$comb['Id']}}">
                                    @foreach($comb['Codigo'] as $nw)
                                        {{$nw->Descripcion.' '.'Peso: '.$nw->peso.','}}
                                        @endforeach
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="Cantidad">Cantidad</label>
                            <br>
                           <input class="form-control" name="cantidad" type="number">
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
                    <h5 class="modal-title" style="color: white"><span class="fas fa-plus"></span> Editar Salida Materia Prima
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: white">&times;</span>
                    </button>
                </div>
                <form id="nuevoP" method="POST" action="{{route("updatesalidarmp")}}" >
                    @method("PUT")

                    @csrf
                    <div class="modal-body">
                    <div class="form-group">
                            <label for="marca1">Marca</label>
                            <br>
                            <select name="marca"
                                    style="width: 100%"
                                    class="disponible form-control" id="marca1"
                                    onchange="peticion1();" required="required" disabled>
                                <option disabled selected value="">Seleccione</option>
                                @foreach($marca as $marcas)
                                    <option value="{{$marcas->id}}">
                                        {{$marcas->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="vitola1">Vitola</label>
                            <br>
                            <select name="vitola" disabled
                                    style="width: 100%"
                                    class="disponible form-control" id="vitola1"
                                    onchange="peticion1();" required="required">
                                <option disabled selected value="">Seleccione</option>
                                @foreach($vitola as $vitolas)
                                    <option value="{{$vitolas->id}}">
                                        {{$vitolas->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="combinacion1">Seleccione la Combinacion</label>
                            <br>
                            <select name="combinacion"
                                    style="width: 100%" required="required"
                                    class="prueba form-control @error('id_vitolas') is-invalid @enderror" id="combinacion1">
                                <option disabled selected value="">Seleccione</option>
                            </select>
                            <!---- Boton para crear un nuevo tipo de categoria- -->
                        </div>

                        <div class="form-group">
                            <label for="cantidad1">Cantidad</label>
                            <input class=" form-control @error('name') is-invalid @enderror"
                             name="cantidad" id="cantidad1" type="number"
                              maxlength="100"
                                   value="{{ old('cantidad')}}" required="required" type="number">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <input id="salida" name="salida" type="hidden">
                        <button type="submit" class="btn btn-success" id="id_producto" onclick="f()">Editar</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!------------------MODAL BORRAR PRODUCTO---------------------------->
    <div class="modal fade" id="modalBorrarSalida" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <form method="post" action="{{route("deletesalidarmp")}}" >
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
                        <p>¿Estás seguro que deseas borrar esta salida de materia prima<label
                                id="nombreProducto"></label>?</p>

                    </div>
                    <div class="modal-footer">
                        <input id="id_salida" name="id_salida" type="hidden" value="">
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
            <div class="modal-header" style="background: #2a2a35">
                        <h5 class="modal-title" style="color: white"><span class=""></span> Aplicar Inventario
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span style="color: white" aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <p>¿Esta seguro que desea aplicar las salidas del dia: 
                            @isset($fecha) {{$fecha}} @endisset <label
                                id="nombreProducto"></label>?</p>

                    

            <table id="tblsalprevio" class="table">
                       <thead class="thead-dark">
                        <tr>
                            <th>Codigo</th>
                            <th>Descripcion</th>
                            <th>Peso</th>

                        </tr>
                        </thead>
                        <tbody id="previo">

                        </tbody>
                       </table>
                       </div>
                <form method="post" action="{{route("procesarsalida")}}" >
                    @csrf
                    
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
                <form method="post" action="{{route("desaplicarsalida")}}" >
                    @csrf
                    <div class="modal-header" style="background: #2a2a35">
                        <h5 class="modal-title" style="color: white"><span class=""></span> Desaplicar del Inventario
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span style="color: white" aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>¿Estas seguro que desea desaplicar las salidas del dia: 
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


    <!--                         MODAL SALIDA MATERIA PRIMA -->


    <div class="modal fade" id="salidamateriaprima" tabindex="-1" role="dialog">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <form method="post" action="{{route("excelversalidas")}}" >
                    @csrf
                    <div class="modal-header" style="background: #2a2a35">
                        <h5 class="modal-title" style="color: white">
                        <span class=""></span> Salida Materia Prima
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span style="color: white" aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                       <table id="tblsal" class="table">
                       <thead class="thead-dark">
                        <tr>
                            <th>Codigo</th>
                            <th>Descripcion</th>
                            <th>Peso</th>

                        </tr>
                        </thead>
                        <tbody id="tblInyect">

                        </tbody>
                       </table>

                    </div>
                    <div class="modal-footer">
                        <input name="fecha" type="hidden" @isset($fecha) value="{{$fecha}}" @endisset>
                        <button type="submit" class="btn btn-success">Excel</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>

        </div>
    </div>


    <!--          MODAL SUMAR A LA SALIDA           -->

    <div class="modal fade" id="modalSumar" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <form method="post" action="{{route("sumarsalidaMP")}}" >
                    @method("PUT")
                    @csrf
                    <div class="modal-header" style="background: #2a2a35">
                        <h5 class="modal-title" style="color: white"><span class="fas fa-trash"></span> Sumar
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span style="color: white" aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="suma">Total a Sumar</label>
                            <input class="form-control @error('name') is-invalid @enderror" name="suma" id="suma" maxlength="100"
                                   value="{{ old('suma')}}">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div></div>
                    <div class="modal-footer">
                        <input id="id_capa_entrega" name="id" type="hidden">
                        <button type="submit" class="btn btn-success" id="id_capa_entrega" name="id" >Sumar</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>

        </div>
    </div>




    <!-- MODAL VER Combinaciones -->
    <div class="modal fade" id="modalVerMP" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #2a2a35">
                    <h5 class="modal-title" style="color: white"><span class="fas fa-plus"></span> Detalle de la Materia Prima
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: white">&times;</span>
                    </button>
                </div>
                    @include('Alerts.errors')

                    @csrf
                    <div class="modal-body row" >


                        <div class="col-sm-10 card">

                            <div class="form-group row">
                                <div class="col-md-6">
                                    <label for="marcacapaentrega">
                                        <strong>Materia Prima:</strong>
                                    </label>
                                </div>
                                    <ol class="list-group" id="listado">

                                    </ol>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-success">Aceptar</button>
                    </div>
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

    <!----------------------------------------------------MODAL GENERAR MARCAS------------------------------------------------------->

    <div class="modal fade" id="generarmarcas" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #2a2a35">
                    <h5 class="modal-title" style="color: white"><span class="fas fa-plus"></span> Generar Marcas Automaticas.
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: white">&times;</span>
                    </button>
                </div>
                <form id="nuevoP" method="POST" action="{{route("generarMarcasAut")}}" enctype="multipart/form-data">

                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="fecha1">¿Desea generar registros de la ultima fecha?</label>
                            <input hidden class="form-control @error('name') is-invalid @enderror" name="fecha1" id="fecha1"
                                   type="date"
                                   value="{{$fecha}}" >
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="nuevoP" class="btn btn-success">Generar</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>

            </div>
        </div>
    </div>


    <div class="modal fade" id="modalfechacvs" tabindex="-1" 
    role="dialog" aria-labelledby="myLargeModalLabel" >
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #2a2a35">
                    <h5 class="modal-title" style="color: white"><span class="fas fa-plus"></span> Diferencias.
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: white">&times;</span>
                    </button>
                </div>
                    <div class="modal-body">
                            <table class="table">
                                <thead class="thead-dark">
                                <tr>
                                <th>Marca</th>
                                <th>Vitola</th>
                                <th>Sal. RMP</th>
                                <th>Ent. Despacho</th>
                                <th>Diferencias</th>
                                </tr>
                                </thead>

                                <tbody id="tablediff">
                                
                                </tbody>

                            </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
                    </div>
            </div>
        </div>
    </div>


    <script>

        let status = false;

        function mostrarDiferencias(fecha){
            let _token= "{{ csrf_token() }}";
            $.ajax({
            type: 'get',
            url: '/rmp/mostrardiferencias',
            data: {
                _token: _token,
                fecha: fecha
            },
            success: function(data) {
                $("#modalfechacvs").modal();
                mostrartablediff(data);
            }
        });
        }

        function mostrartablediff(data){
            $("#tablediff").empty();
            for (let i = 0; i < data.length; i++) {
                $("#tablediff").append(
                "<tr> <td>"+data[i].marca+"</td> <td>"
                    + data[i].vitola + "</td><td>" 
                    + data[i].totalInventario + "</td> <td>"
                    + data[i].totalDespacho + "</td> <td>"
                    + data[i].diferencia + "</td> </tr> "
            )
            }
        }

        function versalida(){
            let fecha = $("#fechahidden").val();
            let _token= "{{ csrf_token() }}";
            $.ajax({
            type: 'post',
            url: '/rmp/versalida/'+fecha,
            data: {
                _token: _token
            },
            success: function(data) {
                $('#tblInyect').empty();
                for (let i = 0; i < data.length; i++) {
                $('#tblInyect').append(
                    "<tr> <td>"+data[i].codigo_materia_prima+"</td> <td>"
                    + data[i].Descripcion + "</td> <td>"+ data[i].peso+"</td> </tr> "
                    
                ); 
            } 
            }
        });
        }

        function versalidaprevia(){
            let fecha = $("#fechahidden").val();
            let _token= "{{ csrf_token() }}";
            $.ajax({
            type: 'get',
            url: '/rmp/versalidaprevio/'+fecha,
            data: {
                _token: _token
            },
            success: function(data) {
                $('#previo').empty();
                for (let i = 0; i < data.length; i++) {
                $('#previo').append(
                    "<tr> <td>"+data[i].codigo+"</td> <td>"
                    + data[i].descripcion + "</td> <td>"+ data[i].peso+"</td> </tr> "
                    
                ); 
            } 
            }
        });
        }

        function verdetalle(id){
            let _token= "{{ csrf_token() }}";
            $.ajax({
            type: 'post',
            url: '/rmp/vercombinacion/'+id,
            data: {
                _token: _token
            },
            success: function(data) {
                $('#listado').empty();
                $('#modalVerMP').modal();
                mostrardetalle(data);
            }
        });

        }

        function mostrardetalle(data){
            for (let i = 0; i < data.length; i++) {
                $('#listado').append(
                    "<li class='list-group-item'>"
                    + data[i].Descripcion + ': '+ data[i].peso+
                    " </li> "
                    
                ); 
            }     
        }
        function peticion1(){
            let vitola= $('#vitola1').val();
            let marca= $('#marca1').val();
            let _token= "{{ csrf_token() }}";
            if (marca!=null && vitola!=null && status){
            $.ajax({
            type: 'post',
            url: '/rmp/peticion',
            data: {
                _token: _token,
                marca: marca,
                vitola: vitola
            },
            success: function(data) {
                if (data.ok) {
                    alert('No existe bulto registrado para el producto seleccionado');
                    reset1();
                }else{
                FiltrarSelectUpdate(data);
                }
            }
        }); 
    }
        }

        function showdelete(salida){
            $('#id_salida').val(salida);
        }

        function show(marca, vitola, combinacion, cantidad, salida){
            let _token= "{{ csrf_token() }}";
            if (marca!=null && vitola!=null){
            $.ajax({
            type: 'post',
            url: '/rmp/peticion',
            data: {
                _token: _token,
                marca: marca,
                vitola: vitola
            },
            success: function(data) {
                if (data.ok) {
                    alert('No existe bulto registrado para el producto seleccionado');
                    reset1();
                }else{
                    $('#marca1').val(marca);
                    $('#vitola1').val(vitola);
                    $('#vitola1').trigger('change');
                    $('#marca1').trigger('change');
                    status=true;
                    $('#cantidad1').val(cantidad);
                    $('#salida').val(salida);
                    FiltrarSelectUpdate(data);
                    $('#combinacion1').val(combinacion);
                    status=false;
                }
            }
        }); }
        }

        function peticion(){
            let vitola= $('#vitola').val();
            let marca= $('#marca').val();
            let _token= "{{ csrf_token() }}";
            if (marca!=null && vitola!=null){
            $.ajax({
            type: 'post',
            url: '/rmp/peticion',
            data: {
                _token: _token,
                marca: marca,
                vitola: vitola
            },
            success: function(data) {
                if (data.ok) {
                    alert('No existe bulto registrado para el producto seleccionado');
                    reset();
                }else{
                FiltrarSelect(data);
                }
            }
        }); }
        }



        function FiltrarSelect(data){
            let combinacion = $("#combinacion");
            combinacion.attr('disabled', false);
            combinacion.empty();
            for (let i = 0; i < data.length; i++) {
                combinacion.append
                ("<option class='item" + data[i].Id + "' value='"+data[i].Id+"'>"
                +
                concatenacion(data[i].Codigo)
                +
                " </option>");
            }            
        }

        function FiltrarSelectUpdate(data){
            let combinacion = $("#combinacion1");
            combinacion.attr('disabled', false);
            combinacion.empty();
            for (let i = 0; i < data.length; i++) {
                combinacion.append
                ("<option class='item" + data[i].Id + "' value='"+data[i].Id+"'>"
                +
                concatenacion(data[i].Codigo)
                +
                " </option>");
            }            
        }


        function reset(data){
            let combinacion = $("#combinacion");
            combinacion.attr('disabled', true);
            combinacion.empty();            
        }

        function reset1(data){
            let combinacion = $("#combinacion1");
            combinacion.attr('disabled', true);
            combinacion.empty();            
        }

        function concatenacion(data){
            let dato =[];
            for (let cod = 0; cod < data.length; cod++) {
                    dato.push(data[cod].Descripcion+': '+data[cod].peso+' ')
                }
                return dato;
        }

    </script>



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
