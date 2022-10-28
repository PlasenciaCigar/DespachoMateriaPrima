<!-----------------------------MODAL EDITAR PRODUCTO------------------------------->
<div class="modal fade" id="modalEditarCapaEntrega" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background: #2a2a35">
                    <h5 class="modal-title" style="color: white"><span class="fas fa-plus"></span> Editar Materia Prima
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true" style="color: white">&times;</span>
                    </button>
                </div>
                <form id="nuevoP" method="POST" action="{{route("ligasupdate")}}">
                    @method("PUT")

                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="codigo1">Codigo</label>
                            <input  class="form-control @error('name') is-invalid @enderror"
                                    name="codigo" id="codigo1" maxlength="100"
                                    required="required" type="text" value="" readonly>
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>


                            </span>
                            @enderror
                        </div>


                        <div class="form-group">
                            <label for="descripcion1">Nombre</label>
                            <input class=" form-control @error('name') is-invalid @enderror" name="descripcion" id="descripcion1" maxlength="100"
                                   value="" type="text" required="required">
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="libras1">Libras</label>
                            <input class=" form-control @error('name') is-invalid @enderror" name="libras" id="libras1" maxlength="100"
                                   value="" type="text" required="required" readonly>
                            @error('name')
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                            @enderror
                        </div>


                    </div>
                    <div class="modal-footer">
                        <input id="id_producto" name="id" type="hidden">
                        <button type="submit" class="btn btn-success" id="id_producto" onclick="f()">Editar</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                    </div>
                </form>

            </div>
        </div>
    </div>