<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use GuzzleHttp\Client;
use App\Mail\WelcomeMail;
use Illuminate\Support\Facades\DB; // <-- add this at the top of UserController


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

         // If you also want a welcome email via Graph:
            $features = [
                    'Track prescription status in real time',
                ];

                $welcome = new \App\Mail\WelcomeMail(
                    $user->name,
                    'https://deliveritpharmacy.com/wp-content/uploads/2024/04/DeliverIt-Hero.jpg',
                    'https://portal.deliveritgroup.us/admin', // admin landing (adjust if needed)
                    $features,
                    false // isAdmin
                );
            // Render Blade -> HTML string:
            $htmlContent = $welcome->render();

            // Send via Graph with HTML:
            $this->sendGraphEmail(
                $user->email,
                'Welcome to DeliverIt Portal | DeliverIt Health',
                $htmlContent
            );
        // $user->sendEmailVerificationNotification();

        return response()->json($user, 201);
    }


    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            $user->assignRole('doctor');

            // If you want verification, uncomment:
            // $user->sendEmailVerificationNotification();

            // If you also want a welcome email via Graph:
            $features = [
                    'Track prescription status in real time',
                ];

                $welcome = new \App\Mail\WelcomeMail(
                    $user->name,
                    'https://deliveritpharmacy.com/wp-content/uploads/2024/04/DeliverIt-Hero.jpg',
                    'https://portal.deliveritgroup.us/admin', // admin landing (adjust if needed)
                    $features,
                    false // isAdmin
                );
            // Render Blade -> HTML string:
            $htmlContent = $welcome->render();

            // Send via Graph with HTML:
            $this->sendGraphEmail(
                $user->email,
                'Welcome to DeliverIt Portal | DeliverIt Health',
                $htmlContent
            );

            return $user;
        });

        // If you DID send a verification email, keep this:
        return response()->json(['message' => 'Account created. Please verify your email.'], 201);

        // If you did NOT send verification (and only sent welcome), return success instead:
        // return response()->json(['success' => true], 201);
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

        $user = null;

        DB::transaction(function () use ($request, &$user) {
            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole('admin');
        });

        DB::afterCommit(function () use ($user) {
            try {
                $features = [
                    'Create new users',
                    'Edit prescriptions',
                    'Admin settings',
                ];

                $welcome = new \App\Mail\WelcomeMail(
                    $user->name,
                    'https://deliveritpharmacy.com/wp-content/uploads/2024/04/DeliverIt-Hero.jpg',
                    'https://portal.deliveritgroup.us/admin', // admin landing (adjust if needed)
                    $features,
                    true // isAdmin
                );

                $html = $welcome->render();

                $this->sendGraphEmail(
                    $user->email,
                    'Welcome Admin — DeliverIt Portal',
                    $html
                );
            } catch (\Throwable $e) {
                \Log::error('Admin welcome email failed', ['message' => $e->getMessage()]);
            }
        });

        return response()->json([
            'message' => 'Admin user created successfully',
            'user'    => $user
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

        try {
            $response = $client->post('https://graph.microsoft.com/v1.0/users/ITdeliveritgroup@deliveritpharmacy.com/sendMail', [
                'headers' => [
                    'Authorization' => "Bearer $accessToken",
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'message' => [
                        'subject' => $subject,
                        'body' => [
                            'contentType' => 'HTML',   // ✅ was "Text"
                            'content'     => $body,
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

            \Log::info('Graph sendMail OK', [
                'status' => $response->getStatusCode(),
                'body'   => (string) $response->getBody()
            ]);
        } catch (\Throwable $e) {
            \Log::error('Graph sendMail failed', [
                'message' => $e->getMessage(),
            ]);
            throw $e; // rethrow if you want to fail the request
        }
    }




}
