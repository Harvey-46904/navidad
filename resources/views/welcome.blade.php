<!doctype html>
<html lang="es">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <title>Navidad</title>

    <style>
        /* Fondo navideño */
        body {
            background-image: url('https://img.freepik.com/fotos-premium/imagens-de-estrelas-de-natal-felizes-colecoes-de-papeis-de-parede-bonitos-ai-gerados_643360-361958.jpg');
            /* Cambia por tu URL */
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            min-height: 100vh;
            /* Ocupa toda la pantalla */
            margin: 0;
        }

        /* Contenido centrado */
        .content {
            text-align: center;
            color: white;

            font-family: Arial, sans-serif;
            font-size: 1rem;
            text-shadow: 2px 2px 4px #000;
        }

        #nieve {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 10;
        }

        input[type="checkbox"] {
            transform: scale(2);
            /* Escala el checkbox */
            /* Ajusta el espaciado si es necesario */
        }
    </style>
</head>

<body>
    <canvas id="nieve"></canvas>
    <nav class="navbar navbar-light bg-success text-light">
        <a class="navbar-brand navbar-light">Regalos Navideños</a>
    </nav>
    <div class="container content">
        <div class="row justify-content-center py-2">
            <div class="col-md-6">

                <h1 class="display-4">Hola <b>{{ Auth::user()->name}}</b></h1>
            </div>
        </div>

        @if ($estado==1)
        <div class="row" id="amigo_secreto">
            <div class="col-md-12 py-5">
                <form action="{{route('amigo.secreto')}}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="exampleFormControlSelect1">Para personalizar tu busqueda primero selecciona a tu
                            amigo secreto</label>
                        <select class="form-control form-control-lg" id="exampleFormControlSelect1" name="amigo">
                            @foreach ($users as $user)
                            <option value="{{$user->id}}">{{$user->name}}</option>
                            @endforeach

                        </select>
                        <label for="exampleFormControlSelect1"><b>NOTA: Asegurate de realizar correctamente esta acción
                                ya que no podras editarla</b></label>
                    </div>
                    <button type="submit" class="btn btn-success">Registrar</button>
                </form>
            </div>
        </div>
        @else
        <div class="row justify-content-center">
            <div class="col-md-6">
                <label for="exampleFormControlSelect1">Escoge una opción</label>
                <div class="btn-group btn-group-lg" role="group" aria-label="Opciones de selección">
                    <button id="btn-quiero-navidad" class="btn btn-success">Qué quiero para Navidad</button>
                    <button id="btn-quiere-familia" class="btn btn-primary">Qué quiere mi familia</button>
                    <button id="btn-debo-comprar" class="btn btn-warning">Qué debo comprar</button>
                </div>
            </div>
        </div>
        <div class="row" id="regalos" style="display: none; margin-top: 20px;">
            <div class="col-md-12">
                <button type="button" class="btn btn-success" data-toggle="modal" data-target="#exampleModal">
                    Agregar Regalo
                </button>
                <br>
                <label for="exampleFormControlSelect1">Podras gestionar los regalos que deseas </label>
                <table class="table table-striped text-light">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Nombre de regalo</th>
                            <th scope="col">Detalles</th>

                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($regalos as $regalo)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $regalo->nombre}}</td>
                            <td><button type="button" class="btn btn-info btn-sm btn-info-regalo"
                                    data-nombre="{{ $regalo->nombre }}" data-descripcion="{{ $regalo->descripcion }}"
                                    data-donde="{{ $regalo->donde }}"
                                    data-img-lugar="{{ $regalo->lugar }}"
                                    data-img-regalo="{{ $regalo->regalo }}">

                                    Expandir
                                </button></td>

                            <td>

                                @if ( setting('site.eliminanr'))

                                <a href="{{ route('eliminar.regalo', $regalo->id) }}" class="btn btn-warning btn-sm"
                                    onclick="return confirm('¿Seguro que deseas eliminar este regalo?')">
                                    Eliminar
                                </a>
                                @endif


                            </td>
                        </tr>
                        @endforeach



                    </tbody>
                </table>
            </div>
        </div>

        <div class="row" id="regalar" style="display: none; margin-top: 20px;">
            Tu familia quiere esto
            <div class="col-md-12">

                <form method="POST" action="{{route('regalos.reserva')}}">
                    @foreach ($familia as $usuario => $regalos)
                    <!-- Mostrar el nombre del usuario como encabezado -->
                    <h3 class="bg-success">{{ $usuario }}</h3>
                    <!-- Agrega tu ruta en el action -->
                    @csrf
                    <!-- Token de seguridad para formularios en Laravel -->
                    <ul class="list-unstyled">
                        @foreach ($regalos as $regalo)
                        <li class="form-check">
                            <!-- Acceder a las propiedades usando notación de objeto -->
                            <input type="checkbox" class="form-check-input" id="regalo_{{ $regalo->id }}"
                                name="regalos[]" value="{{ $regalo->id }}">
                            <label class="form-check-label" for="regalo_{{ $regalo->id }}">
                                - {{ $regalo->nombre }}
                            </label>

                            <!-- Botón info (NO activa el checkbox) -->
                            <button type="button" class="btn btn-info btn-sm btn-info-regalo"
                                data-nombre="{{ $regalo->nombre }}" data-descripcion="{{ $regalo->descripcion }}"
                                data-donde="{{ $regalo->donde }}"
                                data-img-lugar="{{ $regalo->lugar }}"
                                data-img-regalo="{{ $regalo->regalo }}">

                                Expandir
                            </button>
                        </li>



                        @endforeach
                    </ul>
                    <!-- Botón de envío -->

                    <hr> <!-- Línea divisoria entre usuarios -->
                    @endforeach

                    @if (setting('site.reservar'))
                    <button type="submit" class="btn btn-primary mt-2">Reservar regalos</button>
                    @endif

                </form>
            </div>
        </div>

        <div class="row" id="debodar" style="display: none; margin-top: 20px;">
            Carta de regalos que debes de comprar
            <div class="col-md-12">
                <table class="table table-striped text-center text-light">
                    <thead>
                        <tr>
                            <th scope="col">Para</th>
                            <th scope="col">Regalo</th>
                            <th scope="col">Detalle</th>
                            <th scope="col">Cancelar Regalo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($darregalos as $item)
                        <tr>
                            <th scope="row">{{$item->name}}</th>
                            <td>{{$item->nombre}}</td>
                            <td><button type="button" class="btn btn-info btn-sm btn-info-regalo"
                                    data-nombre="{{ $item->nombre }}" data-descripcion="{{ $item->descripcion }}"
                                    data-donde="{{ $item->donde }}"
                                    data-img-lugar="{{ $item->lugar }}"
                                    data-img-regalo="{{ $item->regalo }}"
                                   >

                                    Expandir
                                </button></td>

                            <td> <a href="{{ route('cancelar.regalo', ['id' => $item->id]) }}" class="btn btn-warning"
                                    title="Eliminar">
                                    Cancelar
                                </a></td>
                        </tr>
                        @endforeach

                    </tbody>

                    <a href="{{ route('regalos.pdf') }}" class="btn btn-success mt-3">
                        DECARGA LA CARTA NAVIDEÑA
                    </a>
                </table>
            </div>
        </div>
        @endif


    </div>


    <!-- regalo -->

    <div class="modal fade" id="modalRegalo" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content text-dark">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitulo"></h5>
                    <button class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <p><b>Descripción:</b> <span id="modalDescripcion"></span></p>
                    <p><b>Dónde comprar:</b> <span id="modalDonde"></span></p>

                    <div class="row">
                        <div class="col-md-6 text-center" id="contenedorLugar">
                            <p><b>Lugar</b></p>
                        </div>

                        <div class="col-md-6 text-center" id="contenedorRegalo">
                            <p><b>Regalo</b></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Tu regalo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{route('regalo.registro')}}" enctype="multipart/form-data">
                        @csrf
                        <!-- Campo Nombre -->
                        <div class="form-group">
                            <label for="nombre">Nombre del regalo</label>
                            <input type="text" class="form-control" id="nombre" name="nombre"
                                placeholder="Ingresa el nombre" required>
                        </div>

                        <!-- Campo Descripción -->
                        <div class="form-group">
                            <label for="descripcion">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="4"
                                placeholder="Escribe una descripción" required></textarea>
                        </div>

                        <!-- Campo Donde -->
                        <div class="form-group">
                            <label for="donde">Dónde podria comprarlo</label>
                            <input type="text" class="form-control" id="donde" name="donde"
                                placeholder="Ubicación o referencia" required>
                        </div>

                        <!-- Campo Donde -->
                        <div class="form-group">
                            <label for="donde">Foto del lugar</label>
                            <input type="file" class="form-control" id="lugarfoto" name="lugarfoto">
                        </div>
                        <!-- Campo Donde -->
                        <div class="form-group">
                            <label for="donde">Foto del regalo</label>
                            <input type="file" class="form-control" id="regalofoto" name="regalofoto">
                        </div>


                        <!-- Botón de envío -->


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Registrarlo</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- Option 1: jQuery and Bootstrap Bundle (includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous">
    </script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <!-- Bootstrap 4 JS + Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
$(document).on('click', '.btn-info-regalo', function (e) {
    e.stopPropagation();

    $('#modalTitulo').text($(this).data('nombre'));
    $('#modalDescripcion').text($(this).data('descripcion'));
    $('#modalDonde').text($(this).data('donde'));

    let lugar  = $(this).data('img-lugar');
    let regalo = $(this).data('img-regalo');

    $('#contenedorLugar .contenido').remove();
    $('#contenedorRegalo .contenido').remove();

    // ---- LUGAR ----
    if (!lugar) {
        mostrarTexto('#contenedorLugar');
    } else if (esVideo(lugar)) {
        mostrarVideo('#contenedorLugar', lugar);
    } else {
        mostrarImagen('#contenedorLugar', lugar);
    }

    // ---- REGALO ----
    if (!regalo) {
        mostrarTexto('#contenedorRegalo');
    } else {
        mostrarImagen('#contenedorRegalo', regalo);
    }

    $('#modalRegalo').modal('show');
});

function mostrarTexto(contenedor) {
    $(contenedor).append('<p class="contenido text-muted">No información</p>');
}

function mostrarImagen(contenedor, archivo) {
    $(contenedor).append(`
        <img class="contenido img-fluid rounded"
             src="/storage/${archivo}">
    `);
}

function mostrarVideo(contenedor, archivo) {
    $(contenedor).append(`
        <video class="contenido img-fluid rounded" controls>
            <source src="/storage/${archivo}">
        </video>
    `);
}

function esVideo(archivo) {
    return /\.(mp4|webm|ogg)$/i.test(archivo);
}
</script>
    <script>
        $(document).ready(function () {
        // Ocultar todos los divs al inicio
        $('#regalos, #regalar, #debodar').hide();

        // Botón "Qué quiero para Navidad"
        $('#btn-quiero-navidad').on('click', function () {
            // Ocultar todos los divs y mostrar "regalos"
            $('#regalos, #regalar, #debodar').hide();
            $('#regalos').fadeIn(); // Mostrar con efecto fade
        });

        // Botón "Qué quiere mi familia"
        $('#btn-quiere-familia').on('click', function () {
            // Ocultar todos los divs y mostrar "regalar"
            $('#regalos, #regalar, #debodar').hide();
            $('#regalar').fadeIn(); // Mostrar con efecto fade
        });

        // Botón "Qué debo comprar"
        $('#btn-debo-comprar').on('click', function () {
            // Ocultar todos los divs y mostrar "debodar"
            $('#regalos, #regalar, #debodar').hide();
            $('#debodar').fadeIn(); // Mostrar con efecto fade
        });
    });
    </script>

    <script>
        const canvas = document.getElementById("nieve");
    const ctx = canvas.getContext("2d");
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;

    const numFlakes = 100; // Número de copos de nieve
    const flakes = [];

    // Generar copos de nieve
    for (let i = 0; i < numFlakes; i++) {
        flakes.push({
            x: Math.random() * canvas.width,
            y: Math.random() * canvas.height,
            radius: Math.random() * 3 + 1,
            speed: Math.random() * 2 + 1
        });
    }

    function drawSnow() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = "white";
        ctx.beginPath();

        for (let flake of flakes) {
            ctx.moveTo(flake.x, flake.y);
            ctx.arc(flake.x, flake.y, flake.radius, 0, Math.PI * 2);
        }
        ctx.fill();
        updateSnow();
    }

    function updateSnow() {
        for (let flake of flakes) {
            flake.y += flake.speed;
            if (flake.y > canvas.height) {
                flake.y = 0;
                flake.x = Math.random() * canvas.width;
            }
        }
    }

    function animate() {
        drawSnow();
        requestAnimationFrame(animate);
    }

    animate();
    </script>
    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js" integrity="sha384-+sLIOodYLS7CIrQpBjl+C7nPvqq+FbNUBDunl/OZv93DB7Ln/533i8e/mZXLi/P+" crossorigin="anonymous"></script>
    -->
</body>

</html>