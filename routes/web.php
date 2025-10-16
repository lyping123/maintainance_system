<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\reportsController;
use App\Http\Controllers\technicalPeopleController;
use App\Models\report;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        // 'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::get('/reports',[reportsController::class,'index'])->name("report.index");


Route::middleware('auth')->group(function () {
    Route::get('/dashboard',[reportsController::class,'dashboard'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/reports',[reportsController::class,'index'])->name("report.index");
    Route::post('/report/store',[reportsController::class,'store'])->name("report.store");
    Route::post('/report/update/{report}',[reportsController::class,'update'])->name('report.update');
    Route::delete('/report/destroy/{report}',[reportsController::class,'destroy'])->name('report.destroy');
    Route::post('/report/{id}/close/',[reportsController::class,'closeReport'])->name('report.close');

    Route::get('/report/{id}/detail',[reportsController::class,'reportDetail'])->name('report.detail.show');
    Route::post('/report/{id}/assign',[reportsController::class,'assignTechnician'])->name('report.assign.technician');
    Route::put('/report/{id}/assign/update',[reportsController::class,'updateAssignTechnician'])->name('report.assign.technician.update');
    Route::delete('/report/{id}/assign/destroy',[reportsController::class,'destroyAssignTechnician'])->name('report.destroye.assign.technician');
    

    Route::get('/personLists',[technicalPeopleController::class,'index'])->name('technical.index');
    Route::post('/person/store',[technicalPeopleController::class,'store'])->name('technical.store');
    Route::put('/person/{technical_person}/update',[technicalPeopleController::class,'update'])->name('technical.update');
    Route::delete('/person/{technical_person}/destroy',[technicalPeopleController::class,'destroy'])->name('technical.destroy');


});

require __DIR__.'/auth.php';
