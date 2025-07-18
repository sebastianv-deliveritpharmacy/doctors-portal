<?php

use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use App\Models\User;

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = User::find($id);

    if (! $user) {
        return response()->json(['message' => 'User not found.'], 404);
    }

    if (! URL::hasValidSignature($request)) {
        return response()->json(['message' => 'Invalid or expired verification link.'], 403);
    }

    if (! hash_equals((string) $hash, sha1($user->email))) {
        return response()->json(['message' => 'Hash mismatch.'], 403);
    }

    if ($user->hasVerifiedEmail()) {
        return redirect('https://portal.deliveritgroup.us/email-verified');
    }

    $user->markEmailAsVerified();

    return redirect('https://portal.deliveritgroup.us/email-verified');
})->middleware('signed')->name('verification.verify');


Route::get('/reset-password/{token}', function ($token) {
    return redirect()->away("https://portal.deliveritgroup.us/reset-password/$token");
})->middleware('guest')->name('password.reset');

