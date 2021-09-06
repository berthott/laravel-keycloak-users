<?php

namespace berthott\KeycloakUsers\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewUserMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The new users plain password.
     */
    private $password;

    /**
     * The who sents the email.
     */
    private $user;

    /**
     * Create a new message instance.
     *
     * @param App\Models\Auth\User  $user
     * @param string                $password
     * @return void
     */
    public function __construct($user, $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('keycloak-users.mail.from.address'), config('keycloak-users.mail.from.name'))
                    ->subject(config('keycloak-users.mail.subject'))
                    ->markdown('keycloak-users::email.new_user')
                    ->with([
                        'user' => $this->user,
                        'password' => $this->password,
                        'loginLink' => config('keycloak-users.mail.link')
                    ]);
    }
}
