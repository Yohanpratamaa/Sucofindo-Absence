<?php

use Illuminate\Support\Facades\Route;

// Test route untuk halaman gabungan attendance
Route::get('/test-combined-attendance', function () {
    return view('filament.pegawai.pages.attendance-page', [
        'attendanceType' => 'WFO',
        'todayAttendance' => null,
        'canCheckIn' => true,
        'canCheckOut' => false,
        'canCheckInPagi' => false,
        'canCheckInSiang' => false,
    ]);
})->name('test.combined.attendance');

// Test route untuk melihat apakah halaman lama masih dapat diakses
Route::get('/test-wfo', function () {
    return redirect('/pegawai/wfo-attendance');
})->name('test.wfo');

Route::get('/test-dinas-luar', function () {
    return redirect('/pegawai/dinas-l-luar-attendance');
})->name('test.dinas.luar');
