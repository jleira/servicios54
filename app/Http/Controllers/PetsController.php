<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use Auth;
use DB;
class PetsController extends Controller
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

public function crearmascota(Request $request)
{
    $this->validate($request, [
        'nombre' => 'required',
        'sexo' => 'required',
        'raza' => 'required',
        'color' => 'required',
        'microchip' => 'required',
        'vender' =>'required',
        'precio'=>'required'
        ]);
        $existemascota=0;
        $existemascota=DB::table('mascotas')->where('id_usuario',Auth::user()->id)->
        where('nombre',$request->nombre)->
        where('sexo',$request->sexo)->
        where('raza',$request->raza)->
        where('color',$request->color)->
        where('microchip',$request->microchip)->
        count();

        if($existemascota>0){
            return response('Ya tiene una mascota registrada con esta informacion',422);
        }
        DB::table('mascotas')->insert(
            [
        'id_usuario'=>Auth::user()->id,
        'nombre'=>$request->nombre,
        'sexo'=>$request->sexo,
        'raza'=>$request->raza,
        'color'=>$request->color,
        'microchip'=>$request->microchip,
        'vender'=>$request->vender,
        'precio'=>$request->precio
            ]
        ); 
        $macotanueva=DB::table('mascotas')->where('id',DB::table('mascotas')->where('id_usuario',Auth::user()->id)->max('id'))->get();
        return response($macotanueva,200);
}
public function mismascotas($id)
{
    if($id==0){
        $id=Auth::user()->id;
    }
        return response(DB::table('mascotas')->where('id_usuario',$id)->get(),200);  
}

public function prueba()
{
    if(tienepermisos([6,7,8])){   
       $categorias = DB::table('categorias')->select("id_categoria","nombre","referencia","descripcion")->where('id_empresa',5)->orderBy('id_categoria','DESC')->get();
    if(count($categorias)>0){
               return response($categorias,200);
}else{
    return response('No se encuentran Categorias registradas',204);
      }

    }else{
        $key = "message.noautorizado";
        return response(trans($key),401);  
    }

}
    public function editarcategoria(Request $request)
    {
          if(tienepermisos([6,7])){   

    $this->validate($request, [
            'nombre' => 'required',
            'id' => 'required'
    ]);
    $categoria=DB::table('categorias')->select('id','nombre')->where('id_empresa',Auth::user()->id_empresa)->where('id_categoria',$request->id)->first();
    if($categoria->nombre==$request->nombre){

    }else{
    $validador=DB::table('categorias')->where('id_empresa',Auth::user()->id_empresa)->where('nombre',$request->nombre)->first();
         if(($validador)){
            $this->validate($request, [
                'nombre' => 'required|unique:categorias,nombre',
         ]);
         }    
       }
       if(!$request->has('descripcion')){
        $request->descripcion="";
       }
       if(!$request->has('referencia')){
        $request->referencia="";
       }
       DB::table('categorias')->where('id',$categoria->id)->update(
        [
        'nombre' => $request->nombre,
        'referencia' => $request->referencia,
        'descripcion'=> $request->descripcion        
        ]
    );
        $key = "message.categoriaeditada";
        return response(trans($key),200);

        }else{
        $key = "message.noautorizado";
        return response(trans($key),401);  
    }
    }

}




