@component('mail::message')
<p>Hello {{ $user->name }},</p>
<p>Please verify your account using the button below.</p>

@component('mail::button', ['url' => route('verification.token.validate', $user->verification_token)])
Verify account
@endcomponent

<p>Regards,<br>{{ config('app.name') }}</p>

<div style="border-top: 1px solid #faf2e6; margin-top: 25px; margin-bottom: 25px;"></div> 

<p>If you're having trouble with the "Verify account" button, copy and paste the following URL into your web browser: {{ route('verification.token.validate', $user->verification_token) }}</p>
@endcomponent