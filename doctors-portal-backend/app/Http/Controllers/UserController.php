<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use GuzzleHttp\Client;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::role('doctor')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'sheet_identifier' => 'nullable|string|max:255',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'sheet_identifier' => $request->sheet_identifier,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('doctor');
        $user->sendEmailVerificationNotification();

        return response()->json($user, 201);
    }

    public function show(User $user)
    {
        return response()->json($user,  200);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|nullable|min:6',
            'sheet_identifier' => 'nullable|string|max:255',
        ]);

        $user->update([
            'name' => $request->name ?? $user->name,
            'email' => $request->email ?? $user->email,
            'sheet_identifier' => $request->sheet_identifier ?? $user->sheet_identifier,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
        ]);

        return response()->json($user);
    }


    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(null, 204);
    }


   public function admins()
   {
        return response()->json(User::role('admin')->get());
   }

    public function createAdmin(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign the 'admin' role to the user with the correct guard
        $user->assignRole('admin');

        return response()->json([
            'message' => 'Admin user created successfully',
            'user' => $user
        ], 201);
    }

   private function getGraphAccessToken()
    {
        $client = new Client();

        $response = $client->post("https://login.microsoftonline.com/554f6b40-7694-4d23-852e-9d90a43fb525/oauth2/v2.0/token", [
            'form_params' => [
                'grant_type' => 'client_credentials',
                'client_id' => config('services.microsoft_mail.GRAPH_CLIENT_ID'),
                'client_secret' => config('services.microsoft_mail.GRAPH_CLIENT_SECRET'),
                'scope' => 'https://graph.microsoft.com/.default',
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true)['access_token'];
    }


    private function sendGraphEmail($toEmail, $subject, $body)
    {
        $accessToken = $this->getGraphAccessToken();

        $client = new Client();

        $client->post('https://graph.microsoft.com/v1.0/users/ITdeliveritgroup@deliveritpharmacy.com/sendMail', [
            'headers' => [
                'Authorization' => "Bearer $accessToken",
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'message' => [
                    'subject' => $subject,
                    'body' => [
                        'contentType' => 'Text',
                        'content' => $body,
                    ],
                    'toRecipients' => [
                        [
                            'emailAddress' => [
                                'address' => $toEmail,
                            ],
                        ],
                    ],
                ],
                'saveToSentItems' => true,
            ],
        ]);
    }



}
