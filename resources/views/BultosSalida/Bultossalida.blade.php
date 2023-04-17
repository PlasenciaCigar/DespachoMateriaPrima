@extends("layouts.MenuBanda")
@section("content")
    <div class="container-fluid">
        <h1 class="mt-4">Entrega De Bultos
            <div class="btn-group" role="group">

                <button class="btn btn-sm btn-success"
                        id="botonAbrirModalNuevoBultoEntrega"
                        data-toggle="modal" data-target="#modalNuevoBultoEntrega">
                    <span class="fas fa-plus"></span> Nueva
                </button>
            </div>

        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item" aria-current="page" ><a href="/">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Entrega De Bulto</li>

                <form  class="d-none d-md-inline-block form-inline
                           ml-auto mr-0 mr-md-2 my-0 my-md-0 mb-md-2">
                    <div class="input-group" style="width: 900px">

                            <select name="modulos"
                                    required
                                    class="form-control @error('id_marca') is-invalid @enderror" id="empleadoEditarcapaentregas">
                                <option selected value="0">Todos Los Modulos</option>
                                    <option value="1">Modulo #1</option>
                                    <option value="2">Modulo #2</option>
                                    <option value="3">Modulo #3</option>
                            </select>
                        <input class="form-control" name="fecha" type="date" placeholder="fecha"
                               aria-label="Search" @isset($fecha)
                                   value="{{$fecha}}"
                               @endisset >
                               <input class="form-control" name="marca" type="search" placeholder="Marca"
                               aria-label="Search">
                               <input class="form-control" name="search" type="search" placeholder="Codigo Empleado"
                               aria-label="Search">

                        <div class="input-group-append">
                            <a id="borrarBusqueda" class="btn btn-danger hideClearSearch" style="color: white"
                               href="{{route("BultoSalida")}}">&times;</a>
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </ol>

            <div class="pagination pagination-sm">

                <a class="btn btn-dark hideClearSearch" style="color: white"
                   id="botonAbrirModalNuevoRecepcionCapa"
                   data-toggle="modal" data-target="#modalfechacvs">CVS</a>

                <a class="btn btn-success hideClearSearch" style="color: white"
                   id="botonAbrirModalNuevoRecepcionCapa"
                   data-toggle="modal" data-target="#modalfecha">Excel</a>

                <a class="btn btn-danger hideClearSearch" style="color: white"
                   id="botonAbrirModalNuevoRecepcionCapa"
                   data-toggle="modal" data-target="#modalfechapdf">PDF</a>

                   @if($generado)
                   <a class="btn btn-info hideClearSearch" style="color: white"
                id="EMarcas"
                data-toggle="modal" data-target="#modalfechamarca">Ver. MP</a>
                @else
                <a class="btn btn-info hideClearSearch" style="color: white"
                id="EMarcas"
                data-toggle="modal" data-target="#modalconfirmar">Gen. MP</a>
                 @endif

                @foreach($total as $producto)
                    <label  class="d-none d-md-inline-block form-inline
                           ml-auto mr-0 mr-md-2 my-0 my-md-0 mb-md-2"
                            style="align-content: center">Total Entregado: {{$producto->total_capa}}</label>
                @endforeach
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

        @if(session("errores"))
            <input type="hidden" id="id_producto" name="id_producto" value="{{session("id_producto")}}">
            <script type="text/javascript">
                var id = document.getElementById("id_producto").value;
                document.onreadystatechange = function n(){
                    if (document.readyState){
                        document.getElementById("editarCapaEntrega"+id).click();
                    }
                }
            </script>
        @endif
        @if($errors->any())

            <script>
                document.onreadystatechange = function n(){
                    if (document.readyState){
                       document.getElementById("botonAbrirModalNuevoBultoEntrega").click();
                    }
                }
            </script>
        @endif

        <table class="table">
            <thead class="thead-dark">
            <tr>
                <th>Codigo</th>
                <th>Empleado</th>
                <th>Marca</th>
                <th>Vitola</th>
                <th>Semilla</th>
                <th>Variedad</th>
                <th>Tamaño</th>
                <th>Procedencia</th>
                <th>Total</th>


                <th><span class="fas fa-info-circle"></span></th>
            </tr>
            </thead>
            <tbody>
            @if($entregaBulto->count()<= 0)
                <tr>
                    <td colspan="4" style="align-items: center">No hay productos</td>
                </tr>
            @endif
            @foreach($entregaBulto as $productos)
                <tr>
                    <td>{{$productos->codigo_empleado}}</td>

                    <td>{{$productos->nombre_empleado}}</td>
                    <td>{{$productos->nombre_marca}}</td>
                    <td>{{$productos->nombre_vitolas}}</td>
                    <td>{{$productos->semilla}}</td>
                    <td>{{$productos->variedad}}</td>
                    <td>{{$productos->tamano}}</td>
                    <td>{{$productos->procedencia}}</td>
                    <td>{{$productos->total}}</td>

                    <td>
                    @if($generado)
                    <button class="btn btn-sm btn-info"
                                title="Ver"
                                data-toggle="modal"
                                data-target="#modalVerCapaEntrega"
                                data-id_empleado="{{$productos->adicional}}"
                                data-id_marca="{{$productos->nombre_marca}}"
                                data-id_vitolas="{{$productos->nombre_vitolas}}"
                                data-total="{{$productos->total}}"
                                data-combinacion="{{$productos->combinacion}}"
                                onclick="mandar({{$productos->combinacion}});">
                            <span class="fas fa-eye"></span>
                        </button>
                        @else
                        <button
                            class="btn btn-sm btn-info"
                            data-toggle="modal"
                            data-target="#modalSumar75"
                            data-id="{{$productos->id}}"
                            title="Agregar"><span class="fas fa-plus"></span></button>

                            <button
                            class="btn btn-sm btn-info"
                            data-toggle="modal"
                            data-target="#modalRestar75"
                            data-id="{{$id_send = $productos->id}}"
                            title="Agregar" onclick="envioauto({{$id_send}})"><span class="fas fa-minus"></span></button>

                        <button class="btn btn-sm btn-info"
                                data-toggle="modal"
                                data-target="#modalSumar100"
                                data-id="{{$productos->id}}"
                                title="100">
                            100 </button>

                            <button class="btn btn-sm btn-info"
                            data-toggle="modal"
                            data-target="#modalRestar100"
                            data-id="{{$pro = $productos->id}}"
                            title="100" onclick="resta({{$pro}})">
                        -100 </button>

                        <script>
                       function resta(pro){
                            $('#id_capa_entregap').val(pro);

                        }
                        </script>


                        <button class="btn btn-sm btn-info"
                                title="Ver"
                                data-toggle="modal"
                                data-target="#modalVerCapaEntrega"
                                data-id_empleado="{{$productos->adicional}}"
                                data-id_marca="{{$productos->nombre_marca}}"
                                data-id_vitolas="{{$productos->nombre_vitolas}}"
                                data-total="{{$productos->total}}"
                                data-combinacion="{{$productos->combinacion}}"
                                onclick="mandar({{$productos->combinacion}});">
                            <span class="fas fa-eye"></span>
                        </button>


                        <button class="btn btn-sm btn-success" 
                                id="editarCapaEntrega{{$productos->id}}"
                                data-toggle="modal"
                                data-target="#modalEditarCapaEntrega"
                                data-id="{{$productos->id}}"
                                data-id_empleado="{{$productos->id_empleado}}"
                                data-id_marca="{{$productos->id_marca}}"
                                data-id_vitolas="{{$productos->id_vitolas}}"
                                data-id_semilla="{{$productos->id_semilla}}"
                                data-id_variedad="{{$productos->id_variedad}}"
                                data-id_tamano="{{$productos->id_tamano}}"
                                data-id_procedencia="{{$productos->id_procedencia}}"
                                data-total="{{$productos->total}}"
                                title="Editar"
                                onclick="normaenviar({{$productos->combinacion}})">
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
                    </td>
                </tr>
            @endforeach
            <script>
                function envioauto(id){
                    $('#id_capa_entregass').val(id);
                }
                function mandar(combi){
                        $('#combix').text(combi);
                }
            </script>
            <tr>
                <td colspan="8" style="text-align:center;">
                    <strong>
                        TOTAL
                    </strong></td>

                    <?php
                    $totalI = 0;

                foreach($entregaBulto as $productos){
                    $totalI+=$productos->total;
                }
                ?>
                <td>
                    <strong>{{$totalI}}</strong>
                </td>
            </tr>

            </tbody>
        </table>

    </div>
    <!-----vista previa imagen------->
<!----------------------------------------------------MODAL NUEVO PRODUCTO------------------------------------------------------->
    <div class="modal fade" id="modalNuevoBultoEntrega" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #2a2a35">
                    <h5 class="modal-title" style="color: white"><span class="fas fa-plus"></span> Agregar Salida De Bulto
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: white">&times;</span>
                    </button>
                </div>
                <form id="nuevoP" method="POST" action="{{route("BultoSalidanueva")}}" enctype="multipart/form-data">

                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="fecha">Fecha</label>
                            <input class="form-control @error('name') is-invalid @enderror" name="fecha" id="fecha"
                                   type="date"
                                   @isset($fecha)
                                   value="{{$fecha}}"
                                   @endisset
                                   >
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="empleadoEditarcapaentrega">Seleccione Empleado</label>
                            <br>
                            <select name="id_empleado"
                                    style="width: 100%" required="required"
                                    class="marca form-control @error('id_marca') is-invalid @enderror" id="empleadoEditarcapaentrega">
                                <option disabled selected value="">Seleccione</option>
                                @foreach($empleados as $empleado)
                                    <option value="{{$empleado->id}}" @if(Request::old('id_empleado')==$empleado->id){{'selected'}}@endif
                                    @if(session("idMarca"))
                                        {{session("idMarca")==$empleado->id ? 'selected="selected"':''}}
                                            @endif>{{$empleado->codigo}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="marca1">Seleccione la marca</label>
                            <br>
                            <select name="id_marca" onchange="peticion()";
                                    style="width: 100%"  required="required"
                                    class="marca form-control @error('id_marca') is-invalid @enderror" id="marca1"
                                    >
                                <option disabled selected value="">Seleccione</option>
                                @foreach($marca as $marcas)
                                    <option value="{{$marcas->id}}" @if(Request::old('id_marca')==$marcas->id){{'selected'}}@endif
                                    @if(session("idMarca"))
                                        {{session("idMarca")==$marcas->id ? 'selected="selected"':''}}
                                            @endif>{{$marcas->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="vitolacapaentrega1">Seleccione la Vitola</label>
                            <br>
                            <select name="id_vitolas"
                                    style="width: 100%"  required="required"
                                    class="marca form-control @error('id_marca') is-invalid @enderror" id="vitolacapaentrega1"
                                    onchange="peticion();">
                                <option disabled selected value="">Seleccione</option>
                                @foreach($vitola as $vitolas)
                                    <option value="{{$vitolas->id}}" @if(Request::old('id_vitolas')==$vitolas->id){{'selected'}}@endif
                                    @if(session("idMarca"))
                                        {{session("idMarca")==$vitolas->id ? 'selected="selected"':''}}
                                            @endif>{{$vitolas->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="combinaciones">Seleccione la Combinacion</label>
                            <br>
                            <select name="combinaciones"
                                    style="width: 100%"
                                    class="marca form-control @error('id_marca') is-invalid @enderror" id="combinaciones">
                            </select>
                        </div>



                        <div class="form-group">
                            <label for="id_semilla">Seleccione la semilla</label>
                            <br>
                            <select name="id_semilla"
                                    style="width: 100%"  required="required"
                                    class="marca form-control @error('id_marca') is-invalid @enderror" id="id_semilla">
                                <option disabled selected value="">Seleccione</option>
                                @foreach($semilla as $marcas)
                                    <option value="{{$marcas->id}}" @if(Request::old('id_marca')==$marcas->id){{'selected'}}@endif
                                    @if(session("idMarca"))
                                        {{session("idMarca")==$marcas->id ? 'selected="selected"':''}}
                                            @endif>{{$marcas->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="id_variedad">Seleccione la variedad</label>
                            <br>
                            <select name="id_variedad"
                                    style="width: 100%"  required="required"
                                    class="marca form-control @error('id_marca') is-invalid @enderror" id="id_variedad">
                                <option disabled selected value="">Seleccione</option>
                                @foreach($variedad as $marcas)
                                    <option value="{{$marcas->id}}" @if(Request::old('id_marca')==$marcas->id){{'selected'}}@endif
                                    @if(session("idMarca"))
                                        {{session("idMarca")==$marcas->id ? 'selected="selected"':''}}
                                            @endif>{{$marcas->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="id_tamano">Seleccione el Tamano</label>
                            <br>
                            <select name="id_tamano"
                                    style="width: 100%"  required="required"
                                    class="marca form-control @error('id_marca') is-invalid @enderror" id="id_tamano">
                                <option disabled selected value="">Seleccione</option>
                                @foreach($tamano as $marcas)
                                    <option value="{{$marcas->id}}" @if(Request::old('id_marca')==$marcas->id){{'selected'}}@endif
                                    @if(session("idMarca"))
                                        {{session("idMarca")==$marcas->id ? 'selected="selected"':''}}
                                            @endif>{{$marcas->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                         <div class="form-group">
                            <label for="id_procedencia">Seleccione la Procedencia</label>
                            <br>
                            <select name="id_procedencia"
                                    style="width: 100%"  required="required"
                                    class="marca form-control @error('id_marca') is-invalid @enderror" id="id_procedencia">
                                <option disabled selected value="">Seleccione</option>
                                @foreach($procedencia as $vitolas)
                                    <option value="{{$vitolas->id}}" @if(Request::old('id_vitolas')==$vitolas->id){{'selected'}}@endif
                                    @if(session("idMarca"))
                                        {{session("idMarca")==$vitolas->id ? 'selected="selected"':''}}
                                            @endif>{{$vitolas->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="subir">
                            <label class="form-check-label" for="flexCheckDefault">
                              Realizar entrada
                            </label>
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
                    <h5 class="modal-title" style="color: white"><span class="fas fa-plus"></span> Editar Norma
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: white">&times;</span>
                    </button>
                </div>
                <form id="nuevoP" method="POST" action="{{route("BultoSalidaeditar")}}" >
                    @method("PUT")

                    @csrf
                    <div class="modal-body">
                        <div hidden>

                        
                        <div class="form-group">
                            <label for="totalcapaentrega">Total</label>
                            <input readonly class="form-control @error('name') is-invalid @enderror"
                                    name="total" id="totalcapaentrega" maxlength="100" value="{{old('total')}}">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="empleadoEditarcapaentrega">Seleccione el Empleado:
                            </label>
                            <br>
                            <select name="id_empleado"
                                    required="required"
                                    style="width: 100%"
                                    class="empresa form-control @error('id_categoria') is-invalid @enderror"
                                    id="empleadoEditarcapaentrega" >
                                <option disabled selected value="">Seleccione</option>
                                @foreach($empleados as $empleado)
                                    <option value="{{$empleado->id}}">{{$empleado->nombre}}</option>
                                @endforeach
                            </select>
                            <!---- Boton para crear un nuevo tipo de categoria- -->
                        </div>
                        <div class="form-group">
                            <label for="vitolacapaentrega">Seleccione la vitola</label>
                            <br>
                            <select name="id_vitolas"
                                    required
                                    style="width: 100%"
                                    class="empresa form-control @error('id_empresa') is-invalid @enderror"
                                    id="vitolacapaentrega" required="required">
                                <option disabled selected value="">Seleccione</option>
                                @foreach($vitola as $vitolas)
                                    <option value="{{$vitolas->id}}">{{$vitolas->name}}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_empresa')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="marcacapaentrega">Seleccione la marca</label>
                            <br>
                            <select name="id_marca"
                                    style="width: 100%"
                                    class="empresa form-control @error('id_marca') is-invalid @enderror"
                                    id="marcacapaentrega" required="required">
                                <option disabled selected value="">Seleccione</option>
                                @foreach($marca as $marca)
                                    <option value="{{$marca->id}}">{{$marca->name}}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_marca')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>


                        <div class="form-group">
                            <label for="semillacapaentrega">Seleccione la semilla</label>
                            <br>
                            <select name="id_semilla"
                                    style="width: 100%"
                                    class="empresa form-control @error('id_marca') is-invalid @enderror"
                                    id="semillacapaentrega" required="required">
                                <option disabled selected value="">Seleccione</option>
                                @foreach($semilla as $marca)
                                    <option value="{{$marca->id}}">{{$marca->name}}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_marca')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="variedadcapaentrega">Seleccione la Variedad</label>
                            <br>
                            <select name="id_variedad"
                                    style="width: 100%"
                                    class="empresa form-control @error('id_marca') is-invalid @enderror"
                                    id="variedadcapaentrega" required="required">
                                <option disabled selected value="">Seleccione</option>
                                @foreach($variedad as $marca)
                                    <option value="{{$marca->id}}">{{$marca->name}}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_marca')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="tamanocapaentrega">Seleccione el Tamaño</label>
                            <br>
                            <select name="id_tamano"
                                    style="width: 100%"
                                    class="empresa form-control @error('id_marca') is-invalid @enderror"
                                    id="tamanocapaentrega" required="required">
                                <option disabled selected value="">Seleccione</option>
                                @foreach($tamano as $marca)
                                    <option value="{{$marca->id}}">{{$marca->name}}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_marca')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="procedenciacapaentrega">Seleccione la Procedencia</label>
                            <br>
                            <select name="id_procedencia"
                                    style="width: 100%"
                                    class="empresa form-control @error('id_marca') is-invalid @enderror"
                                    id="procedenciacapaentrega" required="required">
                                <option disabled selected value="">Seleccione</option>
                                @foreach($procedencia as $marca)
                                    <option value="{{$marca->id}}">{{$marca->name}}
                                    </option>
                                @endforeach
                            </select>
                            @error('id_marca')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        </div>

                        <div class="form-group">
                            <label for="procedenciacapaentrega">Seleccione la Combinacion</label>
                            <br>
                            <select name="norma"
                                    style="width: 100%"
                                    class="empresa form-control"
                                    id="normaenviar" required="required">
                                @foreach($norma as $val)
                                    <option value="{{$val->id}}">{{$val->id}}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <input id="id_producto" name="id" type="hidden" >
                        <button type="submit" class="btn btn-success" id="id_producto" onclick="f()">Editar</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!------------------MODAL VER PRODUCTO-------------------------------->
    <div class="modal fade" id="modalVerCapaEntrega" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #2a2a35">
                    <h5 class="modal-title" style="color: white"><span class="fas fa-plus"></span> Detalle de la Salida De Bulto
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
                                <div class="col-sm-6"><label for="marcacapaentrega"><strong>Marca:</strong></label></div>
                                <div class="col-sm-2"><label for="marca" id="marcacapaentrega"></label></div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6"><label for="vitolacapaentrega"><strong>Vitola:</strong></label></div>
                                <div class="col-sm-2"><label for="precioLoteProducto" id="vitolacapaentrega"></label></div>
                            </div>


                            <div class="form-group row">
                                <div class="col-sm-6"><label for="totalcapaentrega"><strong>Total Entregado:</strong></label></div>
                                <div class="col-sm-2"><label for="disponible" id="totalcapaentrega"></label></div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6"><label for="empleadoNuevocapaentrega"><strong>Banda Adicional</strong></label></div>
                                <div class="col-sm-2"><label for="marca" id="empleadoNuevocapaentrega"></label></div>
                            </div>

                            <div class="form-group row">
                                <div class="col-sm-6"><label for="combi"><strong>Combinacion</strong></label></div>
                                <div class="col-sm-2"><label for="marca" id="combix"></label></div>
                            </div>


                        </div>
                    </div>
                    <div class="modal-footer">
                        <button data-dismiss="modal" class="btn btn-success">Aceptar</button>
                    </div>
            </div>
        </div>
    </div>



    <!------------------MODAL BORRAR PRODUCTO---------------------------->
    <div class="modal fade" id="modalBorrarCapaEntrega" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <form method="post" action="{{route("BultoSalidaborrar")}}" >
                    @method("DELETE")
                    @csrf
                    <div class="modal-header" style="background: #2a2a35">
                        <h5 class="modal-title" style="color: white"><span class="fas fa-trash"></span> Borrar Salida De Bulto
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span style="color: white" aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>¿Estás seguro que deseas borrar esta Salida De Bulto? <label
                                id="nombreProducto"></label></p>

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
                <form id="nuevoP" method="POST" action="{{route("exportarbultoentrega")}}" enctype="multipart/form-data">

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
    <!----------------------------------------------------MODAL fecha Exportar PDF------------------------------------------------------->

    <div class="modal fade" id="modalfechapdf" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #2a2a35">
                    <h5 class="modal-title" style="color: white"><span class="fas fa-plus"></span> Exportar PDF
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: white">&times;</span>
                    </button>
                </div>
                <form id="nuevoP" method="POST" action="{{route("exportarbultoentregapdf")}}" enctype="multipart/form-data">

                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="fecha1">Fecha</label>
                            <input class="form-control @error('name') is-invalid @enderror" name="fecha1" id="fecha1"
                                   type="datetime-local"
                                   value="{{ old('fecha1')}}">
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
    <!----------------------------------------------------MODAL fecha Exportar CVS------------------------------------------------------->
    <div class="modal fade" id="modalfechacvs" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #2a2a35">
                    <h5 class="modal-title" style="color: white"><span class="fas fa-plus"></span> Exportar CVS
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: white">&times;</span>
                    </button>
                </div>
                <form id="nuevoP" method="POST" action="{{route("exportarbultoentregaacvs")}}" enctype="multipart/form-data">

                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="fecha1">Fecha</label>
                            <input class="form-control @error('name') is-invalid @enderror" name="fecha1" id="fecha1"
                                   type="datetime-local"
                                   value="{{ old('fecha1')}}">
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

    <!-------------------MODAL sumar 75------------------------------>
    <div class="modal fade" id="modalSumar75" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <form method="post" action="{{route("sumarBulto")}}" >
                    @method("PUT")
                    @csrf
                    <div class="modal-header" style="background: #2a2a35">
                        <h5 class="modal-title" style="color: white"><span class="fas fa-trash"></span> Agregar un Nuevo Bulto
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span style="color: white" aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <p>¿Estás seguro que deseas agregar  Un Bulto<label
                                id="nombreProducto"></label>?</p>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="subir">
                                    <label class="form-check-label" for="flexCheckDefault">
                                    Con Entrada.
                                    </label>
                                </div>

                    </div>

                    <div class="modal-footer">
                        <input type="hidden" name="fecha" @isset($fecha)
                        value="{{$fecha}}"
                        @endisset >
                        <input id="id_capa_entrega" name="id" type="hidden" value="">
                        <input id="id_capa_entrega" name="id" type="hidden" value="">
                        <input id="id_capa_entrega" name="id" type="hidden" value="">
                        <button type="submit" class="btn btn-success">Sumar</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>

        </div>
    </div>



    <div class="modal fade" id="modalRestar75" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <form method="post" action="{{route("restarBulto")}}" >
                    @method("PUT")
                    @csrf
                    <div class="modal-header" style="background: #2a2a35">
                        <h5 class="modal-title" style="color: white"><span class="fas fa-trash"></span> Quitar un Bulto
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span style="color: white" aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <p>¿Estás seguro que deseas quitar  Un Bulto<label
                                id="nombreProducto"></label>?</p>

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="subir">
                                    <label class="form-check-label" for="flexCheckDefault">
                                    Con Disminucion de entrada.
                                    </label>
                                </div>

                    </div>
                    <div class="modal-footer">
                        <input id="id_capa_entregass" name="id" type="hidden" value="">
                        <input type="hidden" name="fecha" @isset($fecha) value="{{$fecha}} @endisset">
                        <button type="submit" class="btn btn-success">Restar</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>

        </div>
    </div>


    <div class="modal fade" id="modalSumar100" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <form method="post" action="{{route("sumar100BandaBulto")}}" >
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
                        <p>¿Estás seguro que deseas  Sumar 100 <label
                                id="nombreProducto"></label>?</p>

                    </div>
                    <div class="modal-footer">
                        <input id="id_capa_entrega" name="id" type="hidden" value="">
                        <button type="submit" class="btn btn-success" id="id_capa_entrega" name="id" >Sumar</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>

        </div>
    </div>



    <div class="modal fade" id="modalRestar100" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <form method="post" action="{{route("restar100BandaBulto")}}" >
                    @method("PUT")
                    @csrf
                    <div class="modal-header" style="background: #2a2a35">
                        <h5 class="modal-title" style="color: white"><span class="fas fa-trash"></span> Deduccion
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span style="color: white" aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>¿Estás seguro que deseas  disminuir 100 bandas adicionales<label
                                id="nombreProducto"></label>?</p>

                    </div>
                    <div class="modal-footer">
                        <input id="id_capa_entregap" name="ids" type="hidden" >
                        <button type="submit" class="btn btn-success" id="id_capa_entrega" name="id" >Restar</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- Modal para exportar pesos por marcas, reporte para Paolo -->
    <div class="modal fade" id="modalfechamarca" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #2a2a35">
                    <h5 class="modal-title" style="color: white"><span class="fas fa-plus"></span> Exportar Peso Por Marca
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: white">&times;</span>
                    </button>
                </div>
                <form id="nuevoP" method="POST" action="{{route("exportarbultoentregamp")}}" enctype="multipart/form-data">

                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="fecha1">Fecha</label>
                            <input class="form-control @error('name') is-invalid @enderror" name="fecha1" id="fecha1"
                                   type="date"
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



    <div class="modal fade" id="modalconfirmar" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #2a2a35">
                    <h5 class="modal-title" style="color: white"><span class="fas fa-plus"></span> Generar Salida de Materia Prima
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: white">&times;</span>
                    </button>
                </div>
                <form id="nuevoP" method="POST" action="{{route("generarbultosmp")}}" enctype="multipart/form-data">

                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="fecha1">Esta seguro que desea generar la salida de Materia Prima
                             </label>
                            <label for="">para la fecha {{$fecha}} ?</label>
                            <input class="form-control" name="fecha1" id="fecha1" type="hidden"
                                   value="{{$fecha}}" >
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



    <!-------------------MODAL NUEVO CATEGORIA------------------------------>

    {{-- <div class="modal fade" id="modalNuevaCategoria" tabindex="-1" role="dialog">
         <div class="modal-dialog" role="document">
             <div class="modal-content">
                 <div class="modal-header" style="background: #2a2a35">
                     <h5 class="modal-title" style="color: white"><span class="fas fa-plus"></span> Agregar Categoría
                     </h5>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                         <span aria-hidden="true" style="color: white">&times;</span>
                     </button>
                 </div>
                 <form method="POST" action="{{route("CapaEntrega")}}" enctype="multipart/form-data">

                     @csrf
                     <div class="modal-body">
                         <div class="form-group">
                             <label for="nombreNuevaCategoria">Nombre de categoria</label>
                             <input required class="form-control" name="name"
                                    id="nombreNuevaCategoria" maxlength="100">
                         </div>
                         <div class="form-group">
                             <label for="tipoNuevaCategoria">Seleccione el tipo de Categoria

                             </label>
                             <br>
                             <select name="id_categoria"
                                     required
                                     style="width: 85%"
                                     class="empresa2 form-control" id="tipoNuevaCategoria">
                                 <option disabled selected value="">Seleccione</option>
                                 @foreach($calidad as $calidad)
                                     <option value="{{$calidad->id}}" @if(session("idNuevaCategoria"))
                                         {{session("idNuevaCategoria") == $tipoCategoria->id ? 'selected="selected"':''}}
                                         @endif>{{$calidad->name}}</option>
                                 @endforeach
                             </select>
                             <!---- Boton para crear un nuevo tipo de categoria- -->

                         </div>
                         <div class="form-group">
                             <label for="descripcionNuevaCategoria">Descripción de nueva categoria (Opcional):</label>
                             <textarea class="form-control"
                                       name="descripcion"
                                       id="descripcionNuevaCategoria"
                                       maxlength="192"></textarea>
                         </div>
                         <label for="imagenCategoria">Seleccione una imagen (opcional): </label>
                         <div class="input-group image-preview">

                             <input type="text" name="imagen_url" class="form-control image-preview-filename"
                                    disabled="disabled">
                             <!-- don't give a name === doesn't send on POST/GET -->
                             <span class="input-group-btn">
                                 <!-- image-preview-clear button -->
                                 <button type="button" class="btn btn-outline-danger image-preview-clear"
                                         style="display:none;">
                                     <span class="fas fa-times"></span> Clear
                                 </button>
                                 <!-- image-preview-input -->
                                 <div class="btn btn-default image-preview-input">
                                     <span class="fas fa-folder-open"></span>
                                     <span class="image-preview-input-title">Seleccionar</span>
                                     <input type="file" accept="image/png, image/jpeg, image/gif"
                                            name="imagen_url"/>
                                     <!-- rename it -->
                                 </div>
                             </span>
                         </div><!-- /input-group image-preview [TO HERE]-->
                     </div>
                     <div class="modal-footer">
                         <button type="submit" class="btn btn-success" >Crear</button>
                         <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                     </div>
                 </form>

             </div>
         </div>
     </div>


--}}

<script>

function peticion(){
            let vitola= $('#vitolacapaentrega1').val();
            let marca= $('#marca1').val();
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
            let combinacion = $("#combinaciones");
            combinacion.attr('disabled', false);
            combinacion.empty();
            for (let i = 0; i < data.length; i++) {
                combinacion.append
                ("<option class='item" + data[i].Id + "' value='"+data[i].Id+"'>"
                +
                data[i].Id
                +
                " </option>");
            }            
        }

        function reset(){
            let combinacion = $("#combinaciones");
            combinacion.attr('disabled', true);
            combinacion.empty();            
        }

        function normaenviar(id){
            $("#normaenviar").val(id);
            $('#normaenviar').trigger('change');
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
