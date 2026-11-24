<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Signed trigger from the GitHub Actions workflow in the dumps repo, four
// times a day. See scripts/backup/README.md. Rate-limited per IP; HMAC-checked; 202.
Route::post('/backup/trigger', \App\Http\Controllers\BackupTriggerController::class)
    ->middleware(['throttle:' . (int) config('dump-github.trigger.rate_per_hour', 12) . ',60', 'backup.signature'])
    ->name('backup.trigger');
