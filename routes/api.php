<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\RolesPermissonController;
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
Route::middleware('guest')->group(function () {
    Route::post('register', [App\Http\Controllers\Auth\RegisteredUserController::class, 'signup']);
    Route::post('login', [App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'login']);
    // Route::post('forgot-password', [App\Http\Controllers\Auth\PasswordResetLinkController::class,'forgotPassword']);
    // Route::post('reset-password', 'Auth\NewPasswordController@store');
    //  Route::post('email/verification-notification', 'Auth\EmailVerificationNotificationController@store');
    //  Route::post('confirm-password', 'Auth\ConfirmablePasswordController@store');
});

Route::middleware('auth:api')->group(function () {

    Route::get('logout',[App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy']);
     // roles and permissions 
    Route::get('get-roles', [App\Http\Controllers\Admin\RolesPermissonController::class, 'userRoles']);
    Route::post('save-role-permissions', [App\Http\Controllers\Admin\RolesPermissonController::class,'SaveRolesPermission']);
    Route::post('update-role-permissions', [App\Http\Controllers\Admin\RolesPermissonController::class,'updateRolesPermission']);
    Route::get('edit-role-permissions/{id}', [App\Http\Controllers\Admin\RolesPermissonController::class,'editRolesPermission']);
    Route::get('add-role-permissions', [App\Http\Controllers\Admin\RolesPermissonController::class,'addRolesPermission']);


    Route::prefix('/settings')->group(function () {
    // fuel type
    Route::post('/fuel/{fuelId?}', [App\Http\Controllers\Admin\SettingsController::class,'addfuel']);
    Route::post('/delete-fuel', [App\Http\Controllers\Admin\SettingsController::class,'deletefuel']);
    Route::get('/fuel/details/{fuelId?}', [App\Http\Controllers\Admin\SettingsController:: class, 'fuelList']);
   
    //vehicle type
    Route::get('/vehicle-type/list/{vehicleTypeId?}',[App\Http\Controllers\Admin\SettingsController::class,'vehicleTypeList']);
    Route::post('/vehicle-type/{vehicleTypeId?}',[App\Http\Controllers\Admin\SettingsController::class,'addVehicleType']);
    Route::post('/delete-vehicle-type', [App\Http\Controllers\Admin\SettingsController::class,'deleteVehicleType']);

    //vehicle class
    Route::post('/vehicle-class/{vehicleClassId?}',[App\Http\Controllers\Admin\SettingsController::class,'addVehicleClass']);
    Route::post('/delete-vehicle-class', [App\Http\Controllers\Admin\SettingsController::class,'deleteVehicleClass']);
    Route::get('/vehicle-class/list/{vehicleclassId?}',[App\Http\Controllers\Admin\SettingsController::class,'vehicleClassList']);
    
    //document type 
    Route::post('/document-type/{documentTypeId?}',[App\Http\Controllers\Admin\SettingsController::class,'addDocumentType']);
    //discount card
    Route::post('/discount_cards/{discountCardId?}',[App\Http\Controllers\Admin\SettingsController::class,'discountCard']);
    });

    Route::prefix('/company')->group(function () {

    
    });
    


   
});




// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
