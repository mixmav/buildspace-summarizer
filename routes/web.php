<?php

use Illuminate\Support\Facades\Route;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;


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

Route::post('/api/summarize', function(){
	$process = new Process(['python', '../resources/external_scripts/transcription.py', request('videoId')]);
	$process->run();

	if (!$process->isSuccessful()) {
		return 'invalid URL';
	}

	$json = json_decode($process->getOutput());
	return $json;
});