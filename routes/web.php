<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

/*Admin section routes*/
$router->group(['prefix' => 'admin','middleware' => 'auth'], function () use ($router) {

    $router->get('trackers/{id}',  ['uses' => 'TrackerController@getOneTracker']);

    $router->get('trackers',  ['uses' => 'TrackerController@getAllTrackers']);
    
    $router->post('trackers', ['uses' => 'TrackerController@create']);
  
    $router->delete('trackers/{id}', ['uses' => 'TrackerController@delete']);
  
    $router->put('trackers/{id}', ['uses' => 'TrackerController@update']);

    $router->post('twitter/search', ['uses' =>'TwitterApiController@searchTwitterApi']);

    $router->post('twitter/tracker',['uses'=>'TwitterController@getTweets']);

    $router->post('twitter/count',['uses'=>'TwitterController@getTweetCountByDay']);

    $router->post('export',['uses'=>'ExportController@createCsv']);

  });


