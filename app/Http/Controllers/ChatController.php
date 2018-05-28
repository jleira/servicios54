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
        public function people(Request $request)
    {
        $clave=$request->clave;
        $arrayexplode=explode(' ',$clave);
         $results=DB::table('users')->select('id','first_name', 'last_name')->where(
            function ($query) use($arrayexplode) {
                for ($i = 0; $i < count($arrayexplode); $i++){
                   $query->orwhere('first_name', 'like',  '%' . $arrayexplode[$i] .'%')->whereNotIn('id', [Auth::user()->id]);
                } 
           }
         )->orWhere( function ($query) use($arrayexplode) {
            for ($i = 0; $i < count($arrayexplode); $i++){
               $query->orwhere('last_name', 'like',  '%' . $arrayexplode[$i] .'%')->whereNotIn('id', [Auth::user()->id]);
            } 
       })->get();
       $datos['datos']=$results;
         return response($datos,200); 
     
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


    public function nuevacategoria(Request $request)
    {
    $this->validate($request, [
            'nombre' => 'required',
    ]);
    $validador=DB::table('categorias')->where('id_empresa',Auth::user()->id_empresa)->where('nombre',$request->nombre)->first();
        if(($validador)){
            $this->validate($request, [
                'nombre' => 'required|unique:categorias,nombre',
        ]);
    
       }
       if(!$request->has('descripcion')){
        $request->descripcion="";
       }
       if(!$request->has('referencia')){
        $request->referencia="";
       }
       $categoriaid = DB::table('categorias')->where('id_empresa',Auth::user()->id_empresa)->max('id_categoria');

       DB::table('categorias')->insert(
        [
        'id_empresa' => Auth::user()->id_empresa,
        'id_categoria' => $categoriaid+1, 
        'nombre' => $request->nombre,
        'referencia' => $request->referencia,
        'descripcion'=> $request->descripcion        
        ]
    );
    return response('Categoria creada exitosamente',200);
    }

    //
}
