<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::get('/test', function () {
   
    // $QUERY = \App\Models\User::
	// with(['department:id,name','designation:id,name','deptGroup:id,name'])
    // ->where('id','!=',1)
	// ->get();
	// dd($QUERY);

    $QUERY = \App\Models\InOut::
	with(['employee'])
	->get();
	dd($QUERY);
});
