<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SummaryController;
use App\Http\Controllers\SubscriberController;


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

Route::inertia('/', 'Home/Page');


// If app debug is true, use the following route
if (config('app.debug')) {
	Route::get('/api/process', [SummaryController::class, 'Process']);
	Route::get('/api/summarize/section', [SummaryController::class, 'SummarizeSection']);
	Route::get('/api/subscriber/subscribe', [SubscriberController::class, 'Subscribe']);
}

Route::post('/api/process', [SummaryController::class, 'Process']);
Route::post('/api/summarize/section', [SummaryController::class, 'SummarizeSection']);
Route::post('/api/subscriber/subscribe', [SubscriberController::class, 'Subscribe']);