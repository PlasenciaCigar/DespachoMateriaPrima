@extends("layouts.MenuRMP")
@section("content")
    <div class="container-fluid">
        <h1 class="mt-4">Combinaciones Segun Bultos
            <div class="btn-group" role="group">
            <!-- <a class="btn btn-sm btn-success" href="{{Route('combinacionuevo')}}"
                        id="botonAbrirModalNuevoConsumo">
                    <span class="fas fa-plus"></span> Nueva
                </a> -->

               <button class="btn btn-sm btn-success"
                        id="botonAbrirModalNuevoConsumo"
                        data-toggle="modal" data-target="#modalNuevoConsumo">
                    <span class="fas fa-plus"></span> Nueva
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
                <li class="breadcrumb-item active" aria-current="page">Combinaciones segun Bultos</li>
            </ol>

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
                <th><span class="fas fa-info-circle"></span></th>
            </tr>
            </thead>
            <tbody>
            @foreach($combinaciones as $combinacion)
                <tr>
                    <td rowspan="">{{$combinacion->id}}</td>
                    <td rowspan="">{{$combinacion->marca}}</td>
                    <td rowspan="">{{$combinacion->name}}</td>
                    <td> 
                    <button onclick="ver('{{$combinacion->id}}', '{{$combinacion->id_marca}}','{{$combinacion->id_vitolas}}')" class="btn btn-sm btn-info">
                    <span class="fas fa-eye"></span>
                    </button>
                        <button onclick="deletecombinacion('{{$combinacion->id}}')" class='delete-modal btn btn-danger'>
                    <span class='fas fa-trash'></span>
                    </button>                
                </td>
                </tr>
                @endforeach
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
            $('#codigo1').val(tripa);
            $('#codigo1').trigger('change');
        }
    </script>
    <!-----vista previa imagen------->
<!----------------------------------------------------MODAL NUEVO PRODUCTO------------------------------------------------------->
    <div data-backdrop="static" data-keyboard="false" class="modal fade" id="modalNuevoConsumo" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #2a2a35">
                    <h5 class="modal-title" style="color: white"><span class="fas fa-plus"></span> Agregar Combinacion
                    </h5>
                    <button type="button" onclick="cerrar()" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: white">&times;</span>
                    </button>
                </div>
                <form id="nuevoP" method="post" action="" enctype="multipart/form-data">

                    @csrf
                    <div class="modal-body">

                        <div class="form-group">
                            <label for="procedencia">Marca</label>
                            <br>
                            <select name="marca"
                                    style="width: 100%" required="required"
                                    class="marca form-control @error('id_vitolas') is-invalid @enderror" id="marca">
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
                            <input class="col-md-7 form-control @error('name') is-invalid @enderror" name="libras" id="codigo1" maxlength="50"
                                   value="{{ old('total')}}" required="required" type="text" readonly>
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        <div class="form-group col-md-50">
                            <label for="nombreNuevoProducto">Peso</label>
                            <input class="col-md-5 form-control @error('name') is-invalid @enderror" name="libras" id="peso" maxlength="50"
                                   value="{{ old('total')}}" required="required" type="number">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>
                        <input type="hidden" id="combinacion">

                        <div class="form-group col-md-50">
                            <br>
                            <button onclick="guardar(event)" id="add" class="btn btn-success">Add</button>
                        </div>

            <table id="table" class="table table-striped">

            <tr class="table-primary">
                <thead class="thead-dark">
                <th>Codigo</th>
                <th>Peso</th>
                <th>Remove</th>
                </thead>
            </tr>
            
        </table>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="cerrar()" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <script>

        function ignorancia(cod){ 
            let _token= "{{ csrf_token() }}";
            $.ajax({
            type: 'post',
            url: '/detalledelete/'+cod,
            data: {
                _token: _token,
                codigo: cod
            },
            success: function(data) {
                alert('xd');
                $('.item'+cod).remove(); 
            }
        });          
        }


        function detalle(marca, vitola,id){
            $('#marca').val(marca);
            $('#marca').trigger('change');
            $('#vitola').val(vitola);
            $('#vitola').trigger('change');
            $('#combinacion').val(id);
        }

function ver(id_combinaciones, marca, vitola){
    let id_combinacion = id_combinaciones;
    let _token= "{{ csrf_token() }}";
    $.ajax({
    type: "POST",
    url: "/detallecombinacionver/"+id_combinaciones,
    data: {
        id_combinacion: id_combinacion,
      _token: _token},
      success: function(response) {
            if ((response.errors)) {
                alert('Este bulto no esta registrado')
            } else {
                $('#modalNuevoConsumo').modal();
                agregartabledetalle(response);
                detalle(marca, vitola, id_combinaciones);
                desactivar();             
            }
        },
    });
        }

    function guardar(evt){
    evt.preventDefault();
  let marca = $("#marca").val();
  let vitola = $("#vitola").val();
  let peso = $("#peso").val();
  let codigo_materia_prima= $("#codigo").val();
  let _token= "{{ csrf_token() }}";
  let id_impor = $("#combinacion").val();
  if(id_impor==""){
    $.ajax({
    type: "POST",
    url: "/rmp/combinaciones/store",
    data: {
    marca: marca,
      vitola: vitola,
      codigo_materia_prima: codigo_materia_prima,
      peso: peso,
      _token: _token},
      success: function(data) {
            if ((data.errors)) {
                alert('Este bulto no esta registrado')
            } else {
               $("#combinacion").val(data.id);
               agregartable(codigo_materia_prima, peso);
               desactivar();
            }
        },
    });



  }else{
    $.ajax({
    url: '/detallecombinacion',
    type: "POST",
    data: {id_combinaciones: id_impor,
      codigo_materia_prima: codigo_materia_prima,
      peso: peso,
      _token: _token},
      success: function(data) {
            if ((data.errors)) {
                alert('No existe ese bulto')
            } else {
               alert('Materia Prima agregada con exito');
               $("#combinacion").val(data);
               agregartable(codigo_materia_prima, peso);
            }
        },
  })
  }
}

    function deletedetalle(codigo){
        alert('codigo');
        $.ajax({
    url: '/detalledelete/',
    type: "DELETE",
    data: {id_combinaciones: id_impor,
      codigo_materia_prima: codigo_materia_prima,
      peso: peso,
      _token: _token},
      success: function(data) {
            if ((data.errors)) {
                alert('No existe ese bulto')
            } else {
               alert('Codigo borrado con exito');
            }
        },
  })
    }

    function deletecombinacion(id){
        let _token= "{{ csrf_token() }}";
        $.ajax({
    url: '/detallecombinacion/'+id,
    type: "POST",
    data: {id_combinaciones: id,
      _token: _token},
      success: function(data) {
            if ((data.errors)) {
                alert('No existe ese bulto')
            } else {
               alert('Codigo borrado con exito');
            }
        },
  })
    }

        function agregartabledetalle(response){
            for (let i = 0; i < response.length; i++) {
                let msj = response[i].codigo_materia_prima.split('-');
                let codigoMP = parseInt(msj[1].toString(8), 10);
            $('#table').append("<tr class='item" + codigoMP
            + "'> <td>" + response[i].codigo_materia_prima + "</td><td>" 
            + response[i].peso + "</td><td> <button id='xd' class='delete-modal btn btn-danger' data-id='" + 
            response[i].peso + "' data-name='" + response[i].peso 
             + "' onclick='ignorancia("+codigoMP+")' ><span class='fas fa-trash'></span></button></td></tr>"); 
            }
        }

        function agregartable(codigo_materia_prima, peso){
            $('#table').append("<tr class='item" + codigo_materia_prima + "'><td>" + codigo_materia_prima + "</td><td>" 
            + peso + "</td><td> <button id='xd' class='delete-modal btn btn-danger' data-id='" + 
             peso + "' data-name='" + peso 
             + "'><span class='fas fa-trash'></span></button></td></tr>");
        }

        function desactivar(){
            let marca = $("#marca").attr('disabled', true);
            let vitola = $("#vitola").attr('disabled', true);
        }
        
        function cerrar(){
            location.reload();
        }

        document.addEventListener("keydown", function (event) {
    if (event.keyCode === 27) {
        location.reload();
    }
});
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
