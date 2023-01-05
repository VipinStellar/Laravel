<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DownloadPass;

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

// Route::get('/downloadpass/{id}/{pass_no}', function () {
//     return view('downloadpass');
// });


 Route::resource('/downloadpass/{id}/{pass_no}', DownloadPass::class);