<?php

use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\LeaveBalanceController;
use App\Http\Controllers\EquilibriumController;
use Illuminate\Support\Facades\Route;

Route::apiResource('contacts', ContactController::class);
Route::post('leave-balance/calculate', [LeaveBalanceController::class, 'calculate']);



Route::post('/equilibrium', [EquilibriumController::class, 'findEquilibrium']);
