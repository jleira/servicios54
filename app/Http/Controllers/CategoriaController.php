<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use Auth;
use DB;
class CategoriaController extends Controller
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

   public function nuevacategoria(Request $request)
   {
    if(tienepermisos([6])){   
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
        $key = "message.categoriaguardada";
        return response(trans($key),200);
    }else{
        $key = "message.noautorizado";
        return response(trans($key),401);

    }

}

public function mydata()
{
   return response(Auth::user(),200);
}
public function categorias()
{
    $cantidad=$_GET['cantidadinicial'];
    if(tienepermisos([6,7,8])){   
       $categorias = DB::table('categorias')->select("id_categoria","nombre","referencia","descripcion")->where('id_empresa',Auth::user()->id_empresa)->where('id_categoria','>',$cantidad)->orderBy('id_categoria','ASC')->get();
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




