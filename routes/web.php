<?php

use Illuminate\Support\Facades\Route;
use app\Http\Controllers\ZohoOauthController;

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
    return view('welcome');
});

Route::get('/success', function () {
    return view('success');
});

Route::get('zoho/oauth/{grant_token}', 'ZohoOauthController@__invoke');