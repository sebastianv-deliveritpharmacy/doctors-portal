@component('mail::message')
# Hello {{ $user->name }}

Thanks for signing up with **DeliverIt Health**! Please confirm your email address to activate your account and access the portal.

@component('mail::button', ['url' => $verificationUrl])
Verify Email
@endcomponent

If you did not create this account, you can safely ignore this email.

Thanks,<br>
**DeliverIt Health Team**
@endcomponent
