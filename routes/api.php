<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WeatherController;
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

// Route group for api.
Route::middleware('api')->group(function () {
    // Route group for all auth requests.
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login'])->name('login');
        Route::post('/register', [AuthController::class, 'register'])->name('register');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
        Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
        Route::get('/user-profile', [AuthController::class, 'userProfile'])->name('userProfile');
    });
    // Route group for all weather requests.
    Route::prefix('weather')->group(function () {
        Route::get('/{city}', [WeatherController::class, 'city'])->name('weather-city');
    });

    // Catch all requests outside of scope and return a 404 error.
    // Not using fallback due to it only working for GET and HEAD. We need one for all.
    Route::any('{catchall}', function () {
        return response()->json(['error' => 'route not found'], 404);
    })->where('catchall', '.*')->name('exception');
});

