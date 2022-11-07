<table>
    <thead>
    <tr>
        <th colspan="6" style="text-align:center;"><b>Informe de capa Entregada</b></th>
        </tr>
        <tr>
        <th><b>Fecha: {{$fecha}}</b></th>
        <th><b>Planta: TAOSA</b></th>
        </tr>
    <tr style="font-weight: bold;">
        <th><b>Marca</b></th>
        <th><b>Vitola</b></th>
        <th><b>Semilla</b></th>
        <th><b>Calidad</b></th>
        <th><b>Cantidad</b></th>
        <th><b>Peso</b></th>
    </tr>
    </thead>
    <tbody>
        <?php 
        $acum = $first->marca;
        $cant = 0;
        $peso = 0;
        $else = 0;
        ?>
    @foreach($dato as $datos)
    @if($acum==$datos['Marca'])
        <tr>
            <td>{{ $datos['Marca']}}</td>
            <td>{{ $datos['Vitola']}}</td>
            <td>{{ $datos['Semilla']}}</td>
            <td>{{ $datos['Calidad']}}</td>
            <td>{{ $datos['Cantidad']}}</td>
            <td>{{ $datos['Peso']}}</td>
            <?php
            $cant+=$datos['Cantidad'];
            $peso+=$datos['Peso'];
            ?>
        </tr>
        @else
        <tr>
            <td style="text-align:center;" colspan="4"> <b>Total de: {{$acum}} </b></td>
            <td><b>{{$cant}}</b></td>
            <td><b>{{$peso}}</b></td>
        </tr>
        <tr>
        <td>{{ $datos['Marca']}}</td>
            <td>{{ $datos['Vitola']}}</td>
            <td>{{ $datos['Semilla']}}</td>
            <td>{{ $datos['Calidad']}}</td>
            <td>{{ $datos['Cantidad']}}</td>
            <td>{{ $datos['Peso']}}</td>
            <?php
                $cant=0;$peso=0;
                $cant+=$datos['Cantidad'];$peso+=$datos['Peso'];
            ?>
        </tr>
        @endif
        <?php 
        $acum = $datos['Marca'];
        ?>
    @endforeach
    <tr>
            <td style="text-align:center;" colspan="4"><b>Total de: {{$acum}}</b></td>
            <td><b>{{$cant}}</b></td>
            <td><b>{{$peso}}</b></td>
        </tr>
    </tbody>
</table>