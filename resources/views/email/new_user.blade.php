@component('mail::message')
# Hallo {{ $user->firstName }} {{ $user->lastName }},

Für Sie wurde ein Nutzer-Konto für {{ config('keycloak-users.mail.from.name') }} erstellt. Sie können sich nun mit ihrer E-Mail-Adresse anmelden.
Ihr automatisch generiertes Passwort lautet:

{{ $password }}

Sie werden beim ersten Login aufgefordert ein neues Passwort zu wählen.

@component('mail::button', ['url' => $loginLink])
Login
@endcomponent

Mit freundlichen Grüßen,<br>
{{ config('keycloak-users.mail.from.name') }}
@endcomponent