<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\JWTAuth;
use Auth;
//use Storage;
use  Illuminate\Support\Facades\Storage;
use DB;
use Carbon\Carbon;
use File;


class UploadfileController extends Controller
{

    public function __construct(){

    }
    public function uploadfile(Request $request){
            Storage::disk('local')->makeDirectory(Auth::user()->id.'/'.$request->id);
            $imagen=DB::table('mascotas')->select('imagenes')->where('id',$request->id)->where('id_usuario',Auth::user()->id)->value('imagenes');
            $imagennueva='';
            $fecha=carbon::now('America/Bogota')->timestamp;
                if($imagen){
                $imagennueva=$imagen.','.$fecha;
            }else{
                $imagennueva=$fecha;                
            }
            DB::table('mascotas')->where('id',$request->id)->where('id_usuario',Auth::user()->id)->update(['imagenes'=>$imagennueva]);
            
            $image = $request->file;
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = $fecha;

            Storage::disk('local')->put(Auth::user()->id.'/'.$request->id.'/'.$imageName, base64_decode($image));

            
            
         return response()->json(['archivo creado exitosamente' => $request]);
    }  
    public function pruebab64(Request $request){

                 
            Storage::disk('local')->makeDirectory(Auth::user()->id.'/'.$request->id);
//            $imagen=DB::table('mascotas')->select('imagenes')->where('id',$request->id)->where('id_usuario',Auth::user()->id)->value('imagenes');
            $imagennueva='';
            $fecha=carbon::now('America/Bogota')->timestamp;
/*                 if($imagen){
                $imagennueva=$imagen.','.$fecha;
            }else{
                                
            } */
          //  DB::table('mascotas')->where('id',$request->id)->where('id_usuario',Auth::user()->id)->update(['imagenes'=>$imagennueva]);
          $imagennueva=$fecha;
          $image = $request->file;

          $image = str_replace('data:image/png;base64,', '', $image);
          $image = str_replace(' ', '+', $image);
          $imageName = str_random(10);
          $rt= Auth::user()->id.'\\'.$request->id;
          echo $rt;
    
          Storage::disk('local')->put(Auth::user()->id.'/'.$request->id.'/'.$imageName, base64_decode($image));

    //      Storage::put('10/10/juan', base64_decode($image));

         return response()->json(['archivo creado exitosamente' => $request]);
    }  
    function base64_to_jpeg($base64_string,$output_file) {
        // open the output file for writing
        $ifp = fopen($output_file, 'wb' ); 
    
        // split the string on commas
        // $data[ 0 ] == "data:image/png;base64"
        // $data[ 1 ] == <actual base64 string>
        $data = explode( ',', $base64_string );
    
        // we could add validation here with ensuring count( $data ) > 1
        fwrite( $ifp, base64_decode( $data[ 1 ] ) );
    
        // clean up the file resource
        fclose( $ifp ); 
    
        return $output_file; 
    }

    public function uploadfile2(Request $request){

                 
            Storage::disk('local')->makeDirectory('productos/'.Auth::user()->id.'/'.$request->id);
            $imagen=DB::table('productos')->select('imagenes')->where('id',$request->id)->where('usuario_id',Auth::user()->id)->value('imagenes');
            $imagennueva='';
            $fecha=carbon::now('America/Bogota')->timestamp;
                if($imagen){
                $imagennueva=$imagen.','.$fecha;
            }else{
                $imagennueva=$fecha;                
            }
            DB::table('productos')->where('id',$request->id)->where('usuario_id',Auth::user()->id)->update(['imagenes'=>$imagennueva]);


            $image = $request->file;
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = $fecha;

            Storage::disk('local')->put('productos/'.Auth::user()->id.'/'.$request->id.'/'.$imageName, base64_decode($image));
            return response()->json(['archivo creado exitosamente' => $request]);
    }       

    public function uploadfile3(Request $request){

        //
                 
            Storage::disk('local')->makeDirectory('pedigree/'.Auth::user()->id);
            $imagennueva='';
            $fecha=carbon::now('America/Bogota')->timestamp;
            $aleatorio= rand ( 100 , 999 );
            $imagennueva=$fecha.$aleatorio;
              
            $image = $request->file;
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = $imagennueva;
            Storage::disk('local')->put('pedigree/'.Auth::user()->id.'/'.$imageName, base64_decode($image));

         return response()->json(['suceess' => 'pedigree/'.Auth::user()->id.'/'.$imagennueva]);
    }  
    public function fotochat(Request $request){ 
                 
            Storage::disk('local')->makeDirectory('mensajes/'.Auth::user()->id);
            $imagennueva='';
            $fecha=carbon::now('America/Bogota')->timestamp;
            $aleatorio= rand ( 100 , 999 );
            $imagennueva=$fecha.$aleatorio; 


            $image = $request->file;
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = $imagennueva;
            Storage::disk('local')->put('mensajes/'.Auth::user()->id.'/'.$imageName, base64_decode($image));


         return response()->json(['suceess' => 'mensajes/'.Auth::user()->id.'/'.$imagennueva]);
    }  
    public function fotousuario(Request $request){
         
            Storage::disk('local')->makeDirectory(Auth::user()->id);
            $aleatorio= rand ( 11 , 99 );
            $imagennueva='perfil'.$aleatorio;
  
            
            $image = $request->file;
            $image = str_replace('data:image/png;base64,', '', $image);
            $image = str_replace(' ', '+', $image);
            $imageName = $imagennueva;

            Storage::disk('local')->put(Auth::user()->id.'/'.$imageName, base64_decode($image));
     
            DB::table('users')->where('id',Auth::user()->id)->update(['img'=>Auth::user()->id.'/'.$imagennueva]);
         return response()->json(['suceess' => Auth::user()->id.'/'.$imagennueva]);
    }
}
