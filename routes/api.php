<?php
use App\Models\promo_code;
use Illuminate\Http\Request;
use App\Http\Controllers\PromoCodesAPIController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


//Get all promo codes /api/promo_codes
Route::get('/promo_codes', [PromoCodesAPIController::class, 'index']);

//POST Create promo codes /api/promo_codes
Route::post('/promo_codes', [PromoCodesAPIController::class, 'create']);

//PUT update promo codes /api/promo_codes/{id}
Route::put('/promo_codes/{promoCode}', [PromoCodesAPIController::class, 'update']);

///POST update promo code status 
/// 1 = activate, 2 = deactivate, 3 = deleted
///api/promo_codes/action/{id}
Route::put('/promo_codes/action/{promoCode}', [PromoCodesAPIController::class, 'action']);

///GET a single promo code api/promo_codes/{id}
Route::get('/promo_codes/{id}', [PromoCodesAPIController::class, 'single']);

///GET active promo codes api/promo_codes/active
Route::get('/promo_codes/all/active', [PromoCodesAPIController::class, 'active']);

///GET active promo codes api/promo_codes/inactive
Route::get('/promo_codes/all/inactive', [PromoCodesAPIController::class, 'inactive']);

///GET deleted promo codes api/promo_codes/inactive
Route::get('/promo_codes/all/trash', [PromoCodesAPIController::class, 'trash']);

///POST cash in promo code api/get_ride 
Route::post('/get_ride', [PromoCodesAPIController::class, 'getPromoRide']);
