@extends("layouts.MenuBanda")
@section("content")
    <div class="container-fluid">
        <h1 class="mt-4">Consumo De Banda
            <div class="btn-group" role="group">

                <button class="btn btn-sm btn-success"
                        id="botonAbrirModalNuevoConsumo"
                        data-toggle="modal" data-target="#modalNuevoConsumo">
                    <span class="fas fa-plus"></span> Nueva
                </button>
            </div>

        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item" aria-current="page" ><a href="/">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Entrega De Banda</li>

                <form  class="d-none d-md-inline-block form-inline
                           ml-auto mr-0 mr-md-2 my-0 my-md-0 mb-md-2">
                    <div class="input-group" style="width: 600px">
                    <input class="form-control" name="search" type="search" placeholder="Marca"
                               aria-label="Search" value="">
                    <input class="form-control" name="semilla" type="search" placeholder="Semilla"
                               aria-label="Search" value="">
                    <input class="form-control" name="fecha" type="date" placeholder="fecha"
                               aria-label="Search" @isset($fecha)
                                   value="{{$fecha}}"
                               @endisset>
                        <div class="input-group-append">
                            <a id="borrarBusqueda" class="btn btn-danger hideClearSearch" style="color: white"
                               href="{{route("ConsumoBanda")}}">&times;</a>
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </ol>

            <div class="pagination pagination-sm">

                <a class="btn btn-dark hideClearSearch" style="color: white"
                   id="botonAbrirModalNuevoRecepcionCapa"
                   onclick="mostrarDiferencias('{{$fecha}}')"
                   data-toggle="modal" >Comparativo</a>

                <a class="btn btn-success hideClearSearch" style="color: white"
                   id="botonAbrirModalNuevoRecepcionCapa"
                   data-toggle="modal" data-target="#modalfecha">Excel</a>

                <a class="btn btn-dark hideClearSearch" style="color: white"
                   id="botonAbrirModalNuevoRecepcionCapa"
                   data-toggle="modal" data-target="#recalcular">Calcular</a>

                <a class="btn btn-info hideClearSearch" style="color: white"
                   id="EMarcas"
                   data-toggle="modal" data-target="#modalfechamarca">E. Marcas</a>

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
                       document.getElementById("botonAbrirModalNuevoConsumo").click();
                    }
                }
            </script>
        @endif

        <table class="table">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Marca</th>
                <th>Vitola</th>
                <th>Semilla</th>
                <th>Variedad</th>
                <th>Tamaño</th>
                <th>Procedencia</th>
                <th>Total</th>
                <th hidden>Peso</th>
                <th>Total En Libras</th>
                <th><span class="fas fa-info-circle"></span></th>


                <th><span class="fas fa-info-circle"></span></th>
            </tr>
            </thead>
            <tbody>
            @if($consumoBanda->count()<= 0)
                <tr>
                    <td colspan="4" style="align-items: center">No hay productos</td>
                </tr>
            @endif
            @foreach($consumoBanda as $productos)
                <tr>
                    <td>{{$noPagina++}}</td>


                    <td>{{$productos->nombre_marca}}</td>
                    <td>{{$productos->nombre_vitolas}}</td>
                    <td>{{$productos->nombre_semillas}}</td>
                    <td>{{$productos->nombre_variedad}}</td>
                    <td>{{$productos->nombre_tamano}}</td>
                    <td>{{$productos->nombre_procedencia}}</td>
                    <td>{{$productos->total}}</td>
                    <td hidden>{{$productos->onzas}}</td>
                    <td>{{$productos->libras}}</td>

                    <td>

                        <button class="btn btn-sm btn-info"
                                data-toggle="modal"
                                data-target="#modalSumar100"
                                data-id="{{$productos->id}}"
                                title="100">
                            100 </button>

                        <button class="btn btn-sm btn-success"
                                data-toggle="modal"
                                data-target="#modalSumar"
                                data-id="{{$productos->id}}"
                                title="Agregar">
                            <span class="fas fa-plus"></span>  </button>
                    </td>
                    <td>

                        <button class="btn btn-sm btn-success"
                                id="editarCapaEntrega{{$productos->id}}"
                                data-toggle="modal"
                                data-target="#modalEditarCapaEntrega"
                                data-id="{{$productos->id}}"
                                data-id_semilla="{{$productos->id_semillas}}"
                                data-id_tamano="{{$productos->id_tamano}}"
                                data-id_marca="{{$productos->id_marca}}"
                                data-id_vitolas="{{$productos->id_vitolas}}"
                                data-total="{{$productos->total}}"
                                data-onzas="{{$productos->onzas}}"
                                data-libras="{{$productos->libras}}"
                                data-variedad="{{$productos->variedad}}"
                                data-procedencia="{{$productos->procedencia}}"
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
                    </td>
                </tr>
            @endforeach

            <tr>
                <td colspan="7" style="text-align:center;">
                    <strong>
                        TOTAL
                    </strong></td>

                    <?php
                    $totalI = 0;

                foreach($consumoBanda as $productos){
                    $totalI+=$productos->total;
                }
                ?>
                <td>
                    <strong>{{$totalI}}</strong>
                </td>

                <?php
                $totalLibras = 0;

            foreach($consumoBanda as $productos){
                $totalLibras+=$productos->libras;
            }
            ?>
            <td>
                <strong>{{$totalLibras}}</strong>
            </td>
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
                    <h5 class="modal-title" style="color: white"><span class="fas fa-plus"></span>  Entrega  De Banda
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: white">&times;</span>
                    </button>
                </div>
                <form id="nuevoP" method="POST" action="{{route("ConsumoBandanueva")}}" enctype="multipart/form-data">

                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="nombreNuevoProducto">Total</label>
                            <input class="form-control @error('name') is-invalid @enderror" name="total" id="nombreNuevoProducto" maxlength="100"
                                   value="{{ old('total')}}" required="required">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="onzasNuevoProducto">onzas</label>
                            <input class="form-control @error('onzas') is-invalid @enderror" name="onzas" id="onzasNuevoProducto" maxlength="100"
                                   value="{{ old('onzas')}}" required="required">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="fecha">Fecha</label>
                            <input class="form-control @error('onzas') is-invalid @enderror" name="fecha" id="fecha" maxlength="100"
                                   value="{{ old('fecha')}}" type="date" >
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="id_marca">Seleccione la marca</label>
                            <br>
                            <select name="id_marca"
                                    style="width: 100%" required="required"
                                    class=" marca form-control @error('id_marca') is-invalid @enderror" id="marca">
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
                            <label for="vitolacapaentrega">Seleccione la Vitola</label>
                            <br>
                            <select name="id_vitolas"
                                    style="width: 100%" required="required"
                                    class=" marca form-control @error('id_marca') is-invalid @enderror" id="vitolacapaentrega">
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
                            <label for="semillacapaentrega">Seleccione  semilla</label>
                            <br>
                            <select name="id_semillas"
                                    style="width: 100%" required="required"
                                    class=" marca form-control @error('id_marca') is-invalid @enderror" id="semillacapaentrega">
                                <option disabled selected value="">Seleccione</option>
                                @foreach($semilla as $semillas)
                                    <option value="{{$semillas->id}}" @if(Request::old('id_semillas')==$semillas->id){{'selected'}}@endif
                                    @if(session("idMarca"))
                                        {{session("idMarca")==$semillas->id ? 'selected="selected"':''}}
                                        @endif>{{$semillas->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tamanocapaentrega">Seleccione tamaño</label>
                            <br>
                            <select name="id_tamano"
                                    style="width: 100%" required="required"
                                    class=" marca form-control @error('id_marca') is-invalid @enderror" id="tamanocapaentrega">
                                <option disabled selected value="">Seleccione</option>
                                @foreach($tamano as $tamanos)
                                    <option value="{{$tamanos->id}}" @if(Request::old('id_tamano')==$tamanos->id){{'selected'}}@endif
                                    @if(session("idMarca"))
                                        {{session("idMarca")==$tamanos->id ? 'selected="selected"':''}}
                                        @endif>{{$tamanos->name}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="variedadcapaentregaa">variedad</label>
                            <br>
                            <select name="id_variedad"
                                    style="width: 100%" required="required"
                                    class=" marca form-control @error('id_marca')
                                        is-invalid @enderror" id="variedadcapaentregaa">
                                <option disabled selected value="">Seleccione</option>
                                @foreach($variedad as $variedades)
                                    <option value="{{$variedades->id}}" @if(Request::old('id_variedad')==$variedades->id){{'selected'}}@endif
                                    @if(session("idMarca"))
                                        {{session("idMarca")==$variedades->id ? 'selected="selected"':''}}
                                        @endif>{{$variedades->name}}
                                    </option>
                                @endforeach
                            </select>
                            <!---- Boton para crear un nuevo tipo de categoria- -->
                        </div>
                        <div class="form-group">
                            <label for="procedenciacapaentregaa">Procedencia
                            </label>
                            <br>
                            <select name="id_procedencia"
                                    style="width: 100%" required="required"
                                    class=" marca form-control @error('id_marca')
                                        is-invalid @enderror" id="procedenciacapaentregaa">
                                <option disabled selected value="">Seleccione</option>
                                @foreach($procedencia as $procedencias)
                                    <option value="{{$procedencias->id}}" @if(Request::old('id_procedencia')==$procedencias->id){{'selected'}}@endif
                                    @if(session("idMarca"))
                                        {{session("idMarca")==$procedencias->id ? 'selected="selected"':''}}
                                        @endif>{{$procedencias->name}}
                                    </option>
                                @endforeach
                            </select>
                            <!---- Boton para crear un nuevo tipo de categoria- -->
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
                    <h5 class="modal-title" style="color: white"><span class="fas fa-plus"></span> Editar Entrega De Banda
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: white">&times;</span>
                    </button>
                </div>
                <form id="nuevoP" method="POST" action="{{route("ConsumoBandaeditar")}}" >
                    @method("PUT")

                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="totalcapaentrega">Total</label>
                            <input  class="form-control @error('name') is-invalid @enderror"
                                    name="total" id="totalcapaentrega" maxlength="100"
                                    required="required"type="number" value="{{old('total')}}">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="onzascapaentrega">Onzas</label>
                            <input  class="  form-control @error('name') is-invalid @enderror"
                                    name="onzas" id="onzascapaentrega" maxlength="100"
                                    required="required" type="" value="{{old('onzas')}}">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
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
                            <label for="tamanocapaentrega">Seleccione el Tamaño</label>
                            <br>
                            <select name="id_tamano"
                                    style="width: 100%"
                                    class=" empresa form-control @error('id_marca') is-invalid @enderror"
                                    id="tamanocapaentrega" required="required">
                                <option disabled selected value="">Seleccione</option>
                                @foreach($tamano as $tamanos)
                                    <option value="{{$tamanos->id}}">{{$tamanos->name}}
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
                            <label for="semillacapaentrega">Seleccione la Semilla</label>
                            <br>
                            <select name="id_semillas"
                                    style="width: 100%"
                                    class=" empresa form-control @error('id_marca') is-invalid @enderror"
                                    id="semillacapaentrega" required="required">
                                <option disabled selected value="">Seleccione</option>
                                @foreach($semilla as $semillas)
                                    <option value="{{$semillas->id}}">{{$semillas->name}}
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
                            <label for="variedadcapaentregaa">variedad
                            </label>
                            <br>
                            <select name="id_variedad"
                                    required="required"
                                    style="width: 100%"
                                    class="empresa form-control
                            @error('disponible') is-invalid @enderror" id="variedadcapaentregaa">
                                <option disabled selected value="">Seleccione</option>
                                @foreach($variedad as $variedades)
                                    <option value="{{$variedades->id}}">{{$variedades->name}}
                                    </option>
                                @endforeach
                            </select>
                            <!---- Boton para crear un nuevo tipo de categoria- -->
                        </div>
                        <div class="form-group">
                            <label for="procedenciacapaentregaa">prosedencia
                            </label>
                            <br>
                            <select name="id_procedencia"
                                    required="required"
                                    style="width: 100%"
                                    class="empresa form-control
                                    @error('disponible') is-invalid @enderror" id="procedenciacapaentregaa">
                                <option disabled selected value="">Seleccione</option>
                                @foreach($procedencia as $procedencias)
                                    <option value="{{$procedencias->id}}">{{$procedencias->name}}
                                    </option>
                                @endforeach  </select>
                            <!---- Boton para crear un nuevo tipo de categoria- -->
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

    <!------------------MODAL BORRAR PRODUCTO---------------------------->
    <div class="modal fade" id="modalBorrarCapaEntrega" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <form method="post" action="{{route("ConsumoBandaborrar")}}" >
                    @method("DELETE")
                    @csrf
                    <div class="modal-header" style="background: #2a2a35">
                        <h5 class="modal-title" style="color: white"><span class="fas fa-trash"></span> Borrar Salida
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span style="color: white" aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>¿Estás seguro que deseas borrar la Entrega De Banda ? <label
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
                <form id="nuevoP" method="POST" action="{{route("exportarconsumobanda")}}" enctype="multipart/form-data">

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
                <form id="nuevoP" method="POST" action="{{route("exportarconsumobandapdf")}}" enctype="multipart/form-data">

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
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #2a2a35">
                    <h5 class="modal-title" style="color: white"><span class="fas fa-plus"></span> Comparativo
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: white">&times;</span>
                    </button>
                </div>
                    <div class="modal-body">
                        <table class="table">
                            <tr>
                            <th>Descripcion</th>
                            <th>Inventario</th>
                            <th>Consumo</th>
                            <th>Diferencias</th>
                            </tr>

                            <tr>
                                <tbody id="comparativo">
                                </tbody>
                            </tr>
                        </table>
                        </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" data-dismiss="modal">OK</button>
                    </div>
                    </div>

            </div>
        </div>


    <!--MODAL CALCULAR PESOS  !-->
    <div class="modal fade" id="recalcular" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #2a2a35">
                    <h5 class="modal-title" style="color: white"><span class="fas fa-plus"></span> Recalcular Libras
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: white">&times;</span>
                    </button>
                </div>
                <p>¿ Esta seguro que desea recalcular el peso por banda ?</p>
                <form id="nuevoP" method="POST" action="{{route("recalcularconsumobanda")}}" enctype="multipart/form-data">
                    @csrf
                    @isset($fecha)
                    <input name="fecha" type="hidden" value="{{$fecha}}">
                @endisset
                    <div class="modal-footer">
                        <button type="submit" id="nuevoP" class="btn btn-success">Recalcular</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-------------------MODAL sumar  100------------------------------>


    <div class="modal fade" id="modalSumar100" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <form method="post" action="{{route("sumar100EntregaBanda")}}" >
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
                        <input id="id_capa_entrega" name="id" type="hidden">
                        <button type="submit" class="btn btn-success" id="id_capa_entrega" name="id" >Sumar</button>
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
                <form id="nuevoP" method="POST" action="{{route("exportbandas")}}" enctype="multipart/form-data">

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



    <!-------------------MODAL sumar  50------------------------------>
    <div class="modal fade" id="modalSumar" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <form method="post" action="{{route("sumarEntregaBanda")}}" >
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
                            <label for="suma">Tota a Sumar</label>
                            <input class="form-control @error('name') is-invalid @enderror" name="suma" id="suma" maxlength="100"
                                   value="{{ old('suma')}}" required="required">
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

    <script>
        function mostrarDiferencias(fecha){
            let _token= "{{ csrf_token() }}";
            $.ajax({
            type: 'get',
            url: '/ConsumoBanda/comparativo',
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
            $("#comparativo").empty();
            for (let i = 0; i < data.length; i++) {
                $("#comparativo").append(
                "<tr> <td>"+data[i].nombre+"</td> <td>"
                    + data[i].inv + "</td><td>" 
                    + data[i].con + "</td> <td>"
                    + data[i].diferencia + "</td> </tr> "
            )
            }
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
