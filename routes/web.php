<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('home');
});


Route::group(['middleware' => ['auth']], function(){

	Route::get('/gasStationsCount', 'StationController@count' );

	Route::get('/stats' , 'StationController@stats');

	Route::get('/prices/{gasstation_id?}' , 'StationController@stationData' );

	Route::get('/gasStations/{format?}', 'StationController@allStations' );

	Route::post('/order' , 'UserController@order');
});

Route::group(['middleware' => ['owner']], function(){
	
	Route::get('/orders/{gasstation_id}' , 'StationController@orders');
	Route::post('/update/{gasstation_id}' , 'StationController@update');

});

Auth::routes();

Route::get('/logout' , function() {
	Auth::logout();
	return redirect('/');
});

Route::get('/home', function() {
	return redirect('/');
});

