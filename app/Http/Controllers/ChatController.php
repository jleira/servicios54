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
                   $query->where('usuario_envia',Auth::user()->id)->where('usuario_recibe', $recibe);
           }
         )->orWhere( function ($query) use($recibe) {
                $query->where('usuario_envia',$recibe)->where('usuario_recibe', Auth::user()->id);
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
    $fecha=carbon::now()->toDateTimeString();
    $id=0;
    if($request->has('id')){
        $id=$request->id;
    }
    if($id==0){
        $id1=DB::table('chat')->where('usuario1',Auth::user()->id)->where('usuario2',$request->usuario_recibe)->value('id');        
        $id2=DB::table('chat')->where('usuario2',Auth::user()->id)->where('usuario1',$request->usuario_recibe)->value('id');
        if($id2==0 && $id1==0){
            DB::table('chat')->insert(
                [
                'usuario1' => Auth::user()->id,
                'usuario2' => $request->usuario_recibe, 
                'fecha1' => $fecha,
                'fecha2'=> $fecha,
                'fecha'=>$fecha,
                'msj'=>$request->mensaje,
                'habilitado1'=>1,
                'habilitado2'=>1
                ]
            );
            $id=DB::table('chat')->where('usuario1',Auth::user()->id)->where('usuario2',$request->usuario_recibe)->value('id');
        }else{
            if($id1){
                $id=$id1;
                DB::table('chat')
                ->where('id', $id)
                ->update(['fecha' => $fecha,'msj'=>$request->mensaje,'habilitado1'=>1,'habilitado2'=>1]);
            }
            if($id2){
                $id=$id2;
                DB::table('chat')
                ->where('id', $id)
                ->update(['fecha' => $fecha,'msj'=>$request->mensaje,'habilitado1'=>1,'habilitado2'=>1]);    
            }
        }

    }else{
        DB::table('chat')
            ->where('id', $id)
            ->update(['fecha' => $fecha,'msj'=>$request->mensaje,'habilitado1'=>1,'habilitado2'=>1]);
    }    
    DB::table('mensajes')->insert(
        [
        'usuario_envia' => Auth::user()->id,
        'usuario_recibe' => $request->usuario_recibe, 
        'mensaje' => $request->mensaje,
        'tipo' =>$request->tipo,
        'creado'=> $fecha
        ]
    );
            
    return response($id,200);
    }

    public function mischat(){
        $pr=0;
$results=DB::table('chat')->join('users as a', 'a.id', '=', 'chat.usuario1')
->join('users as b', 'b.id', '=', 'chat.usuario2')
->select('a.first_name as usuario1name','a.last_name as usuario1lastname','a.img as img1','b.first_name as usuario2name','b.last_name as usuario2lastname','b.img as img2','chat.*')->
where('usuario1',Auth::user()->id)->Orwhere('usuario2',Auth::user()->id)->orderby('fecha','desc')->get();

       $datos['datos']=$results;
         
    return response($datos,200);      
       
    }

    public function eliminarchat($id)
    {
        $datos=DB::table('chat')->where('id',$id)->get()->last();
        $fecha=carbon::now()->toDateTimeString();
        if($datos->usuario1==Auth::user()->id){
            DB::table('chat')
            ->where('id', $id)
            ->update(['habilitado1'=>0,'fecha1'=>$fecha]);
        }
        if($datos->usuario2==Auth::user()->id){
            DB::table('chat')
            ->where('id', $id)
            ->update(['habilitado2'=>0,'fecha2'=>$fecha]);

        }
        return $this->mischat();

    } 

    //



    public function insertmsjmascota(Request $request)
    {
        $this->validate($request, [
            'id_usuario' => 'required',
            'mensaje' => 'required'
    ]);
    $fecha=carbon::now()->toDateTimeString();
    $id=0;
   
    if($id==0){
        $id1=DB::table('chat')->where('usuario1',Auth::user()->id)->where('usuario2',$request->id_usuario)->value('id');        
        $id2=DB::table('chat')->where('usuario2',Auth::user()->id)->where('usuario1',$request->id_usuario)->value('id');
        if($id2==0 && $id1==0){
            DB::table('chat')->insert(
                [
                'usuario1' => Auth::user()->id,
                'usuario2' => $request->id_usuario, 
                'fecha1' => $fecha,
                'fecha2'=> $fecha,
                'fecha'=>$fecha,
                'msj'=>$request->mensaje,
                'habilitado1'=>1,
                'habilitado2'=>1
                ]
            );
            $id=DB::table('chat')->where('usuario1',Auth::user()->id)->where('usuario2',$request->id_usuario)->value('id');
        }else{
            if($id1){
                $id=$id1;
                DB::table('chat')
                ->where('id', $id)
                ->update(['fecha' => $fecha,'msj'=>$request->mensaje,'habilitado1'=>1,'habilitado2'=>1]);
            }
            if($id2){
                $id=$id2;
                DB::table('chat')
                ->where('id', $id)
                ->update(['fecha' => $fecha,'msj'=>$request->mensaje,'habilitado1'=>1,'habilitado2'=>1]);    
            }
        }

    }
    DB::table('mensajes')->insert(
        [
        'usuario_envia' => Auth::user()->id,
        'usuario_recibe' => $request->id_usuario, 
        'mensaje' => $request->mascota,
        'tipo' =>$request->tipo,
        'creado'=> $fecha
        ]
    );
    return response($id,200);
    }

}
