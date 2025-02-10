<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TecnologiaController;
use App\Http\Controllers\UserController;
use App\Models\User;
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

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    ## Authentication Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    ### DATATABLE
    Route::get('/dashboard', [TecnologiaController::class, 'index'])->name('laravel-datatable');
    Route::get('/laravel-datatables-filter', [TecnologiaController::class, 'dataTableListar'])->name('datatables.index');
    ###

    Route::post('/reporte-background-excel', [TecnologiaController::class, 'reporteBackground'])->name('tecnologias.reportbackground');
    Route::get('/download-report/{filename}', [TecnologiaController::class, 'download'])->name('download.report');
    Route::get('/generar-pdf-background', [TecnologiaController::class, 'reportPdfBackground'])->name('tecnologias.reportPdfBackground');
    Route::get('/generar-pdf', [TecnologiaController::class, 'reportPdf'])->name('tecnologias.reportPdf');

    ## ENDPOINT CRUD
    Route::get('/tecnologia-nueva', [TecnologiaController::class, 'create'])->name('tecnologia.create');
    Route::get('/tecnologias/{tecnologia}', [TecnologiaController::class, 'show'])->name('tecnologias.show');
    Route::put('/tecnologias/{tecnologia}', [TecnologiaController::class, 'update'])->name('tecnologias.update');
    Route::post('/tecnologias', [TecnologiaController::class,'store'])->name('tecnologias.store');
    Route::delete('/tecnologias/{tecnologia}', [TecnologiaController::class, 'destroy'])->name('tecnologias.destroy');
    ##

    ##SUBIR TECNOLOGIAS
    Route::post('/tecnologias-import', [TecnologiaController::class,'importTecnologias'])->name('tecnologias.import');
    ##

    ## ASIGNAR TECNOLOGIA A USUARIOS
    Route::get('/users-list', [UserController::class, 'index'])->name('users.list');
    Route::get('/users', [UserController::class, 'usersList'])->name('users.database');
    Route::get('/tecnologias-user/{user}', [UserController::class, 'tecnologiasUser']);
    Route::post('/asignar-tecnologia/{user}', [UserController::class, 'asignarTecnologia']);

    Route::get('/tus-tecnologias', [UserController::class, 'tusTecnologias'])->name('users.tustecnologias');
    ##
});

require __DIR__.'/auth.php';


