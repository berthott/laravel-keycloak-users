<?php

namespace berthott\KeycloakUsers\Mail;

use berthott\KeycloakUsers\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewUserMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * The new users plain password.
     */
    private string $password;

    /**
     * The who sents the email.
     */
    private User $user;

    /**
     * Create a new message instance.
     */
    public function __construct(User $user, string $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->from(config('keycloak-users.mail.from.address'), config('keycloak-users.mail.from.name'))
                    ->subject(config('keycloak-users.mail.subject'))
                    ->markdown('keycloak-users::email.new_user')
                    ->with([
                        'user' => $this->user,
                        'password' => $this->password,
                        'loginLink' => config('keycloak-users.mail.link'),
                    ]);
    }
}
