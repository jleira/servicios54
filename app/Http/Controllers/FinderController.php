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
        $vender=$request->vender;
        $arrayexplode=explode(' ',$clave);
         $results=DB::table('mascotas')->where(
            function ($query) use($arrayexplode,$vender) {
                for ($i = 0; $i < count($arrayexplode); $i++){
                   $query->orwhere('nombre', 'like',  '%' . $arrayexplode[$i] .'%')->whereNotIn('id_usuario', [Auth::user()->id])->whereIn('vender',$vender);
                } 
           }
         )->orWhere( function ($query) use($arrayexplode, $vender) {
            for ($i = 0; $i < count($arrayexplode); $i++){
               $query->orwhere('raza', 'like',  '%' . $arrayexplode[$i] .'%')->whereNotIn('id_usuario', [Auth::user()->id])->whereIn('vender',$vender);
            } 
       })->get();
       $datos['datos']=$results;
         return response($datos,200); 
     
    }

    public function mascotas2(Request $request)
    {
        $clave=$request->clave;
        $vender=$request->vender;
        $arrayexplode=explode(' ',$clave);
         $results=DB::table('mascotas')->where(
            function ($query) use($arrayexplode,$vender) {
                for ($i = 0; $i < count($arrayexplode); $i++){
                   $query->orwhere('nombre', 'like',  '%' . $arrayexplode[$i] .'%')->whereIn('vender',$vender);
                } 
           }
         )->orWhere( function ($query) use($arrayexplode, $vender) {
            for ($i = 0; $i < count($arrayexplode); $i++){
               $query->orwhere('raza', 'like',  '%' . $arrayexplode[$i] .'%')->whereIn('vender',$vender);
            } 
       })->get();
       $datos['datos']=$results;
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
    $categoria=$request->categoria;
    $arrayexplode=explode(' ',$clave);
     $results=DB::table('productos')->crossJoin('users', function ($join) {
        $join->on('productos.usuario_id', '=', 'users.id');
    })->where(
        function ($query) use($arrayexplode, $categoria) {
            for ($i = 0; $i < count($arrayexplode); $i++){
               $query->orwhere('nombre', 'like',  '%' . $arrayexplode[$i] .'%')->whereNotIn('usuario_id', [Auth::user()->id])->whereIn('categoria',$categoria);
            } 
       }
     )->orWhere( function ($query) use($arrayexplode,$categoria) {
        for ($i = 0; $i < count($arrayexplode); $i++){
           $query->orwhere('descripcion', 'like',  '%' . $arrayexplode[$i] .'%')->whereNotIn('usuario_id', [Auth::user()->id])->whereIn('categoria',$categoria);
        } 
   })->select('productos.nombre','users.first_name','users.last_name','productos.categoria','productos.precio','productos.descripcion')->get();
   $datos['datos']=$results;
     return response($datos,200); 
 
}

public function accesoriosyservicios2(Request $request)
{
    $clave=$request->clave;
    $categoria=$request->categoria;
    $arrayexplode=explode(' ',$clave);
     $results=DB::table('productos')->crossJoin('users', function ($join) {
        $join->on('productos.usuario_id', '=', 'users.id');
    })->where(
        function ($query) use($arrayexplode, $categoria) {
            for ($i = 0; $i < count($arrayexplode); $i++){
               $query->orwhere('nombre', 'like',  '%' . $arrayexplode[$i] .'%')->whereIn('categoria',$categoria);
            } 
       }
     )->orWhere( function ($query) use($arrayexplode,$categoria) {
        for ($i = 0; $i < count($arrayexplode); $i++){
           $query->orwhere('descripcion', 'like',  '%' . $arrayexplode[$i] .'%')->whereIn('categoria',$categoria);
        } 
   })->select('productos.*','users.first_name','users.last_name')->get();
   $datos['datos']=$results;
     return response($datos,200); 
 
}

public function misaccesorios($id)
{
    if($id==0){
        $id=Auth::user()->id;
    }
        return response(DB::table('productos')->where('usuario_id',$id)->get(),200);  
}

public function prueba(){
    $users = DB::table('users')
            ->crossJoin('mensajes')
            ->get();
    return response($users);
}

    //
}