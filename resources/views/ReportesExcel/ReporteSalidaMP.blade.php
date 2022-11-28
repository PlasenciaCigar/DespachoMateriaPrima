<table>
    <thead>
        <tr>
        <th colspan="6" style="text-align:center;">
            <b>Informe de Materia Prima Entregada a Despacho</b>
        </th>
        </tr>
        <tr>
        <th><b>Fecha: {{$fecha}}</b></th>
        <th><b>Planta: TAOSA</b></th>
        </tr>
    <tr style="font-weight: bold;">
        <th><b>Codigo</b></th>
        <th><b>Nombre</b></th>
        <th><b>Libras</b></th>
    </tr>
    </thead>
    <tbody>
    @foreach($dato as $datos)
        <tr>
            <td>{{ $datos->codigo_materia_prima}}</td>
            <td>{{ $datos->Descripcion}}</td>
            <td>{{ $datos->peso}}</td>
        </tr>
    @endforeach
    <tr>
            <td style="text-align:center;" colspan="2">
            <b>Total: </b>
            </td>
            <td><b>{{$total}}</b></td>
        </tr>
    </tbody>
</table>