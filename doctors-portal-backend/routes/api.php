<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CaretendCredentialController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Carbon;
use App\Models\User;

use Illuminate\Support\Facades\Auth;
use Laravel\Passport\PersonalAccessTokenResult;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\ShipmentUpdateController;
use App\Http\Controllers\DashboardController;



Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail']);
Route::post('/reset-password', [AuthController::class, 'reset']);

Route::middleware(['auth:api', 'role:super_admin'])->group(function () {
    Route::get('users/admins', [UserController::class, 'admins']);
    Route::post('users/admins', [UserController::class, 'createAdmin']);
});

Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('user', [AuthController::class, 'user']);
    Route::put('user', [AuthController::class, 'update']);


    Route::get('users', [UserController::class, 'index']);
    Route::post('users', [UserController::class, 'store']);
    Route::get('users/{user}', [UserController::class, 'show']);
    Route::put('users/{user}', [UserController::class, 'update']);
    Route::delete('users/{user}', [UserController::class, 'destroy']);

    Route::get('/caretend-credential', [CaretendCredentialController::class, 'index']);
    Route::post('/caretend-credential', [CaretendCredentialController::class, 'store']);
    Route::put('/caretend-credential', [CaretendCredentialController::class, 'update']);
    Route::delete('/caretend-credential', [CaretendCredentialController::class, 'destroy']);
});



Route::post('/email/verification-notification', function (Request $request) {
    if ($request->user()->hasVerifiedEmail()) {
        return response()->json(['message' => 'Already verified']);
    }

    $request->user()->sendEmailVerificationNotification();

    return response()->json(['message' => 'Verification link sent']);
})->middleware(['auth:api', 'throttle:6,1'])->name('verification.send');


// Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
//     $user = User::find($id);

//     if (! $user) {
//         return response()->json(['message' => 'User not found'], 404);
//     }

//     if (! URL::hasValidSignature($request)) {
//         return response()->json(['message' => 'Invalid or expired verification link'], 403);
//     }

//     if ($user->hasVerifiedEmail()) {
//         return redirect('http://localhost:5173/email-verified'); // VUE ROUTE
//     }

//     if (! hash_equals((string) $hash, sha1($user->email))) {
//         return response()->json(['message' => 'Invalid hash'], 403);
//     }

//     $user->markEmailAsVerified();

//     return redirect('http://localhost:5173/email-verified'); // ✅ Replace with your VUE frontend route
// })->middleware('signed')->name('verification.verify');

Route::post('/verify-2fa', function (Request $request) {
    $request->validate([
        'user_id' => 'required|exists:users,id',
        'code' => 'required|string',
    ]);

    $user = User::find($request->user_id);

    if (
        !$user ||
        $user->two_factor_code !== $request->code ||
        now()->gt($user->two_factor_expires_at)
    ) {
        return response()->json(['message' => 'Invalid or expired code'], 401);
    }

    // Clear 2FA fields
    $user->two_factor_code = null;
    $user->two_factor_expires_at = null;
    $user->save();

    // Manually log in and issue a token
    Auth::login($user);
    $tokenResult = $user->createToken('LoginToken');

   
    return response()->json([
        'token' => $tokenResult->accessToken,
        'user' => $user,
        'role' => $user->roles->pluck('name')->first() // Just return the first role name
    ]);
});


Route::post('/resend-2fa', [AuthController::class, 'resend2fa']);

Route::middleware('auth:api')->group(function () {
    Route::get('prescriptions', [PrescriptionController::class, 'index']);
    Route::get('shipment-updates', [ShipmentUpdateController::class, 'index']);
    Route::post('shipment-updates/save', [ShipmentUpdateController::class, 'store']);
    Route::put('shipment-updates/{id}', [ShipmentUpdateController::class, 'update']);
    Route::post('/shipment-updates', [ShipmentUpdateController::class, 'create']);
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

});

Route::middleware('auth:api')->get('shipment-updates/doctor/{id}', function ($id, Request $request) {
    $user = auth()->user();

    if (!$user->hasAnyRole(['super_admin', 'admin'])) {
        return response()->json(['message' => 'Forbidden'], 403);
    }

    return app(\App\Http\Controllers\ShipmentUpdateController::class)->getByUser($id, $request);
});

Route::post('shipment-updates/import-from-sheet', [ShipmentUpdateController::class, 'importFromSheet']);
