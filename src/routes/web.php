<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TecnologiaController;

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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [TecnologiaController::class, 'index'])->name('tecnologias.index');
Route::get('/tecnologia-new', [TecnologiaController::class, 'create'])->name('tecnologia.create');
Route::get('/tecnologias/{tecnologia}', [TecnologiaController::class, 'show'])->name('tecnologias.show');
Route::put('/tecnologias/{tecnologia}', [TecnologiaController::class, 'update'])->name('tecnologias.update');
Route::post('/tecnologias', [TecnologiaController::class,'store'])->name('tecnologias.store');
Route::delete('/tecnologias/{tecnologia}', [TecnologiaController::class, 'destroy'])->name('tecnologias.destroy');


