<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

//Root of lumen
$app->get('/auth/login', function () use ($app) {
    return ($app->version().' powered by jesusleira');
});

//Function to generate a random Key
$app->get('/key', function() {
        $key = "message.categoriaguardada";
	return trans($key);
});

//Route to login users
$app->post('api/auth/login', 'AuthController@login');
$app->get('api/empresasactivas','ExampleController@empresasactivas');
$app->post('api/auth/register','AuthController@register');
$app->post('/api/find/mascotas2', 'FinderController@mascotas2');    
$app->post('/api/find/accesoriosyservicios2', 'FinderController@accesoriosyservicios2');
$app->post('/api/upload/files', 'UploadfileController@prueba');    
$app->post('/api/find/todo2', 'FinderController@todo2');    
$app->get ('/api/find/pedigree/{id}', 'PetsController@mispedigree2');    
$app->get ('/passwordrecovery', 'AuthController@cambiarps');    
$app->post('/resetpass', 'AuthController@reset');    



 
//GRoup with protection to login users.
$app->group(['middleware' => 'auth:api'], function($app)
{
    $app->get('/test', function() {
        return response()->json([
            'message' => 'Hello World!',
        ]);
    }); 
    $app->post('/api/find/todo', 'FinderController@todo');    
    $app->post('/api/find/todoseguidores', 'FinderController@todoseguidores');
    
    $app->get('/api/mydata', 'AuthController@mydata');
    $app->post('/api-material-guardarcategoria', 'CategoriaController@nuevacategoria');    
    $app->post('/api-material-editarcategoria', 'CategoriaController@editarcategoria');    
    $app->get('/api-material-categorias', 'CategoriaController@categorias');
    $app->post('/api/pets/agregar', 'PetsController@crearmascota');    
    $app->get('/api/pets/mismascotas/{id}', 'PetsController@mismascotas');    
    $app->post('/api/find/people', 'FinderController@people');    
    $app->get('/api/finduser/findbyid/{id}', 'AuthController@findbyid');
    $app->get('/api/follow/seguir/{id}', 'AuthController@seguir');
    $app->get('/api/follow/dejardeseguir/{id}', 'AuthController@dejardeseguir');

    $app->post('/api/find/mascotas', 'FinderController@mascotas');    
    $app->post('/api/mascotas/eliminar', 'PetsController@eliminar');    
    
    $app->get('/api/chat/mensajes/usuario', 'ChatController@mischat');
    $app->get('/api/chat/mensajes/eliminar/{id}', 'ChatController@eliminarchat');

    $app->get('/api/chat/mensajes/{recibe}', 'ChatController@mensajes');
    $app->post('/api/chat/mensajes/guardar', 'ChatController@insertmsj');
    $app->post('/api/chat/mensajesmascota/guardar', 'ChatController@insertmsjmascota');


    $app->post('/api/guardar/accesorios', 'FinderController@guardaraccesorio');
    $app->post('/api/find/accesoriosyservicios', 'FinderController@accesoriosyservicios');
    $app->get ('/api/find/misaccesorios/{id}', 'FinderController@misaccesorios');    
    $app->get('/api/find/accesorios2', 'FinderController@prueba');

    $app->post('api/auth/cambiarpass', 'AuthController@cambiarpass');

    $app->post('/api/agregar/pedigree', 'PetsController@agregarpedigree');
    $app->get ('/api/find/mispedigree/{id}', 'PetsController@mispedigree');    

    $app->get('/api/user', 'UserController@getAuthUser');
    $app->post('/api/photoupload', 'UploadfileController@uploadfile');
    $app->post('/api/photouploadaccesorio', 'UploadfileController@uploadfile2');
    $app->post('/api/photouploadpedigree', 'UploadfileController@uploadfile3');
    $app->post('/api/fotochat', 'UploadfileController@fotochat');
    $app->post('/api/fotousuario', 'UploadfileController@fotousuario');
    $app->post('/api/pruebab64', 'UploadfileController@pruebab64');



// http://localhost/servicios54/public/api/find/mispedigree/0
//    http://167.114.185.216/servicios54/public//api/find/accesorios
//   $app->get('/api-material-categorias', 'CategoriaController@prueba');

});

