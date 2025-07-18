@component('mail::message')
# Welcome {{ $user->name }}!

Thank you for signing up with **DeliverIt Portal** — we're excited to have you on board!

You can now explore your personalized portal and learn more about the services we offer.

@component('mail::button', ['url' => 'https://portal.deliveritgroup.us/'])
Visit DeliverIt Portal
@endcomponent

If you did not create this account, you can safely ignore this email.

Thanks,<br>
**The DeliverIt Portal Team**
@endcomponent
