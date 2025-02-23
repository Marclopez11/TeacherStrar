<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\SchoolScheduleController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        $availableSchools = \App\Models\School::whereDoesntHave('users', function($query) {
            $query->where('user_id', auth()->id());
        })->get();

        // Cargar las escuelas del usuario con los conteos
        $userSchools = \App\Models\School::whereHas('users', function($query) {
            $query->where('user_id', auth()->id());
        })
        ->withCount(['groups', 'students', 'users'])
        ->get();

        return view('dashboard', compact('availableSchools', 'userSchools'));
    })->name('dashboard');

    // Rutas de Escuelas
    Route::post('/schools', [SchoolController::class, 'store'])->name('schools.store');
    Route::post('/schools/join', [SchoolController::class, 'join'])->name('schools.join');
    Route::get('/schools/{school}', [SchoolController::class, 'show'])->name('schools.show');
    Route::put('/schools/{school}', [SchoolController::class, 'update'])->name('schools.update');
    Route::get('/api/schools/search', [SchoolController::class, 'search'])->name('schools.search');
    Route::post('/schools/{school}/regenerate-logo', [SchoolController::class, 'regenerateLogo'])->name('schools.regenerate-logo');

    // Rutas de Grupos
    Route::get('/schools/{school}/groups', [GroupController::class, 'index'])->name('groups.index');
    Route::post('/schools/{school}/groups', [GroupController::class, 'store'])->name('groups.store');
    Route::get('/schools/{school}/groups/{group}', [GroupController::class, 'show'])->name('groups.show');
    Route::put('/schools/{school}/groups/{group}', [GroupController::class, 'update'])->name('groups.update');
    Route::delete('/schools/{school}/groups/{group}', [GroupController::class, 'destroy'])->name('groups.destroy');
    Route::post('/schools/{school}/groups/{group}/regenerate-avatar', [GroupController::class, 'regenerateAvatar'])
        ->name('groups.regenerate-avatar');
    Route::get('/schools/{school}/groups/{group}/ranking', [GroupController::class, 'ranking'])->name('groups.ranking');

    // Rutas de Estudiantes
    Route::get('/schools/{school}/students', [StudentController::class, 'index'])->name('students.index');
    Route::post('/schools/{school}/students', [StudentController::class, 'store'])->name('students.store');
    Route::get('/schools/{school}/students/{student}', [StudentController::class, 'show'])->name('students.show');
    Route::put('/schools/{school}/students/{student}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/schools/{school}/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
    Route::patch('/schools/{school}/students/{student}/avatar', [StudentController::class, 'updateAvatar'])
        ->name('students.update-avatar');
    Route::post('/schools/{school}/students/{student}/attitudes', [StudentController::class, 'registerAttitude'])
        ->name('students.register-attitude');
    Route::delete('/schools/{school}/students/{student}/attitudes/{attitude}', [StudentController::class, 'removeAttitude'])
        ->name('students.remove-attitude');
    Route::post('/schools/{school}/students/{student}/quick-attitude', [StudentController::class, 'quickAttitude'])
        ->name('students.quick-attitude');

    // Rutas de Horarios
    Route::middleware(['auth'])->group(function () {
        Route::get('/schools/{school}/schedule', [SchoolScheduleController::class, 'index'])
            ->name('schools.schedule');
        Route::post('/schools/{school}/schedule', [SchoolScheduleController::class, 'updateSchedule'])
            ->name('schools.schedule.update');
        Route::get('/schools/{school}/schedule/pdf', [SchoolScheduleController::class, 'downloadPdf'])
            ->name('schools.schedule.pdf');
        Route::post('/schools/{school}/time-slots', [SchoolScheduleController::class, 'storeTimeSlots'])
            ->name('schools.time-slots.store');
    });
});
