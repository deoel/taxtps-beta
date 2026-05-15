<?php

use App\Http\Controllers\WarRoom2Controller;
use App\Http\Controllers\WarRoomAdvancedController;
use App\Http\Controllers\WarRoomController;
use App\Http\Controllers\WarRoomIntelligenceController;
use App\Http\Controllers\WarRoomUltimateController;
use App\Http\Controllers\WarRoomUnifiedController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::view('war-room', 'pages.war-room')->name('war-room');
});

Route::get('/war-room-b', [WarRoomController::class, 'index'])->name('war.room');
Route::get('/war-room-c', [WarRoom2Controller::class, 'index'])->name('warroom.index');
Route::get('/war-room-unified', [WarRoomUnifiedController::class, 'index'])->name('war-room.unified');
Route::get('/war-room-hub', [WarRoomAdvancedController::class, 'index'])->name('war-room.hub');
Route::get('/war-room-int', [WarRoomIntelligenceController::class, 'index'])->name('war-room.intelligence');
Route::get('/war-room-ultimate', [WarRoomUltimateController::class, 'index'])->name('war-room.ultimate');

require __DIR__.'/settings.php';