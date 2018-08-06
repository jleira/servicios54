<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use Auth;
use DB;
use Carbon\Carbon;

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
         $results=DB::table('users')->select('id','first_name', 'last_name','img')->where(
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
        $inicial=1;
        if($request->has('cantidad')){
            $inicial=$request->cantidad;
        }
        $final=$inicial+9;
        $vender=$request->vender;
        $arrayexplode=explode(' ',$clave);
         $results=DB::table('mascotas as m')->join('users as u', 'u.id', '=', 'm.id_usuario')
         ->select('m.*','u.first_name','u.last_name')->where(
            function ($query) use($arrayexplode,$vender) {
                for ($i = 0; $i < count($arrayexplode); $i++){
                   $query->orwhere('nombre', 'like',  '%' . $arrayexplode[$i] .'%')->whereNotIn('id_usuario', [Auth::user()->id])->whereIn('vender',$vender)->where('estado',1);
                } 
           }
         )->orWhere( function ($query) use($arrayexplode, $vender) {
            for ($i = 0; $i < count($arrayexplode); $i++){
               $query->orwhere('raza', 'like',  '%' . $arrayexplode[$i] .'%')->whereNotIn('id_usuario', [Auth::user()->id])->whereIn('vender',$vender)->where('estado',1);
            } 
       })->skip($inicial)->take($final-$inicial)->orderBy('id', 'desc')->get();
       $datos['datos']=$results;
       $datos['inicial']=$inicial;
       $datos['final']=$final;

         return response($datos,200);
    }

    public function mascotas2(Request $request)
    {
        $clave=$request->clave;
        $inicial=1;
        if($request->has('cantidad')){
            $inicial=$request->cantidad;
        }
        $final=$inicial+9;
    
        $clave=$request->clave;
        $vender=$request->vender;
        $arrayexplode=explode(' ',$clave);
         $results=DB::table('mascotas')->where(
            function ($query) use($arrayexplode,$vender) {
                for ($i = 0; $i < count($arrayexplode); $i++){
                   $query->orwhere('nombre', 'like',  '%' . $arrayexplode[$i] .'%')->whereIn('vender',$vender)->where('estado',1);
                } 
           }
         )->orWhere( function ($query) use($arrayexplode, $vender) {
            for ($i = 0; $i < count($arrayexplode); $i++){
               $query->orwhere('raza', 'like',  '%' . $arrayexplode[$i] .'%')->whereIn('vender',$vender)->where('estado',1);
            } 
       })->skip($inicial)->take($final-$inicial)->orderBy('id', 'desc')->get();
       $datos['datos']=$results;
       $datos['final']=$final;
       $datos['inicial']=$inicial;
    
         return response($datos,200);      
    }
public function guardaraccesorio(Request $request) 
{
    $this->validate($request, [
        'nombre' => 'required',
        'categoria' => 'required',
        'precio'=>'required',
        'descripcion'=>'present'
    ]);
    if($request->id){    
        DB::table('productos')->where('id',$request->id)->update([
        'nombre'=>$request->nombre,
        'categoria'=>$request->categoria,
        'descripcion'=>$request->descripcion,
        'precio'=>$request->precio
            ]); 
    $productonuevo=DB::table('productos')->where('id',$request->id)->get();
    return response($productonuevo,200);
            
    }

    $fecha=carbon::now('America/Bogota')->toDateTimeString();



DB::table('productos')->insert(
    [
    'usuario_id' => Auth::user()->id,
    'nombre' => $request->nombre, 
    'categoria' => $request->categoria,
    'precio'=> $request->precio,
    'descripcion'=>$request->descripcion,
    'creado'=>$fecha
    ]
);
$productonuevo=DB::table('productos')->where('id',DB::table('productos')->where('usuario_id',Auth::user()->id)->max('id'))->take(1)->get();
return response($productonuevo,200);
        

}

public function accesoriosyservicios(Request $request)
{
    $clave=$request->clave;
    $inicial=1;
    if($request->has('cantidad')){
        $inicial=$request->cantidad;
    }
    $final=$inicial+9;
    $categoria=$request->categoria;
    $arrayexplode=explode(' ',$clave);
     $results=DB::table('productos')->crossJoin('users', function ($join) {
        $join->on('productos.usuario_id', '=', 'users.id');
    })->where(
        function ($query) use($arrayexplode, $categoria) {
            for ($i = 0; $i < count($arrayexplode); $i++){
               $query->orwhere('nombre', 'like',  '%' . $arrayexplode[$i] .'%')->whereNotIn('usuario_id', [Auth::user()->id])->whereIn('categoria',$categoria)->where('estado',1);
            } 
       }
     )->orWhere( function ($query) use($arrayexplode,$categoria) {
        for ($i = 0; $i < count($arrayexplode); $i++){
           $query->orwhere('descripcion', 'like',  '%' . $arrayexplode[$i] .'%')->whereNotIn('usuario_id', [Auth::user()->id])->whereIn('categoria',$categoria)->where('estado',1);
        } 
   })->select('productos.*','users.first_name','users.last_name')->skip($inicial)->take($final-$inicial)->orderBy('productos.id', 'desc')->get();
   $datos['datos']=$results;
     return response($datos,200); 
 
}

public function accesoriosyservicios2(Request $request)
{
    $clave=$request->clave;
    $inicial=1;
    if($request->has('cantidad')){
        $inicial=$request->cantidad;
    }
    $final=$inicial+9;
    $categoria=$request->categoria;
    $arrayexplode=explode(' ',$clave);
     $results=DB::table('productos')->crossJoin('users', function ($join) {
        $join->on('productos.usuario_id', '=', 'users.id');
    })->where(
        function ($query) use($arrayexplode, $categoria) {
            for ($i = 0; $i < count($arrayexplode); $i++){
               $query->orwhere('nombre', 'like',  '%' . $arrayexplode[$i] .'%')->whereIn('categoria',$categoria)->where('estado',1);
            } 
       }
     )->orWhere( function ($query) use($arrayexplode,$categoria) {
        for ($i = 0; $i < count($arrayexplode); $i++){
           $query->orwhere('descripcion', 'like',  '%' . $arrayexplode[$i] .'%')->whereIn('categoria',$categoria)->where('estado',1);
        } 
   })->skip($inicial)->take($final-$inicial)->orderBy('productos.id', 'desc')->select('productos.*','users.first_name','users.last_name')->get();
   $datos['datos']=$results;
     return response($datos,200); 
}

public function misaccesorios($id)
{
    if($id==0){
        $id=Auth::user()->id;
    }
        return response(DB::table('productos')->where('usuario_id',$id)->where('estado',1)->get(),200);  
}

public function todo2 (Request $request)
{
    $clave=$request->clave;
    $inicial1=1;
    $inicial2=1;
    $inicial3=1;
    if($request->has('cantidad1')){
        $inicial1=$request->cantidad1;
    }
    if($request->has('cantidad2')){
        $inicial2=$request->cantidad2;
    }
    if($request->has('cantidad3')){
        $inicial3=$request->cantidad3;
    }
    $final1=$inicial1+9;
    $final2=$inicial2+9;
    $final3=$inicial3+9;
    
    $arrayexplode=explode(' ',$clave);
    $results['people']=[];
    $results['mascotas']=DB::table('mascotas')->where(
        function ($query) use($arrayexplode) {
            for ($i = 0; $i < count($arrayexplode); $i++){
               $query->orwhere('nombre', 'like',  '%' . $arrayexplode[$i] .'%')->whereIn('vender',[1,2])->where('estado',1);
            } 
       }
     )->orWhere( function ($query) use($arrayexplode) {
        for ($i = 0; $i < count($arrayexplode); $i++){
           $query->orwhere('raza', 'like',  '%' . $arrayexplode[$i] .'%')->whereIn('vender',[1,2])->where('estado',1);
        } 
     })->orWhere( function ($query) use($arrayexplode) {
     for ($i = 0; $i < count($arrayexplode); $i++){
       $query->orwhere('color', 'like',  '%' . $arrayexplode[$i] .'%')->whereIn('vender',[1,2]);
     }})->skip($inicial1)->take($final1-$inicial1)->orderBy('id', 'desc')->get();

     $results['productos']=DB::table('productos')->crossJoin('users', function ($join) {
        $join->on('productos.usuario_id', '=', 'users.id');
    })->where(
        function ($query) use($arrayexplode) {
            for ($i = 0; $i < count($arrayexplode); $i++){
               $query->orwhere('nombre', 'like',  '%' . $arrayexplode[$i] .'%');
            } 
       }
     )->orWhere( function ($query) use($arrayexplode) {
        for ($i = 0; $i < count($arrayexplode); $i++){
           $query->orwhere('descripcion', 'like',  '%' . $arrayexplode[$i] .'%');
        } 
   })->select('productos.*','users.first_name','users.last_name')->where('productos.estado',1)->skip($inicial3)->take($final3-$inicial3)->orderBy('id', 'desc')->get();

   $datos['datos']=$results;
   $datos['final']=$final1;
   $datos['inicial']=$inicial1;
   
   return response($datos,200); 

}
public function todo (Request $request)//usuarios conectados
{
    $clave=$request->clave;
    $inicial1=1;
    $inicial2=1;
    $inicial3=1;
    if($request->has('cantidad1')){
        $inicial1=$request->cantidad1;
    }
    if($request->has('cantidad2')){
        $inicial2=$request->cantidad2;
    }
    if($request->has('cantidad3')){
        $inicial3=$request->cantidad3;
    }
    $final1=$inicial1+9;
    $final2=$inicial2+9;
    $final3=$inicial3+9;
    $arrayexplode=explode(' ',$clave);
     $results['people']=DB::table('users')->select('id','first_name', 'last_name','img')->where(
        function ($query) use($arrayexplode) {
            for ($i = 0; $i < count($arrayexplode); $i++){
               $query->orwhere('first_name', 'like',  '%' . $arrayexplode[$i] .'%')->whereNotIn('id', [Auth::user()->id]);
            } 
       }
     )->orWhere( function ($query) use($arrayexplode) {
        for ($i = 0; $i < count($arrayexplode); $i++){
           $query->orwhere('last_name', 'like',  '%' . $arrayexplode[$i] .'%')->whereNotIn('id', [Auth::user()->id]);
        } 
   })->skip($inicial2)->take($final2-$inicial2)->orderBy('id', 'desc')->get();

   $results['mascotas']=DB::table('mascotas as m')->join('users as u', 'u.id', '=', 'm.id_usuario')
         ->select('m.*','u.first_name','u.last_name')->where(
            function ($query) use($arrayexplode) {
                for ($i = 0; $i < count($arrayexplode); $i++){
                   $query->orwhere('nombre', 'like',  '%' . $arrayexplode[$i] .'%')->whereNotIn('id_usuario', [Auth::user()->id])->whereIn('vender',[1,2]);
                } 
           }
         )->orWhere( function ($query) use($arrayexplode) {
            for ($i = 0; $i < count($arrayexplode); $i++){
               $query->orwhere('raza', 'like',  '%' . $arrayexplode[$i] .'%')->whereNotIn('id_usuario', [Auth::user()->id])->whereIn('vender',[1,2]);
            } 
       })->skip($inicial1)->take($final1-$inicial1)->orderBy('id', 'desc')->get();

       $results['productos']=DB::table('productos')->crossJoin('users', function ($join) {
          $join->on('productos.usuario_id', '=', 'users.id');
      })->where(
          function ($query) use($arrayexplode) {
              for ($i = 0; $i < count($arrayexplode); $i++){
                 $query->orwhere('nombre', 'like',  '%' . $arrayexplode[$i] .'%')->whereNotIn('usuario_id', [Auth::user()->id])->where('estado',1);
              } 
         }
       )->orWhere( function ($query) use($arrayexplode) {
          for ($i = 0; $i < count($arrayexplode); $i++){
             $query->orwhere('descripcion', 'like',  '%' . $arrayexplode[$i] .'%')->whereNotIn('usuario_id', [Auth::user()->id])->where('estado',1);
          } 
     })->select('productos.*','users.first_name','users.last_name')->skip($inicial3)->take($final3-$inicial3)->orderBy('productos.id', 'desc')->get();
     $datos['datos']=$results;
     return response($datos,200);
}
    //

    public function todoseguidores (Request $request)//usuarios conectados
{
    $inicial1=1;
    $inicial2=1;
    $inicial3=1;
    if($request->has('cantidad1')){
        $inicial1=$request->cantidad1;
    }
    if($request->has('cantidad2')){
        $inicial2=$request->cantidad2;
    }
    if($request->has('cantidad3')){
        $inicial3=$request->cantidad3;
    }
    $final1=$inicial1+9;
    $final2=$inicial2+9;
    $final3=$inicial3+9;

    $id_seguidos=[];
    $resultsids=DB::table('seguidores')->select('usuario_id')->where('seguidor_id',Auth::user()->id)->get();
    foreach ($resultsids as $item) {
        $id_seguidos[]=$item->usuario_id;
    }    
     $clave=$request->clave;
    $arrayexplode=explode(' ',$clave);
     $results['people']=[];

   $results['mascotas']=DB::table('mascotas as m')->join('users as u', 'u.id', '=', 'm.id_usuario')
         ->select('m.*','u.first_name','u.last_name')->where(
            function ($query) use($arrayexplode, $id_seguidos) {
                for ($i = 0; $i < count($arrayexplode); $i++){
                   $query->orwhere('nombre', 'like',  '%' . $arrayexplode[$i] .'%')->whereIn('id_usuario',  $id_seguidos)->whereIn('vender',[1,2])->where('estado',1);
                } 
           }
         )->orWhere( function ($query) use($arrayexplode, $id_seguidos) {
            for ($i = 0; $i < count($arrayexplode); $i++){
               $query->orwhere('raza', 'like',  '%' . $arrayexplode[$i] .'%')->whereIn('id_usuario',  $id_seguidos)->whereIn('vender',[1,2])->where('estado',1);
            } 
       })->skip($inicial3)->take($final3-$inicial3)->orderBy('m.id', 'desc')->get();
       $results['productos']=DB::table('productos')->crossJoin('users', function ($join) {
          $join->on('productos.usuario_id', '=', 'users.id');
      })->where(
          function ($query) use($arrayexplode, $id_seguidos) {
              for ($i = 0; $i < count($arrayexplode); $i++){
                 $query->orwhere('nombre', 'like',  '%' . $arrayexplode[$i] .'%')->whereIn('usuario_id',  $id_seguidos)->where('estado',1);
              } 
         }
       )->orWhere( function ($query) use($arrayexplode , $id_seguidos) {
          for ($i = 0; $i < count($arrayexplode); $i++){
             $query->orwhere('descripcion', 'like',  '%' . $arrayexplode[$i] .'%')->whereIn('usuario_id',  $id_seguidos)->where('estado',1);
          } 
     })->select('productos.*','users.first_name','users.last_name')->skip($inicial3)->take($final3-$inicial3)->orderBy('productos.id', 'desc')->get();
     $datos['datos']=$results;
     return response($datos,200);
}
public function eliminarproducto(Request $request)
{
    $this->validate($request, [
        'id' => 'required',
        ]);
        $existemascota=0;
        if($request->id){    
            DB::table('productos')->where('id',$request->id)->update([
            'estado'=>2
            ]);     
            return response('OK',200);
        }else{
            return response('Error, debe definir el producto que eliminara',200);

        }

}
}
