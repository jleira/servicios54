<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use Auth;
use DB;
use Carbon\Carbon;

class ChatController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function mensajes($recibe)
    {

         $results=DB::table('mensajes')->where(
            function ($query) use($recibe) {
                   $query->where('usuario_envia',Auth::user()->id)->orwhere('usuario_recibe', Auth::user()->id);
           }
         )->orWhere( function ($query) use($recibe) {
                $query->where('usuario_envia',$recibe)->orwhere('usuario_recibe', $recibe);
       })->get();
       $datos['datos']=$results;
         return response($datos,200);      
    }

    public function insertmsj(Request $request)
    {
        $this->validate($request, [
            'usuario_recibe' => 'required',
            'mensaje' => 'required'
    ]);

    $fecha=carbon::now('America/Bogota')->toDateTimeString();

    DB::table('mensajes')->insert(
        [
        'usuario_envia' => Auth::user()->id,
        'usuario_recibe' => $request->usuario_recibe, 
        'mensaje' => $request->mensaje,
        'creado'=> $fecha
        ]
    );
            
    return response('mensaje guardado',200);
    }

    //
}
