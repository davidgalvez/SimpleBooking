<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group([    
    'middleware' => ['auth:sanctum']
], function() {
    Route::get('/user', function (Request $request) {
		return $request->user();
	});
    
});
/*
*Routes for landlord
*/
Route::group([    
	'prefix' => 'landlord'
], function() {
    Route::post('/add_apartment/', 'App\Http\Controllers\LandlordController@addApartment');
	Route::get('/get_apartment/{id}/', 'App\Http\Controllers\LandlordController@getApartment');
	Route::get('/get_my_apartments/{id}/', 'App\Http\Controllers\LandlordController@getMyApartments');
	Route::post('/update_apartment/{id}/', 'App\Http\Controllers\LandlordController@updateApartment');
	Route::get('/confirm_reservation/{id}/', 'App\Http\Controllers\LandlordController@confirmReservation');
});

/**
*Routes for user/clients
*/
Route::group([    
	'prefix' => 'user'
], function() {
    Route::post('/get_apartments/', 'App\Http\Controllers\ClientController@getApartments');
	Route::post('/request_reservation/', 'App\Http\Controllers\ClientController@requestReservation');
});
