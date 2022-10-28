@extends("layouts.MenuRMP")
@section("content")
    <div class="container-fluid">
        <h1 class="mt-4">INVENTARIO MATERIA PRIMA
            <div class="btn-group" role="group">

                <button class="btn btn-sm btn-success"
                        id="botonAbrirModalNuevoConsumo"
                        data-toggle="modal" data-target="#modalNuevoConsumo">
                    <span class="fas fa-plus"></span> Nueva M.P
                </button>
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
                <li class="breadcrumb-item active" aria-current="page">Inventario de Materia Prima.</li>

                <form  class="d-none d-md-inline-block form-inline
                           ml-auto mr-0 mr-md-2 my-0 my-md-0 mb-md-2">
                           <div class="row">

                    <div class="input-group" style="width: 300px">
                        <input class="form-control" name="descripcion" type="search" placeholder="Buscar por nombre"
                               aria-label="Search">
                        <div class="input-group-append">
                            <a id="borrarBusqueda" class="btn btn-danger hideClearSearch" style="color: white"
                               href="{{route("ligas")}}">&times;</a>
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </div>

                    <div class="input-group" style="width: 300px">
                        <input class="form-control" name="fecha" type="date" placeholder="fecha"
                               aria-label="Search" @isset($fecha)
                               value="{{$fecha}}"

                               @endisset>
                        <div class="input-group-append">
                            <a id="borrarBusqueda" class="btn btn-danger hideClearSearch" style="color: white"
                               href="{{route("ligas")}}">&times;</a>
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                    </div>
                </form>
            </ol>

            <div class="pagination pagination-sm">

            <form id="" method="POST" action="{{route("ligasexport")}}" enctype="multipart/form-data">
                    @csrf 
                    <button type="submit" class="btn btn-success hideClearSearch" style="color: white">
                        Exportar
                    </button>
                </form>
                @isset($total)
                    <label  class="d-none d-md-inline-block form-inline
                           ml-auto mr-0 mr-md-2 my-0 my-md-0 mb-md-2"
                            style="align-content: center">Total Inventario: {{$total}}</label>
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
                <th>Codigo</th>
                <th>Producto</th>
                <th>Fecha de registro</th>
                <th>Existencia</th>
                <th><span class="fas fa-info-circle"></span></th>
            </tr>
            </thead>
            <tbody>
            @if($materiaprima->count()<= 0)
                <tr>
                    <td colspan="4" style="align-items: center">No hay productos</td>
                </tr>
            @endif
            @foreach($materiaprima as $productos)
                <tr>
                    <td>{{$noPagina++}}</td>
                    <td>{{$productos->Codigo}}</td>
                    <td>{{$productos->Descripcion}}</td>
                    <td>{{$productos->created_at}}</td>
                    <td>{{$productos->Libras}}</td>
                    <td>
                        <a href="{{url('/rmp/kardex', ['codigo'=>$productos->Codigo])}}" type="button">
                        <span class="fas fa-eye"></span>
                        </a>

                        <button class="btn btn-sm btn-success modal-open"
                        onclick="enviar('{{$codigo = $productos->Codigo}}', '{{$descripcion = $productos->Descripcion}}'
                        , '{{$libras = $productos->Libras}}')"
                                id="editarCapaEntrega"
                                data-toggle="modal"
                                data-target="#modalEditarCapaEntrega"
                                title="Editar">
                            <span class="fas fa-pencil-alt"></span>
                        </button>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>
    <script type="text/javascript">
        function enviar(codigo, descripcion, libras){

            $('#codigo1').val(codigo)
            $('#descripcion1').val(descripcion)
            $('#libras1').val(libras)
        }
</script>
    @include("rmp.modalmateriaprima")

<!----------------------------------------------------MODAL NUEVO PRODUCTO------------------------------------------------------->
    <div class="modal fade" id="modalNuevoConsumo" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #2a2a35">
                    <h5 class="modal-title" style="color: white"><span class="fas fa-plus"></span> Agregar Materia Prima
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: white">&times;</span>
                    </button>
                </div>
                <form id="nuevoP" method="POST" action="{{route("ligastore")}}" enctype="multipart/form-data">

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
                            <label for="nombreNuevoProducto">Codigo</label>
                            <input class=" form-control @error('name') is-invalid @enderror" name="codigo" id="codigo" maxlength="100"
                                   value="{{ old('total')}}" required="required" type="text">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="pesoNuevoProducto">Nombre</label>
                            <input class=" form-control @error('name') is-invalid @enderror" name="descripcion" id="descripcion" maxlength="100"
                                   value="{{ old('peso')}}" type="text" required="required">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="pesoNuevoProducto">Existencia</label>
                            <input class=" form-control @error('name') is-invalid @enderror" name="libras" id="libras" maxlength="100"
                                   value="{{ old('peso')}}" type="number" required="required">
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

    <!------------------MODAL BORRAR PRODUCTO---------------------------->
    <div class="modal fade" id="modalBorrarCapaEntrega" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <form method="post" action="{{route("EntradaBultosborrar")}}" >
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
                        <p>¿Estás seguro que deseas borrar la entrada De Bulto <label
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
