@extends("layouts.MenuBanda")
@section("content")
    <div class="container-fluid ">
        <h1 class="mt-4">Inventario Diario Banda
            <div class="btn-group" role="group">

                <button hidden class="btn btn-sm btn-success"
                        id="botonAbrirModalNuevoI"
                        data-toggle="modal" data-target="#modalNuevoI">
                    <span class="fas fa-plus"></span> Nueva
                </button>
            </div>


        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item" aria-current="page" ><a href="/">Inicio</a></li>
                <li class="breadcrumb-item active" aria-current="page">Inventario Diario Banda</li>

                <form  class="d-none d-md-inline-block form-inline
                           ml-auto mr-0 mr-md-2 my-0 my-md-0 mb-md-2">
                    <div class="input-group" style="width: 300px">
                        <input class="form-control" name="fecha" type="date" placeholder="fecha"
                               aria-label="Search" @isset($fecha)
                               value="{{$fecha}}"

                               @endisset
                               max=<?php $hoy=date("Y-m-d"); $o=date("Y-m-d",strtotime($hoy."+2 days")); echo $hoy?>
                               >

                        <div class="input-group-append">
                            <a id="borrarBusqueda" class="btn btn-danger hideClearSearch" style="color: white"
                               href="{{route("InventarioBanda")}}">&times;</a>
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </ol>
            <?php $hoy=date("Y-m-d"); $o=date("Y-m-d",strtotime($hoy."-1 days"));?>
            <div>
                @if ($fecha == $hoy || @Auth::user()->is_admin==1)
                <button class="btn btn-danger"
                title="Borrar"
                data-toggle="modal"
                data-target="#deleteall"
                data-id="{{$fecha}}">
                ACTUALIZAR INVENTARIO</button>
                @endif
        </div>


        <div class="modal fade" id="deleteall" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <form method="post" action="{{route("InventarioBandaborrarall")}}" >
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
                                    <p>-Al actualizar el inventario se borrara el peso de entrada</p>
                                    <p>-Al actualizar el inventario se borrara el peso de final</p>

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

            <div class="pagination pagination-sm">

                <a class="btn btn-dark hideClearSearch" style="color: white"
                   id="botonAbrirModalNuevoRecepcionCapa"
                   data-toggle="modal" data-target="#modalfechacvs">CVS</a>

                <a class="btn btn-success hideClearSearch" style="color: white"
                   id="botonAbrirModalNuevoRecepcionCapa"
                   data-toggle="modal" data-target="#modalfecha">Excel</a>


                <form  class="d-none d-md-inline-block form-inline
                           ml-auto mr-0 mr-md-2 my-0 my-md-0 mb-md-2">
                    <div class="input-group" style="width: 300px">
                        <input class="form-control" name="search" type="search" placeholder="Search"
                               aria-label="Search">
                        <div class="input-group-append">
                            <a id="borrarBusqueda" class="btn btn-danger hideClearSearch" style="color: white"
                               href="{{route("InventarioBanda")}}">&times;</a>
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </form>
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


        <table class="table main-table"  style="width:100%;border-collapse:collapse;">
            <thead class="thead-dark">
            <tr class="GridviewScrollHeader">
                <th>#</th>
                <th>Semilla</th>
                <th>Tamaño</th>
                <th>variedad</th>
                <th>Procedencia</th>
                <th>Inicial</th>
                <th>Libras</th>
                <th>Entradas</th>
                <th>Libras</th>
                <th>Final</th>
                <th>Libras</th>
                <th>Consumo</th>
                <th>Libras</th>


                <th><span class="fas fa-info-circle" style="width: 7em;"></span></th>
            </tr>
            </thead>
            <tbody>
            @if(!$existenciaDiaria)
                <tr>
                    <td colspan="4" style="align-items: center">No hay productos</td>
                </tr>
            @endif
            @foreach($existenciaDiaria as $productos)
                <tr class="GridviewScrollItem">
                    <td>{{$noPagina++}}</td>


                    <td>{{$productos->nombre_semillas}}</td>
                    <td>{{$productos->nombre_tamano}}</td>
                    <td>{{$productos->nombre_variedad}}</td>
                    <td>{{$productos->nombre_procedencia}}</td>
                    <td>{{$productos->totalinicial}}</td>
                    <td>{{$productos->pesoinicial}}</td>
                    <td>{{$productos->totalentrada}}</td>
                    <td>{{$productos->pesoentrada}}</td>
                    <td>{{$productos->totalfinal}}</td>
                    <td>{{$productos->pesofinal}}</td>
                    <td>{{$productos->totalconsumo}}</td>
                    <td>{{$productos->pesoconsumo}}</td>
                    <td>



                        <button class="btn btn-sm btn-info"
                                title="Ver"
                                data-toggle="modal"
                                data-target="#modalVerCapaEntrega"
                                data-semilla="{{$productos->nombre_semillas}}"
                                data-id_tamano="{{$productos->nombre_tamano}}"
                                data-inicial="{{$productos->totalinicial}}"
                                data-pesoinicial="{{round(($productos->pesoinicial),2)}}"
                                data-totalentrada="{{$productos->totalentrada}}"
                                data-pesoentrada="{{round(($productos->pesoentrada),2)}}"
                                data-totalfinal="{{$productos->totalfinal}}"
                                data-pesofinal="{{round(($productos->pesofinal),2)}}"
                                data-totalconsumo="{{$productos->totalconsumo}}"
                                data-pesoconsumo="{{round(($productos->pesoconsumo),2) }}"
                                data-id_variedad="{{$productos->id_variedad}}"
                                data-id_procedencia="{{$productos->id_procedencia}}"

                               >
                            <span class="fas fa-eye"></span>
                        </button>
                        <button class="btn btn-sm btn-success"
                                id="editarCapaEntrega{{$productos->id}}"
                                data-toggle="modal"
                                data-target="#modalEditarCapaEntrega"
                                data-id="{{$productos->id}}"
                                data-id_semilla="{{$productos->id_semillas}}"
                                data-id_tamano="{{$productos->id_tamano}}"
                                data-totalinicial="{{$productos->totalinicial}}"
                                data-totalentrada="{{$productos->totalentrada}}"
                                data-totalfinal="{{$productos->totalfinal}}"
                                data-pesoconsumo="{{round(($productos->pesofinal), 2)}}"
                                data-totalconsumo="{{$productos->totalfinal}}"
                                data-pesoinicial="{{round(($productos->pesoinicial),2)}}"
                                data-pesoentrada="{{round(($productos->pesoentrada),2)}}"
                                data-pesofinal="{{round(($productos->pesofinal),2)}}"
                                data-id_variedad="{{$productos->id_variedad}}"
                                data-id_procedencia="{{$productos->id_procedencia}}"

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
                <td colspan="5" style="text-align:center;">
                    <strong>
                        TOTAL
                    </strong></td>

                    <?php
                    $totalI = 0;

                foreach($existenciaDiaria as $productos){
                    $totalI+=$productos->totalinicial;
                }
                ?>
                <td>
                    <strong>{{$totalI}}</strong>
                </td>

                <?php
                    $pesoI = 0;

                foreach($existenciaDiaria as $productos){
                    $pesoI+=$productos->pesoinicial;
                }
                ?>
                <td>
                    <strong>{{$pesoI}}</strong>
                </td>

                <?php
                    $totalent = 0;

                foreach($existenciaDiaria as $productos){
                    $totalent+=$productos->totalentrada;
                }
                ?>
                <td>
                    <strong>{{round($totalent, 2)}}</strong>
                </td>


                <?php
                    $pesoentr = 0;

                foreach($existenciaDiaria as $productos){
                    $pesoentr+=$productos->pesoentrada;
                }
                ?>
                <td>
                    <strong>{{$pesoentr}}</strong>
                </td>

                <?php
                    $totalfina = 0;

                foreach($existenciaDiaria as $productos){
                    $totalfina+=$productos->totalfinal;
                }
                ?>
                <td>
                    <strong>{{$totalfina}}</strong>
                </td>

                <?php
                    $pesofina = 0;

                foreach($existenciaDiaria as $productos){
                    $pesofina+=$productos->pesofinal;
                }
                ?>
                <td>
                    <strong>{{$pesofina}}</strong>
                </td>

                <?php
                    $totalcons = 0;

                foreach($existenciaDiaria as $productos){
                    $totalcons+=$productos->totalconsumo;
                }
                ?>
                <td>
                    <strong>{{$totalcons}}</strong>
                </td>

                <?php
                    $pesocons = 0;

                foreach($existenciaDiaria as $productos){
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
                <form id="nuevoP" method="POST" action="{{route("InventarioBandanuevo")}}" enctype="multipart/form-data">

                    @csrf

                    <div class="modal-body">
                        <div class="form-group">
                            <label for="fecha">Fecha</label>
                            <input class="form-control @error('onzas') is-invalid @enderror" name="fecha" id="fecha" maxlength="100"
                                   value="{{ old('fecha')}}" type="date" required="required">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="id_semillas">Seleccione la Semilla</label>
                            <br>
                            <select name="id_semillas"
                                    style="width: 100%" required="required"
                                    class="marca form-control @error('id_marca') is-invalid @enderror" id="id_semillas">
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
                            <label for="variedadcapaentrega">Variedad</label>
                            <br>
                            <select name="id_variedad"
                                    style="width: 100%"  required="required"
                                    class="marca form-control @error('id_marca')
                                        is-invalid @enderror" id="variedadcapaentrega">
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
                            <label for="procedenciacapaentrega">Procedencia
                            </label>
                            <br>
                            <select name="id_procedencia"
                                    style="width: 100%"  required="required"
                                    class="marca form-control @error('id_marca') is-invalid @enderror" id="procedenciacapaentrega">
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

                        <div class="form-group">
                            <label for="id_tamano">Seleccione el Tamaño</label>
                            <br>
                            <select name="id_tamano"
                                    style="width: 100%"  required="required"
                                    class="marca form-control @error('id_marca') is-invalid @enderror" id="id_tamano">
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

                        <label for="totalinicial">Inventario Inicial </label>
                        <input class="form-control @error('name') is-invalid @enderror" name="totalinicial" id="totalinicial" maxlength="100"
                               value="{{ old('totalinicial')}}" required="required" disabled>
                        @error('name')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                        </div>
                        <div class="form-group">
                            <label for="pesoinicialdiario">Libras  Inicial </label>
                            <input class="form-control @error('name')
                                is-invalid @enderror" name="pesoinicial" id="pesoinicialdiario" maxlength="100"
                                   value="{{ old('pesoinicial')}}" required="required">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>


                        <div class="form-group">


                            <label for="totalentrada">Entradas </label>
                            <input class="form-control @error('name') is-invalid @enderror" name="totalentrada" id="totalentrada" maxlength="100"
                                   value="{{ old('totalentrada')}}" required="required">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="form-group">

                            <label for="pesoentradadiario">Libras Entrada </label>
                            <input class="form-control @error('name') is-invalid @enderror" name="pesoentrada" id="pesoentradadiario" maxlength="100"
                                   value="{{ old('pesoentrada')}}" required="required">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>


                        <div class="form-group">

                            <label for="totalfinal">Consumo</label>
                            <input class="form-control @error('name') is-invalid @enderror" name="totalfinal" id="totalfinal" maxlength="100"
                                   value="{{ old('totalfinal')}}" required="required">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <div class="form-group">

                            <label for="pesofinaldiario">Libras Consumo </label>
                            <input class="form-control @error('name') is-invalid @enderror" name="pesofinal" id="pesofinaldiario" maxlength="100"
                                   value="{{ old('pesofinal')}}" required="required">
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
                <form id="nuevoP" method="POST" action="{{route("InventarioBandaeditar")}}" >
                    @method("PUT")

                    @csrf
                    <div class="modal-body">


                        <div class="form-group">
                            <label for="semillacapaentrega">Seleccione la Semilla</label>
                            <br>
                            <select name="id_semillas"
                                    required
                                    style="width: 100%"
                                    class="empresa form-control @error('id_empresa') is-invalid @enderror"
                                    id="semillacapaentrega" required="required">
                                <option disabled selected value="">Seleccione</option>
                                @foreach($semilla as $semillas)
                                    <option value="{{$semillas->id}}">{{$semillas->name}}
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
                            <label for="variedadcapaentrega">variedad
                            </label>
                            <br>
                            <select name="id_variedad"
                                    required="required"
                                    style="width: 100%"
                                    class="empresa form-control
                            @error('disponible') is-invalid @enderror" id="variedadcapaentrega">
                                <option disabled selected value="">Seleccione</option>
                                @foreach($variedad as $variedades)
                                    <option value="{{$variedades->id}}">{{$variedades->name}}
                                    </option>
                                @endforeach
                            </select>
                            <!---- Boton para crear un nuevo tipo de categoria- -->
                        </div>
                        <div class="form-group">
                            <label for="procedenciacapaentrega">procedencia
                            </label>
                            <br>
                            <select name="id_procedencia"
                                    required="required"
                                    style="width: 100%"
                                    class="empresa form-control
                                    @error('disponible') is-invalid @enderror" id="procedenciacapaentrega">
                                <option disabled selected value="">Seleccione</option>
                                @foreach($procedencia as $procedencias)
                                    <option value="{{$procedencias->id}}">{{$procedencias->name}}
                                    </option>
                                @endforeach  </select>
                            <!---- Boton para crear un nuevo tipo de categoria- -->
                        </div>



                        <div class="form-group">
                            <label for="tamanocapaentrega">Seleccione el tamaño </label>
                            <br>
                            <select name="id_tamano"
                                    style="width: 100%"
                                    class="empresa form-control @error('id_marca') is-invalid @enderror"
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
                        <div class="form-row">

                            <label for="totalinicialdiario">Inventario Inicial</label>
                            <input  class="form-control @error('name') is-invalid @enderror"
                                    name="totalinicial" id="totalinicialdiario" maxlength="100" value="{{old('totalinicial')}}">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        <div class="form-row">

                            <label for="pesoinicialdiario">Libra Inicial</label>
                            <input  class="form-control @error('name') is-invalid @enderror"
                                    name="pesoinicial" id="pesoinicialdiario" maxlength="100" value="{{old('pesoinicial')}}">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror

                        </div>

                        <div class="form-row">
                            <label for="totalentradadiario">Entradas</label>
                            <input  class="form-control @error('name') is-invalid @enderror"
                                    name="totalentrada" id="totalentradadiario" maxlength="100" value="{{old('totalentradaE')}}">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror



                        </div>

                        <div class="form-row">

                            <label for="pesoentradadiario">Libra Entrada</label>
                            <input  class="form-control @error('name') is-invalid @enderror"
                                    name="pesoentrada" id="pesoentradadiario" maxlength="100" value="{{old('pesoentrada')}}">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror

                        </div>
                        <div class="form-row">

                            <label for="totalconsumodiario">Inventario Final</label>
                            <input  class="form-control @error('name') is-invalid @enderror"
                                    name="totalfinal" id="totalconsumodiario" maxlength="100" value="{{old('totalfinal')}}">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                    </div>

                        <div class="form-row">

                            <label for="pesoconsumodiario">Libra Final</label>
                            <input  class="form-control @error('name') is-invalid @enderror"
                                    name="pesofinal" id="pesoconsumodiario" maxlength="100" value="{{old('pesofinal')}}">
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
                <form method="post" action="{{route("InventarioBandaborrar")}}" >
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
                        <p>¿Estás seguro que deseas borrar esta entrads <label
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
                <form id="nuevoP" method="POST" action="{{route("exportarInventarioBanda")}}" enctype="multipart/form-data">

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
                <form id="nuevoP" method="POST" action="{{route("exportarInventarioBandapdf")}}" enctype="multipart/form-data">

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
                <form id="nuevoP" method="POST" action="{{route("exportarInventarioBandacvs")}}" enctype="multipart/form-data">

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
