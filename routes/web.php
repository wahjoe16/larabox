<?php

use App\Http\Controllers\DropFileController;
use Illuminate\Support\Facades\Route;

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

Route::get('drop', [DropFileController::class, 'index']);
Route::post('drop', [DropFileController::class, 'store']);
Route::get('drop/{filetitle}', [DropFileController::class, 'show']);
Route::get('drop/{filetitle}/download', [DropFileController::class, 'download']);
Route::get('drop/{id}/destroy', [DropFileController::class, 'destroy']);
