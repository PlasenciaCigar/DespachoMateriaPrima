<table>
    <thead>
        <tr>
            <th colspan="6" style="text-align:center;"><b>Informe de Materia Prima Entregada a Salon</b></th>
        </tr>
        <tr>
        <th><b>Fecha: {{$fecha}}</b></th>
        <th><b>Planta: TAOSA</b></th>
        </tr>
    <tr style="font-weight: bold;">
        <th><b>Marca</b></th>
        <th><b>Vitola</b></th>
        <th><b>Codigo</b></th>
        <th><b>Materia Prima</b></th>
        <th><b># Bultos</b></th>
        <th><b>Peso</b></th>
    </tr>
    </thead>
    <tbody>
        <?php 
        $acum = '$first->marcas';
        $acum2 = '$first->name';
        $cant = 0;
        $peso = 0;
        $else = 1;
        ?>
    @foreach($dato as $datos)
    @if($acum==$datos->marcas && $acum2==$datos->name)
        <tr>
            <td style="text-align:center;" colspan="2">...</td>
            <td>{{ $datos->Codigo }}</td>
            <td>{{ $datos->Descripcion }}</td>
            <td>{{ $datos->bultos }}</td>
            <td>{{ $datos->peso }}</td>
        </tr>
        @else
        <tr>
            <td style="text-align:center;" colspan="6"> <b></b></td>
        </tr>
        <tr>
            <td><b>{{ $datos->marcas }}</b></td>
            <td><b>{{ $datos->name }}</b></td>
            <td>{{ $datos->Codigo }}</td>
            <td>{{ $datos->Descripcion }}</td>
            <td>{{ $datos->bultos }}</td>
            <td>{{ $datos->peso }}</td>
        </tr>
        @endif
        <?php 
        $acum = $datos->marcas;
        $acum2 = $datos->name;
        ?>
    @endforeach
    <tr>
    <td style="text-align:center;" colspan="5">
    <b>Total Materia Prima</b>
    </td>
    <td><b>{{collect($dato)->sum('peso')}}</b></td>
    </tr>
    </tbody>
</table>