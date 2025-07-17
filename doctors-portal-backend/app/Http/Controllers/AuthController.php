<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;
use App\Mail\TwoFactorCodeMail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;



class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'is_admin' => false
        ]);

        return response()->json(['message' => 'Account created. Please verify your email.'], 201);
    }

    // public function login(Request $request)
    // {
    //     $request->validate([
    //         'email' => 'required|email',
    //         'password' => 'required',
    //     ]);

    //     $user = User::where('email', $request->email)->first();

    //     if (! $user || ! Hash::check($request->password, $user->password)) {
    //         throw ValidationException::withMessages([
    //             'email' => ['The provided credentials are incorrect.'],
    //         ]);
    //     }

    //     if (! $user->hasVerifiedEmail()) {
    //         return response()->json([
    //             'message' => 'Please verify your email address before logging in.'
    //         ], 403);
    //     }

    //     $token = $user->createToken('API Token')->accessToken;

    //     return response()->json([
    //         'token' => $token,
    //         'user' => [
    //             'name' => $user->name,
    //             'email' => $user->email,
    //             'is_admin' => $user->is_admin,
    //         ]
    //     ]);
    // }


    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (!auth()->attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = auth()->user();

        // generate and save code
        $user->two_factor_code = rand(100000, 999999);
        $user->two_factor_expires_at = now()->addMinutes(10);
        $user->save();

        // logout and send code
        auth()->logout();

        $htmlContent = (new TwoFactorCodeMail($user->two_factor_code))->render();
        $this->sendGraphMail($user->email, 'Your 2FA Code | DeliverIt Health', $htmlContent);

        return response()->json([
            'message' => 'Verification code sent.',
            'user_id' => $user->id,
            'two_factor_required' => true // ✅ Add this line
        ], 200);
    }

    public function resend2fa(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::find($request->user_id);

        // regenerate and extend the expiration
        $user->two_factor_code = rand(100000, 999999);
        $user->two_factor_expires_at = now()->addMinutes(10);
        $user->save();

        // send email
        $htmlContent = (new TwoFactorCodeMail($user->two_factor_code))->render();
        $this->sendGraphMail($user->email, 'Your 2FA Code | DeliverIt Health', $htmlContent);

        return response()->json([
            'message' => 'Verification code resent.'
        ]);
    }



    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json(['message' => 'Successfully logged out']);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $request->validate([
            'name' => 'sometimes|required',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|required|min:6',
        ]);

        $user->update($request->only(['name', 'email']) + [
            'password' => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        return response()->json($user);
    }

    public function sendResetLinkEmail(Request $request)
    {
        
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Reset link sent to your email.'])
            : response()->json(['message' => 'Unable to send reset link.'], 422);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        return $status == Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password has been reset.'])
            : response()->json(['message' => __($status)], 400);
    }

    private function getMicrosoftGraphAccessToken()
    {
        $client = new \GuzzleHttp\Client();

        $response = $client->post("https://login.microsoftonline.com/554f6b40-7694-4d23-852e-9d90a43fb525/oauth2/v2.0/token", [
            'form_params' => [
                'grant_type' => 'client_credentials',
                'client_id' => config('services.microsoft_mail.GRAPH_CLIENT_ID'),
                'client_secret' => config('services.microsoft_mail.GRAPH_CLIENT_SECRET'),
                'scope' => 'https://graph.microsoft.com/.default',
            ]
        ]);

        $data = json_decode($response->getBody(), true);
        return $data['access_token'];
    }

    private function sendGraphMail($toEmail, $subject, $content)
    {
        $accessToken = $this->getMicrosoftGraphAccessToken(); // assumes you have a working token generator

        $client = new \GuzzleHttp\Client();

        $response = $client->post('https://graph.microsoft.com/v1.0/users/ITdeliveritgroup@deliveritpharmacy.com/sendMail', [
            'headers' => [
                'Authorization' => "Bearer {$accessToken}",
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'message' => [
                    'subject' => $subject,
                    'body' => [
                        'contentType' => 'HTML',
                        'content' => $content,
                    ],
                    'toRecipients' => [
                        [
                            'emailAddress' => [
                                'address' => $toEmail,
                            ],
                        ],
                    ],
                ],
                'saveToSentItems' => 'true',
            ],
        ]);
    }


    
}
