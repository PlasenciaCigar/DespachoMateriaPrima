@extends("layouts.MenuBanda")
@section("content")
    <div class="container-fluid">
        <h1 class="mt-4">Inventario Diario Bultos
            <div class="btn-group" role="group">

                <button class="btn btn-sm btn-success"
                        id="botonAbrirModalNuevoI"
                        data-toggle="modal" data-target="#modalNuevoI">
                    <span class="fas fa-plus"></span> Nueva
                </button>
            </div>

        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item" aria-current="page" ><a href="/">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Inventario Diario Bultos</li>

                <form  class="d-none d-md-inline-block form-inline
                           ml-auto mr-0 mr-md-2 my-0 my-md-0 mb-md-2">
                    <div class="input-group" style="width: 600px">
                    <input class="form-control" name="search" type="text" placeholder="Marca"
                               aria-label="Search" value="">
                        @isset($fecha)
                        <input class="form-control" name="fecha" type="date" placeholder="fecha"
                               aria-label="Search" value="{{$fecha}}"
                               @endisset>
                        <div class="input-group-append">
                            <a id="borrarBusqueda" class="btn btn-danger hideClearSearch" style="color: white"
                               href="{{route("InventarioDiario")}}">&times;</a>
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </ol>
            <div class="row">
            <div>
                <?php $hoy=date("Y-m-d"); $o=date("Y-m-d",strtotime($hoy."-1 days"));?>
                @if ($fecha == $hoy || @Auth::user()->is_admin==1)
                <button class="btn btn-danger"
                title="Borrar"
                data-toggle="modal"
                data-target="#deleteall"
                data-id="{{$fecha}}">
                A.Inventario</button>
                @endif
        </div>

        <div>
            <button class="btn btn-danger"
                title="Borrar"
                data-toggle="modal"
                data-target="#cleaner"
                data-id="{{$fecha}}">
                Limpiar
            </button>
        </div>

        </div>


        <div class="modal fade" id="deleteall" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <form method="post" action="{{route("InventarioDiarioborrarall")}}" >
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
                            <p>¿Estás seguro que desea actualizar el inventario del <label
                                    id="nada">@isset($fecha)
                                        {{$fecha}}
                                    @endisset</label>?</p>

                        </div>
                        <div class="modal-footer">
                            <input id="id_capa_entrega" name="fecha" @isset($fecha)
                                value="{{$fecha}}"
                            @endisset type="hidden">
                            <button type="submit" class="btn btn-danger">Actualizar</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>



        <div class="modal fade" id="cleaner" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <form method="post" action="{{route("InventarioDiariolimpiarall")}}" >
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
                            <p>¿Estás seguro que deseas eliminar todas las marcas con valores nulos? del <label
                                    id="nada">@isset($fecha)
                                        {{$fecha}}
                                    @endisset</label>?</p>

                        </div>
                        <div class="modal-footer">
                            <input id="id_capa_entrega" name="fecha" @isset($fecha)
                                value="{{$fecha}}"
                            @endisset type="hidden">
                            <button type="submit" class="btn btn-danger">Limpiar</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>

            <div class="pagination pagination-sm">

                <a class="btn btn-dark hideClearSearch" style="color: white"
                   data-toggle="modal" onclick="mostrarDiferencias('{{$fecha}}')">Dif. Norma</a>

                <a class="btn btn-success hideClearSearch" style="color: white"
                   id="botonAbrirModalNuevoRecepcionCapa"
                   data-toggle="modal" data-target="#modalfecha">Excel</a>

                   <a class="btn btn-warning hideClearSearch" style="color: white"
                   id="botonAbrirModalNuevoRecepcionCapa"
                   data-toggle="modal" data-target="#recalcular">Calcular</a>

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
                       document.getElementById("botonAbrirModalNuevoI").click();
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
                <th>Inicial</th>
                <th>Peso</th>
                <th>Entradas</th>
                <th>Peso</th>
                <th>Final</th>
                <th>Peso</th>
                <th>Consumo</th>
                <th>Peso</th>


                <th><span class="fas fa-info-circle"></span></th>
            </tr>
            </thead>
            <tbody>
            @if(!$invDiario)
                <tr>
                    <td colspan="4" style="align-items: center">No hay productos</td>
                </tr>
            @endif
            @foreach($invDiario as $productos)
                <tr @if ($productos->onzas==null)
                    class="table-danger"
                @endif>
                    <td>{{$noPagina++}}</td>


                    <td>{{$productos->nombre_marca}}</td>
                    <td>{{$productos->nombre_vitolas}}</td>
                    <td>{{$productos->totalinicial}}</td>
                    <td>{{$productos->pesoinicial}}</td>
                    <td>{{$productos->totalentrada}}</td>
                    <td>{{$productos->pesoentrada}}</td>
                    <td>{{$productos->totalfinal}}</td>
                    <td>{{$productos->pesofinal}}</td>
                    <td>{{$productos->totalconsumo}}</td>
                    <td>{{$productos->pesoconsumo}}</td>
                    <td>




                        <button class="btn btn-sm btn-success"
                                id="editarCapaEntrega{{$productos->id}}"
                                data-toggle="modal"
                                data-target="#modalEditarCapaEntrega"
                                data-id="{{$productos->id}}"
                                data-id_marca="{{$productos->id_marca}}"
                                data-id_vitolas="{{$productos->id_vitolas}}"
                                data-totalinicial="{{$productos->totalinicial}}"
                                data-totalentrada="{{$productos->totalentrada}}"
                                data-totalfinal="{{$productos->totalfinal}}"
                                data-totalconsumo="{{$productos->totalconsumo}}"
                                data-onzas="{{$productos->onzas}}"
                                title="Editar">
                            <span class="fas fa-pencil-alt"></span>
                        </button>
                        <button class="btn btn-sm btn-danger"
                                title="Borrar"
                                data-toggle="modal"
                                data-target="#modalBorrarReBulDiario"
                                data-id="{{$productos->id}}">
                            <span class="fas fa-trash"></span>
                        </button>
                    </td>
                </tr>

            @endforeach
            <tr>
                <td colspan="3" style="text-align:center;">
                    <strong>
                        TOTAL
                    </strong></td>
            <?php
                    $totalI = 0;

                foreach($invDiario as $productos){
                    $totalI+=$productos->totalinicial;
                }
                ?>
                <td>
                    <strong>{{$totalI}}</strong>
                </td>

                <?php
                    $pesoI = 0;

                foreach($invDiario as $productos){
                    $pesoI+=$productos->pesoinicial;
                }
                ?>
                <td>
                    <strong>{{$pesoI}}</strong>
                </td>


                <?php
                    $totalent = 0;

                foreach($invDiario as $productos){
                    $totalent+=$productos->totalentrada;
                }
                ?>
                <td>
                    <strong>{{$totalent}}</strong>
                </td>


                <?php
                    $pesoentr = 0;

                foreach($invDiario as $productos){
                    $pesoentr+=$productos->pesoentrada;
                }
                ?>
                <td>
                    <strong>{{$pesoentr}}</strong>
                </td>

                <?php
                    $totalfina = 0;

                foreach($invDiario as $productos){
                    $totalfina+=$productos->totalfinal;
                }
                ?>
                <td>
                    <strong>{{$totalfina}}</strong>
                </td>

                <?php
                    $pesofina = 0;

                foreach($invDiario as $productos){
                    $pesofina+=$productos->pesofinal;
                }
                ?>
                <td>
                    <strong>{{$pesofina}}</strong>
                </td>


                <?php
                    $totalcons = 0;

                foreach($invDiario as $productos){
                    $totalcons+=$productos->totalconsumo;
                }
                ?>
                <td>
                    <strong>{{$totalcons}}</strong>
                </td>

                <?php
                    $pesocons = 0;

                foreach($invDiario as $productos){
                    $pesocons+=$productos->pesoconsumo;
                }
                ?>
                <td>
                    <strong>{{$pesocons}}</strong>
                </td>
            </tr>

            </tbody>
        </table>

    </div>
    <!-----vista previa imagen------->
    <!----------------------------------------------------MODAL NUEVO PRODUCTO------------------------------------------------------->
    <div class="modal fade" id="modalNuevoI" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #2a2a35">
                    <h5 class="modal-title" style="color: white"><span class="fas fa-plus"></span> Agregar
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: white">&times;</span>
                    </button>
                </div>
                <form id="nuevoP" method="POST" action="{{route("InventarioDiarionuevo")}}" enctype="multipart/form-data">

                    @csrf
                    <div class="modal-body">

                        <div class="form-group">
                            <label for="fecha">Fecha</label>
                            <input class="form-control @error('onzas') is-invalid @enderror" name="fechas" id="fecha" maxlength="100"
                                   value="{{ old('fecha')}}" type="date" required="required">
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
                                    style="width: 100%"  required="required"
                                    class="marca form-control @error('id_marca') is-invalid @enderror" id="marca">
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
                                    style="width: 100%"  required="required"
                                    class="marca form-control @error('id_marca') is-invalid @enderror" id="vitolacapaentrega">
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
                        <label for="onzasNuevoProducto">onzas</label>
                        <input class="form-control @error('onzas') is-invalid @enderror" name="onzas" id="onzasNuevoProducto" maxlength="100"
                               value="{{ old('onzas')}}" required="required">
                        @error('name')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                        <div>

                        <label for="totalinicial">Inventario Inicial </label>
                        <input class="form-control @error('name') is-invalid @enderror" name="totalinicial" id="totalinicial" maxlength="100"
                               value="{{ old('totalinicial')}}" required="required">
                        @error('name')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                        <div>

                            <label for="totalentrada">Entradas </label>
                            <input class="form-control @error('name') is-invalid @enderror" name="totalentrada" id="totalentrada" maxlength="100"
                                   value="{{ old('totalentrada')}}" required="required">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div>

                            <label for="totalfinal">Consumo</label>
                            <input class="form-control @error('name') is-invalid @enderror" name="totalfinal" id="totalfinal" maxlength="100"
                                   value="{{ old('totalfinal')}}" required="required">
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
                    <h5 class="modal-title" style="color: white"><span class="fas fa-plus"></span> Editar Entrega
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: white">&times;</span>
                    </button>
                </div>
                <form id="nuevoP" method="POST" action="{{route("InventarioDiarioeditar")}}" >
                    @method("PUT")

                    @csrf
                    <div class="modal-body">


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
                            <label for="totalinicialdiario">Inventario Inicial</label>
                            <input  class=" form-control @error('name') is-invalid @enderror"
                                    name="totalinicial" id="totalinicialdiario" maxlength="100" value="{{old('totalinicial')}}">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="totalentradadiario">Entradas</label>
                            <input  class="form-control @error('name') is-invalid @enderror"
                                    name="totalentrada" id="totalentradadiario" maxlength="100" value="{{old('totalentrada')}}">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="totalconsumodiario">Consumo</label>
                            <input  class="form-control @error('name') is-invalid @enderror"
                                    name="totalfinal" id="totalconsumodiario" maxlength="100" value="{{old('totalfinal')}}">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="onzascapaentrega">Onzas</label>
                            <input  class="form-control @error('name') is-invalid @enderror"
                                    name="onzas" id="onzascapaentrega" maxlength="100" value="{{old('onzas')}}">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
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
    <div class="modal fade" id="modalBorrarReBulDiario" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <form method="post" action="{{route("InventarioDiarioborrar")}}" >
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
                        <p>¿Estás seguro que deseas borrar  <label
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
                <form id="nuevoP" method="POST" action="{{route("exportarbultoentregadiario")}}" enctype="multipart/form-data">

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
                <form id="nuevoP" method="POST" action="{{route("exportarbultoentregapdfdiario")}}" enctype="multipart/form-data">

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
    <div class="modal fade bd-example-modal-lg" id="modalfechacvs" tabindex="-1" role="dialog"
    aria-labelledby="myLargeModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #2a2a35">
                    <h5 class="modal-title" style="color: white"><span class="fas fa-plus"></span> Diferencias con RMP
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
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Ok</button>
                    </div>
                </form>

            </div>
        </div>
    </div>







    <div class="modal fade" id="recalcular" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #2a2a35">
                    <h5 class="modal-title" style="color: white"><span class="fas fa-plus"></span> Calcular Inventario
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: white">&times;</span>
                    </button>
                </div>
                <form id="nuevoP" method="POST" action="{{route("calcularinventario")}}" enctype="multipart/form-data">

                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="fecha1">¿Está seguro que desea calcular el inventario?</label>
                            <input name="fecha" type="hidden" @isset($fecha)
                            value="{{$fecha}}"

                            @endisset >
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="nuevoP" class="btn btn-success">Calcular</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
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
            url: '/InventarioDiario/diferencias/rmp',
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
