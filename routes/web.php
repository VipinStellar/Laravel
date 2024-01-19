<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DownloadPass;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PriceQuotesController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\PayNowController;
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

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/downloadpass/{id}', function () {
//     return view('downloadpass');
// });

// Route::get('/payment', [PaymentController::class, 'test']);

 //Route::resource('/payment', 'App\Http\Controllers\PaymentController@test');
Route::get('/payment/{id}', [PaymentController::class, 'index']);

Route::post('/price-quote', [PriceQuotesController::class, 'index']);
Route::post('/payments/proceed', [ PaymentController::class, 'paymentsProceed']);
Route::get('payment/invoice/{txn_id}', [ PaymentController::class, 'paymentInvoice']);
Route::post('/payment/status/success', [ PaymentController::class, 'paymentSuccess']);
Route::post('/payment/status/failure', [ PaymentController::class, 'paymentFailure']);

Route::get('/downloadpass/{id}', [DownloadPass::class, 'index']);
Route::get('/generate-pdf/{id}/{preview}', [PDFController::class, 'generatePDF']);
Route::get('/view-invoice/{id}/{num}', [PDFController::class, 'viewPDF']);
Route::get('/pay-now/{id}',[PayNowController::class, 'index']);
Route::post('/pay-now/proceed', [ PayNowController::class, 'payNowProceed']);
Route::post('/pay-now/status/success', [ PayNowController::class, 'payNowSuccess']);
Route::post('/pay-now/status/failure', [ PayNowController::class, 'payNowFailure']);
Route::get('/document/{num}/{id}', [PDFController::class, 'printDocument']);
Route::post('/check-pincode', [ PayNowController::class, 'checkPincode']);
 