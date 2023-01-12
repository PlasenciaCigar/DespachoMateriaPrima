<table>
    <thead>
        <tr>
            <th colspan="6" style="text-align:center;"><b>Informe de Banda Entregada</b></th>
        </tr>
        <tr>
        <th><b>Fecha: {{$fecha}}</b></th>
        <th><b>Planta: TAOSA</b></th>
        </tr>
    <tr style="font-weight: bold;">
        <th><b>Marca</b></th>
        <th><b>Vitola</b></th>
        <th><b>Semilla</b></th>
        <th><b>Variedad</b></th>
        <th><b>Tamaño</b></th>
        <th><b>Procedencia</b></th>
        <th><b>Cantidad</b></th>
        <th><b>Libras</b></th>
    </tr>
    </thead>
    <tbody>
        <?php 
        $acum = $first->nombre_semillas;
        $cant = 0;
        $peso = 0;
        $else = 0;
        ?>
    @foreach($consumobanda as $datos)
    @if($acum==$datos->nombre_semillas)
        <tr>
            <td>{{ $datos->nombre_marca}}</td>
            <td>{{ $datos->nombre_vitolas}}</td>
            <td>{{ $datos->nombre_semillas}}</td>
            <td>{{ $datos->nombre_variedad}}</td>
            <td>{{ $datos->nombre_tamano}}</td>
            <td>{{ $datos->nombre_procedencia}}</td>
            <td>{{ $datos->total}}</td>
            <td>{{ $datos->libras}}</td>
            <?php
            $cant+=$datos->total;
            $peso+=$datos->libras;
            ?>
        </tr>
        @else
        <tr>
            <td style="text-align:center;" colspan="6">
            <b>Total de {{$acum}}</b></td>
            <td><b>{{$cant}}</b></td>
            <td><b>{{$peso}}</b></td>
        </tr>
        <tr>
        <td>{{ $datos->nombre_marca }}</td>
            <td>{{ $datos->nombre_vitolas }}</td>
            <td>{{ $datos->nombre_semillas }}</td>
            <td>{{ $datos->nombre_variedad }}</td>
            <td>{{ $datos->nombre_tamano }}</td>
            <td>{{ $datos->nombre_procedencia }}</td>
            <td>{{ $datos->total}}</td>
            <td>{{ $datos->libras}}</td>
            <?php
                $cant=0;$peso=0;
                $cant+=$datos->total;$peso+=$datos->libras;
            ?>
        </tr>
        @endif
        <?php 
        $acum = $datos->nombre_semillas;
        ?>
    @endforeach
    <tr>
            <td style="text-align:center;" colspan="6"><b>Total de {{$acum}}</b></td>
            <td><b>{{$cant}}</b></td>
            <td><b>{{$peso}}</b></td>
    </tr>
    </tbody>
</table>