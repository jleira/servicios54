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
        'vender' =>'required'
        ]);
        $existemascota=0;
        if($request->id){    
            DB::table('mascotas')->where('id',$request->id)->update([
            'nombre'=>$request->nombre,
            'sexo'=>$request->sexo,
            'raza'=>$request->raza,
            'color'=>$request->color,
            'microchip'=>$request->microchip,
            'vender'=>$request->vender,
            'precio'=>$request->precio
                ]); 
    
            $macotanueva=DB::table('mascotas')->where('id',$request->id)->get();
            return response($macotanueva,200);

        }

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
        DB::table('pedigree')->insert(
            [
        'usuario_id'=>Auth::user()->id,
        'nombrepedigree'=>$request->nombre,
        'pedigree'=>'{"linea1":[{"nombre":"","imagen":"assets/img/mascota.png","caso":1},{"nombre":"","imagen":"assets/img/mascota.png","caso":1}],"linea2":[{"nombre":"","imagen":"assets/img/mascota.png","caso":1},{"nombre":"","imagen":"assets/img/mascota.png","caso":1},{"nombre":"","imagen":"assets/img/mascota.png","caso":1},{"nombre":"","imagen":"assets/img/mascota.png","caso":1}],"linea3":[{"nombre":"","imagen":"assets/img/mascota.png","caso":1},{"nombre":"","imagen":"assets/img/mascota.png","caso":1},{"nombre":"","imagen":"assets/img/mascota.png","caso":1},{"nombre":"","imagen":"assets/img/mascota.png","caso":1},{"nombre":"","imagen":"assets/img/mascota.png","caso":1},{"nombre":"","imagen":"assets/img/mascota.png","caso":1},{"nombre":"","imagen":"assets/img/mascota.png","caso":1},{"nombre":"","imagen":"assets/img/mascota.png","caso":1}]}',
        'mascota_id'=>DB::table('mascotas')->where('id_usuario',Auth::user()->id)->max('id'),
        'color_pedigree'=>'#DEDEDE'
            ]
        ); 
        $macotanueva=DB::table('mascotas')->where('id',DB::table('mascotas')->where('id_usuario',Auth::user()->id)->max('id'))->take(1)->get();

        return response($macotanueva,200);
}
public function mismascotas($id)
{
    if($id==0){
        $id=Auth::user()->id;
    }
        return response(DB::table('mascotas')->where('id_usuario',$id)->where('estado',1)->get(),200);  
}

public function agregarpedigree(Request $request){
    $this->validate($request, [
        'nombre' => 'required',
        'pedigree' => 'required',
        'mascota_id' => 'required',
        'color' => 'required'
        ]);

        if($request->has('pedigre_id')){
            DB::table('pedigree')->where('id',$request->pedigre_id)->update(
                ['nombrepedigree'=>$request->nombre,
            'pedigree'=>$request->pedigree,
            'color_pedigree'=>$request->color]
            ); 
            $nuevoitem[]=DB::table('pedigree')->where('id',$request->pedigre_id)->take(1)->get();    

        }else{
            DB::table('pedigree')->insert(
                [
            'usuario_id'=>Auth::user()->id,
            'nombrepedigree'=>$request->nombre,
            'pedigree'=>$request->pedigree,
            'mascota_id'=>$request->mascota_id,
            'color_pedigree'=>$request->color
                ]
            ); 
            $nuevoitem=DB::table('pedigree')->where('id',DB::table('pedigree')->where('usuario_id',Auth::user()->id)->max('id'))->take(1)->get();    
        }
        return response($nuevoitem,200);
    
}
public function mispedigree($id){
    if($id==0){
        $id=Auth::user()->id;
    }
    $data=DB::table('pedigree as p')->join('mascotas as m', 'p.mascota_id', '=', 'm.id')->
    select('p.id as pedigree_id','p.*','m.*')->where('p.usuario_id',$id)->where('m.estado',1)->get();
        return response($data,200);  
}
public function mispedigree2($id){
    $data=DB::table('pedigree as p')->join('mascotas as m', 'p.mascota_id', '=', 'm.id')->
    select('p.id as pedigree_id','p.*','m.*')->where('p.mascota_id',$id)->where('m.estado',1)->get();
        return response($data,200);  
}


public function eliminar(Request $request)
{
    $this->validate($request, [
        'nombre' => 'required',
        'sexo' => 'required',
        'raza' => 'required',
        'color' => 'required',
        'microchip' => 'required',
        'vender' =>'required'
        ]);
        $existemascota=0;
        if($request->id){    
            DB::table('mascotas')->where('id',$request->id)->update([
            'estado'=>2
            ]);     
            return response('OK',200);
        }else{
            return response('Error, debe definir la mascota que eliminara',200);

        }

}



}




