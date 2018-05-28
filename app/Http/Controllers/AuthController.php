<?php
/**
 * Created by PhpStorm.
 * User: manuelbruna
 * Date: 4/3/17
 * Time: 19:59
 */

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use Auth;
use DB;
class AuthController extends Controller
{
    /**
     * @var \Tymon\JWTAuth\JWTAuth
     */
    protected $jwt;

    public function __construct(JWTAuth $jwt)
    {
        $this->jwt = $jwt;
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required|email|max:255',
            'password' => 'required',
        ]);

        try {

            if (! $token = $this->jwt->attempt($request->only('email', 'password'))) {
                return response()->json(['invalid_credentials'], 401);
            }

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], 500);

        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], 500);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent' => $e->getMessage()], 500);

        }
        $datos['token']=$token;
        $datos['user']=Auth::user();
        return response($datos,200);
    }

    public function mydata()
    {
       return response(Auth::user(),200);
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|confirmed|min:6',
            "first_name"=> 'required',
            "last_name"=> 'required',
//            "gender"=> 'required',
//            "birthday"=> 'required',
//            "phone"=> 'required',
//            "cellphone"=> 'required',
//            "address"=> 'required',
//            "city"=> 'required',
//            "province"=> 'required',
//            "country"=> 'required',
        ]);

         DB::table('users')->insert(
            [
                'email' => $request->email, 
                'password' => app('hash')->make($request->password),
                'first_name'=> $request->first_name,
                'last_name'=> $request->last_name]
        ); 

        return $this->login($request);
    }

    public function findbyid($id)
    {
        if($id==0){
            $id=Auth::user()->id;
        }
     $seguidores= DB::table('seguidores')->where('usuario_id',$id)->count();
     $seguidos=  DB::table('seguidores')->where('seguidor_id',$id)->count();
     $nombre=  DB::table('users')->where('id',$id)->value('first_name').' '.DB::table('users')->where('id',$id)->value('last_name');
     $ejemplares =DB::table('mascotas')->where('id_usuario',$id)->count();
     $losigo =DB::table('seguidores')->where('seguidor_id',Auth::user()->id)->count();
     $datos['seguidores']=$seguidores;
     $datos['seguidos']=$seguidos;
     $datos['nombre']=$nombre;
     $datos['ejemplares']=$ejemplares;
     $datos['losigo']=$losigo;
     
       return response($datos,200);
    }


    public function seguir($id){
        $yalosigue=0;
        $yalosigue=DB::table('seguidores')->where('usuario_id',$id)->where('seguidor_id',Auth::user()->id)->count();

        if($yalosigue==0){
            DB::table('seguidores')->insert(
                [
                    'usuario_id' => $id, 
                    'seguidor_id' => Auth::user()->id,
                ]); 
                return response('Ahora ud sigue a este usuario',200);

        }else{
            return response('Ud ya sigue a este usuario',419);
        }



    }
    public function dejardeseguir($id){
        $yalosigue=0;
        $yalosigue=DB::table('seguidores')->where('usuario_id',$id)->where('seguidor_id')->count();

        if($yalosigue==0){
            DB::table('seguidores')->where(
                    'usuario_id',$id)->where( 
                    'seguidor_id' , Auth::user()->id)->delete(); 
                return response('Ahora ud no sigue a este usuario',200);

        }else{
            return response('Ud no sigue a este usuario',419);
        }

    }


}


