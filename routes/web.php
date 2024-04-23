<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Auth\WebsiteAuthController;
use App\Http\Controllers\EmissionController;
use App\Models\Country;
use App\Models\Emission;
use App\Models\Pollutant;
use App\Models\Year;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

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

Route::get('/register', [WebsiteAuthController::class, 'showRegistrationForm'])->name('/register');
Route::get('/login', [WebsiteAuthController::class, 'showLoginForm'])->name('/login');
Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::get('logout', function () {
    
        Cookie::queue(Cookie::forget('jwt_token')); 
        
        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully',
            'redirect' => '/'
        ]);
})->name('logout');

Route::get('/', function () {
    $country = request('country') ?? 'Australia';
    $pollutant = request('pollutant') ?? 'Sulphur Oxides';
    $variable = request('variable') ?? 'Total man-made emissions';

    $emissions = Emission::with('country', 'year', 'pollutant')
        ->when($country, function ($query) use ($country) {
            $query->whereHas('country', function ($query) use ($country) {
                $query->where('name', $country);
            });
        })
        ->when($pollutant, function ($query) use ($pollutant) {
            $query->whereHas('pollutant', function ($query) use ($pollutant) {
                $query->where('name', $pollutant);
            });
        })
        ->when($variable, function ($query) use ($variable) {
            $query->where('variable', $variable);
        })
        ->take(50)->get();

    return view('welcome', [
        'emissions' => $emissions,
        'countries' => Country::all(),
        'pollutants' => Pollutant::all(),
        'variables' => Emission::distinct()->pluck('variable')
    ]);
});

Route::get('/json', [EmissionController::class, 'exportJson']);
Route::get('/xml', [EmissionController::class, 'exportXml']);


Route::post('/import/xml', [EmissionController::class, 'importXml'])->middleware('role:admin');
Route::post('/import/json', [EmissionController::class, 'importJson'])->middleware('role:admin');