<?php

use Illuminate\Http\Request;

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

Route::get('/users', 'Api\ApiController@index');
Route::get('/pp_gedung', 'Api\ApiController@pp_gedung');
Route::get('/pp_laporan_apar', 'Api\ApiController@pp_laporan_apar');
Route::post('/pp_laporan_apar', 'Api\ApiController@save');
Route::get('/pp_laporan_apar/{id}', 'Api\ApiController@pp_laporan_apar_detail');
Route::post('/login', 'Api\ApiController@login');
Route::get('/home', 'Api\ApiController@home');
Route::post('/laporan', 'Api\ApiController@laporan');
Route::post('/sync_data', 'Api\ApiController@save_sync');
Route::post('/ganti-password', 'Api\ApiController@ganti_password');
Route::post('/register', 'Api\RegisterController@register');


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
