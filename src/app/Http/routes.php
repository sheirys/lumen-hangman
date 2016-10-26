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

$app->post('/auth/login', 'AuthController@postLogin');
$app->post('/auth/register', 'AuthController@postRegister');

$app->group([
    'middleware' => 'JwtMiddleware',
    'namespace' => 'App\Http\Controllers'
], function() use($app) {

    $app->get('/game/sessions', 'GameController@getSessionList');
    $app->put('/game/sessions', 'GameController@putSessionNew');
    $app->get('/game/sessions/{game_id}', 'GameController@getSessionState');
    $app->post('/game/sessions/{game_id}', 'GameController@postSessionGuess');

});
