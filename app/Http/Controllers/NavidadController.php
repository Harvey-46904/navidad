<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use Barryvdh\DomPDF\Facade\Pdf;
class NavidadController extends Controller
{
    public function inicio(){

        if (!Auth::check()) {
            return redirect('/admin'); // Redirige al login si no está autenticado
        }
    
       
        //1. amigo no asignado
        //2. amigo asignado
        //3. regalos
        //4. escoojer regalos
        //consulta si tiene amigo secreto
       //return response(["data"=>self::obtenerlistaregalos()]);
        $userId = Auth::id();
        $estado=1;
        $amigo_secreto=DB::table('amigos')->where('id_personal',$userId)->first();
        if ($amigo_secreto) {
            $regalos = DB::table('regalos')
            ->select()
            ->where('id_usuario', $userId) // Excluir al usuario actual
            ->get();
            $estado=2;
            $familia=self::obtenerRegalosDisponibles();
            $darregalos=self::obtenerlistaregalos();
            return view('welcome',compact('estado','regalos','familia','darregalos'));
        } else {
            $users = DB::table('users')
            ->select('id','name')
            ->where('id', '!=', $userId) // Excluir al usuario actual
            ->get();
            return view('welcome',compact('users','estado'));
        }
      
    }
    public function obtenerlistaregalos(){
        $userId = Auth::id();
        $reservas=DB::table("reservas")
        ->select("regalos.nombre","regalos.descripcion","regalos.donde","users.name","reservas.id")
        ->join("regalos","reservas.id_regalo","=","regalos.id")
        ->join("users","regalos.id_usuario","=","users.id")
        ->where("id_user",$userId)
        ->get();
        return $reservas;
    }
    public function obtenerRegalosDisponibles()
    {
        $userId = Auth::id();
    // Realizamos la consulta con un join y filtramos por estado 'disponible'
    $regalos = DB::table('regalos')
        ->join('users', 'regalos.id_usuario', '=', 'users.id') // Unimos ambas tablas
        ->select('users.name as usuario', 'regalos.id', 'regalos.nombre', 'regalos.descripcion', 'regalos.estado','regalos.donde')
        ->where('regalos.estado', 'disponible') // Filtramos solo los regalos disponibles
        ->where('users.id',"!=", $userId) // Filtramos solo los regalos disponibles
        ->get();

    // Agrupamos los resultados en un array usando el nombre del usuario como clave
   
    $resultado = $regalos->groupBy('usuario')->toArray();
    $familia=[
        "familia"=>$resultado
    ];
    // Retornamos el resultado para probarlo (puedes pasarlo a una vista si lo necesitas)
    return $resultado;
    }
    public function RegistroAmigoSecreto(Request $request){
        $userId = Auth::id();
        DB::table('amigos')->insert([
            'id_personal' => $userId,
            'id_amigo'    => $request->amigo,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
        return back();
    }

    public function RegistroRegalo(Request $request){

        //lugarfoto
        //regalofoto
        $userId = Auth::id();

        $rutaImagenlugar = null;
        $rutaImagenregalo = null;
        if ($request->hasFile('lugarfoto')) {
            $rutaImagenlugar = $request->file('lugarfoto')
                ->store('lugares', 'public'); // como Voyager
        }
        if ($request->hasFile('regalofoto')) {
            $rutaImagenregalo = $request->file('regalofoto')
                ->store('regalos', 'public'); // como Voyager
        }


        DB::table('regalos')->insert([
            'id_usuario' => $userId,
            'nombre'    => $request->nombre,
            'descripcion'=>$request->descripcion,
            'donde'=>$request->donde,
            'regalo'=>$rutaImagenregalo,
            'lugar'=>$rutaImagenlugar,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
        return back();
        return response(["data"=>$request->all()]);
    }
    public function ReservaRegalo(Request $request){
        //return response(["data"=>$request->all()]);
          // Obtener los IDs de los regalos seleccionados desde la petición
    $regalosIds = $request->input('regalos'); // ['5', '6', ...]

    // Obtener el ID del usuario autenticado
    $userId = Auth::id();

    // Verificar si los regalos están disponibles
    $regalos = DB::table('regalos')
        ->whereIn('id', $regalosIds) // Filtrar solo los regalos con los IDs seleccionados
        ->get(); // Obtener todos los regalos seleccionados

    // Inicializamos un array para los regalos procesados
    $regalosProcesados = [];

    // Recorremos los regalos seleccionados
    foreach ($regalos as $regalo) {
        if ($regalo->estado === 'disponible') {
            // Cambiar el estado de este regalo a 'reservado'
            DB::table('regalos')
                ->where('id', $regalo->id)
                ->update(['estado' => 'reservado']);

            // Registrar el regalo en la tabla de reservas
            DB::table('reservas')->insert([
                'id_user' => $userId,  // ID del usuario autenticado
                'id_regalo' => $regalo->id, // ID del regalo
                'created_at' => now(), // Fecha de creación
                'updated_at' => now(), // Fecha de actualización
            ]);

            $regalosProcesados[] = $regalo; // Agregar al array de regalos procesados
        }
    }
    return back();

   
    }
    public function eliminarregalo($id){
        DB::table('regalos')
                ->where('id', $id)
                ->delete();
            return back();  
    }
    public function cancelarreserva($id){

        $consulta= DB::table('reservas')->select('id_regalo')->where("id",$id)->first();
          DB::table('regalos')
                ->where('id', $consulta->id_regalo)
                ->update(['estado' => 'disponible']);
        DB::table('reservas')
        ->where("id",$id)
        ->delete();
        return back();  
        
         return back();  
    }

    public function pdf()
{
    $userId = Auth::id();

    $darregalos = DB::table("reservas")
        ->select(
            "regalos.nombre",
            "regalos.descripcion",
            "regalos.donde",
            "users.name",
            "reservas.id",
            "regalos.regalo",
            "regalos.lugar"
        )
        ->join("regalos", "reservas.id_regalo", "=", "regalos.id")
        ->join("users", "regalos.id_usuario", "=", "users.id")
        ->where("reservas.id_user", $userId)
        ->get();

    $pdf = Pdf::loadView('pdf.regalos', compact('darregalos'));
    return $pdf->download('carta-regalos.pdf');
}
}
