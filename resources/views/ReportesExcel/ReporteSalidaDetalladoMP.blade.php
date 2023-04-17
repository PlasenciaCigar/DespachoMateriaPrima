<table>
    <thead>
        <tr>
        <th colspan="6" style="text-align:center;">
            <b>Informe de Materia Prima Entregada por Bulto</b>
        </th>
        </tr>
        <tr>
        <th><b>Fecha: {{$fecha}}</b></th>
        <th><b>Planta: TAOSA</b></th>
        </tr>
    <tr style="font-weight: bold;">
        <th><b>Marca</b></th>
        <th><b>Vitola</b></th>
        <th><b>Producto</b></th>
        <th><b>Onzas</b></th>
    </tr>
    </thead>
    <tbody>
    @foreach($dato as $datos)
        <tr>
            <td>{{ $datos->marca}}</td>
            <td>{{ $datos->vitola}}</td>
            <td></td>  
            <td></td>          
        </tr>
        @php
                $mp = DB::select('select Descripcion, peso from detalle_combinaciones 
                inner join materia_primas on Codigo = codigo_materia_prima where id_combinaciones = ?',
                [$datos->combinacion]);
        @endphp

                @foreach($mp as $m)
                <tr>
                <td colspan="2"></td>
                <td>{{$m->Descripcion}}</td>
                <td>{{$m->peso}}</td>
                </tr>
                @endforeach
    @endforeach

    </tbody>
</table>