<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\URL;
use Spatie\Permission\Traits\HasRoles;

use App\Mail\VerifyEmailCustom;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\SoftDeletes;
use GuzzleHttp\Client;


class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'sheet_identifier'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected $guard_name = 'api';



    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function caretendCredential()
    {
        return $this->hasOne(CaretendCredential::class);
    }

    private function getGraphAccessToken()
    {
        $client = new Client();

        $response = $client->post("https://login.microsoftonline.com/554f6b40-7694-4d23-852e-9d90a43fb525/oauth2/v2.0/token", [
            'form_params' => [
                'grant_type' => 'client_credentials',
                'client_id' => env('GRAPH_CLIENT_ID'),
                'client_secret' => env('GRAPH_CLIENT_SECRET'),
                'scope' => 'https://graph.microsoft.com/.default',
            ]
        ]);

        return json_decode($response->getBody()->getContents(), true)['access_token'];
    }


    public function sendEmailVerificationNotification()
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $this->id, 'hash' => sha1($this->email)]
        );

        // Render Blade view to HTML
        $htmlContent = View::make('emails.verify', [
            'user' => $this,
            'verificationUrl' => $verificationUrl,
        ])->render();

        $accessToken = $this->getGraphAccessToken();

        $client = new Client();

        $client->post('https://graph.microsoft.com/v1.0/users/ITdeliveritgroup@deliveritpharmacy.com/sendMail', [
            'headers' => [
                'Authorization' => "Bearer $accessToken",
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'message' => [
                    'subject' => 'Verify Your Email Address',
                    'body' => [
                        'contentType' => 'HTML',
                        'content' => $htmlContent,
                    ],
                    'toRecipients' => [
                        [
                            'emailAddress' => [
                                'address' => $this->email,
                            ],
                        ],
                    ],
                ],
                'saveToSentItems' => true,
            ],
        ]);
    }

    public function shipmentUpdates()
    {
        return $this->hasMany(ShipmentUpdate::class, 'user_id');
    }


}
