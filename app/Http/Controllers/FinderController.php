<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use Auth;
use DB;

class FinderController extends Controller
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

    public function mascotas(Request $request)
    {
        $clave=$request->clave;
        $arrayexplode=explode(' ',$clave);
         $results=DB::table('mascotas')->where(
            function ($query) use($arrayexplode) {
                for ($i = 0; $i < count($arrayexplode); $i++){
                   $query->orwhere('nombre', 'like',  '%' . $arrayexplode[$i] .'%')->whereNotIn('id_usuario', [Auth::user()->id])->where('vender',1);
                } 
           }
         )->orWhere( function ($query) use($arrayexplode) {
            for ($i = 0; $i < count($arrayexplode); $i++){
               $query->orwhere('raza', 'like',  '%' . $arrayexplode[$i] .'%')->whereNotIn('id', [Auth::user()->id])->where('vender',1);
            } 
       })->get();
       $datos['datos']=$results;
         return response($datos,200); 
     
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
