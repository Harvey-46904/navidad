<h2 style="text-align:center; margin-bottom:25px">
    Carta de regalos que debes comprar
</h2>

@foreach ($darregalos as $item)
<table width="100%" style="margin-bottom:25px; border:1px solid #999">
    <tr>
        <!-- Imagen del regalo -->
        <td width="40%" style="text-align:center; padding:15px">
            @if(!empty($item->regalo))
            <img src="{{ public_path('storage/'.$item->regalo) }}" style="width:240px; height:auto;">
            @endif
        </td>

        <!-- Información -->
        <td width="60%" style="padding:15px; vertical-align:top">
            <p style="font-size:14px"><strong>Para:</strong> {{ $item->name }}</p>
            <p style="font-size:14px"><strong>Regalo:</strong> {{ $item->nombre }}</p>
            <p style="font-size:13px"><strong>Detalle:</strong> {{ $item->descripcion }}</p>
            <p style="font-size:13px"><strong>Dónde comprar:</strong> {{ $item->donde }}</p>

            <!-- Imagen del lugar -->
            @if(!empty($item->lugar))
            <div style="margin-top:20px; text-align:center">
                <p style="font-size:13px; margin-bottom:8px"><strong>Lugar:</strong></p>
                <img src="{{ public_path('storage/'.$item->lugar) }}" style="width:300px; height:auto;">
            </div>
            @endif

        </td>
    </tr>
</table>
@endforeach