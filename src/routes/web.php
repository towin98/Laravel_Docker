<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TecnologiaController;
use App\Jobs\GenerateReportJob;
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
    // GenerateReportJob::dispatchAfterResponse('cristian_parametro dispatchAfterResponse ');

    // GenerateReportJob::dispatch('cristian_parametrp dispatch ');

    return view('welcome');

    // $params = ['estado' => 'ACTIVO']; // Por ejemplo, filtrar registros
    // GenerateReportJob::dispatch($params);

    // return response('<br> TERMINE');
});


Route::get('/dashboard/{skip}/{take}', [TecnologiaController::class, 'index'])->middleware(['auth', 'verified'])->name('tecnologias.index');

Route::post('/reporte-background-excel', [TecnologiaController::class, 'reporteBackground'])->name('tecnologias.reportbackground');
Route::get('/download-report/{filename}', [TecnologiaController::class, 'download'])->name('download.report');
Route::get('/generar-pdf/{skip}/{take}', [TecnologiaController::class, 'reportPdf'])->name('tecnologias.reportPdf');

// Route::get('/', [TecnologiaController::class, 'index'])->name('tecnologias.index');
Route::get('/tecnologia-new', [TecnologiaController::class, 'create'])->name('tecnologia.create');
Route::get('/tecnologias/{tecnologia}', [TecnologiaController::class, 'show'])->name('tecnologias.show');
Route::put('/tecnologias/{tecnologia}', [TecnologiaController::class, 'update'])->name('tecnologias.update');
Route::post('/tecnologias', [TecnologiaController::class,'store'])->name('tecnologias.store');
Route::delete('/tecnologias/{tecnologia}', [TecnologiaController::class, 'destroy'])->name('tecnologias.destroy');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


