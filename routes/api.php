<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\SeatsController;
use App\Http\Controllers\TicketController;
use App\Http\Middleware\JWTAuthMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware([JWTAuthMiddleware::class])->get('/user', function () {
    return auth()->user();
});

//*********************** AUTH İŞLEMLERİ ***********************
Route::prefix('auth')->group(function (){
    Route::post('/register',[AuthController::class,'register'])->name('auth-register');
    Route::post('/login',[AuthController::class,'login'])->name('auth-login');
    Route::post('/refresh',[AuthController::class,'refresh'])->name('auth-refresh')->middleware('jwt.auth');
    Route::post('/logout',[AuthController::class,'logout'])->name('auth-logout')->middleware('jwt.auth');
});
//*********************** ETKİNLİK İŞLEMLERİ ***********************
Route::prefix('events')->group(function (){
    Route::get('/',[EventsController::class,'index'])->name('events-index');
    Route::get('/{id}',[EventsController::class,'getEvent'])->name('get-event-index');
    Route::post('/',[EventsController::class,'store'])->name('events-store-admin');
    Route::delete('/{id}',[EventsController::class,'destroy'])->name('event-delete-admin');
    Route::put('/{id}',[EventsController::class,'update'])->name('event-update-admin');
});
//*********************** KOLTUK İŞLEMLERİ ***********************
Route::get('events/{id}/seats',[SeatsController::class,'events'])->name('seats-event');
Route::get('venues/{id}/seats',[SeatsController::class,'venues'])->name('seats-venues');
Route::prefix('seats')->group(function (){
    Route::post('/block',[SeatsController::class,'block'])->name('seats-block');
    Route::delete('/release',[SeatsController::class,'release'])->name('seats-release');
});
//*********************** REZERVASYON İŞLEMLERİ ***********************
Route::prefix('reservations')->group(function (){
    Route::get('/',[ReservationController::class,'index'])->name('reservation-index');
    Route::post('/',[ReservationController::class,'postIndex'])->name('reservation-post-index');
    Route::get('/{id}',[ReservationController::class,'show'])->name('reservation-show');
    Route::post('/{id}/confirm',[ReservationController::class,'confirm'])->name('reservation-confirm');
    Route::delete('/{id}',[ReservationController::class,'destroy'])->name('reservation-delete');
});

//*********************** Bilet İŞLEMLERİ ***********************
Route::prefix('tickets')->group(function (){
    Route::get('/',[TicketController::class,'index'])->name('ticket-index');
    Route::get('/{id}',[TicketController::class,'show'])->name('ticket-show');
    Route::get('/{id}/download',[TicketController::class,'download'])->name('ticket-download');
    Route::post('/{id}/transfer',[TicketController::class,'transfer'])->name('ticket-transfer');
});

