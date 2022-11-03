<table>
    <thead>
    <tr style="font-weight: bold;">
        <th>Marca</th>
        <th>Vitola</th>
        <th>Semilla</th>
        <th>Calidad</th>
        <th>Cantidad</th>
        <th>Peso</th>
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
            <td style="text-align:center;" colspan="4">Total de: {{$acum}}</td>
            <td>{{$cant}}</td>
            <td>{{$peso}}</td>
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
            <td style="text-align:center;" colspan="4">Total de: {{$acum}}</td>
            <td>{{$cant}}</td>
            <td>{{$peso}}</td>
        </tr>
    </tbody>
</table>