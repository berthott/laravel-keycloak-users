<?php

namespace berthott\KeycloakUsers\Mail;

use berthott\KeycloakUsers\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewUserMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public ?string $loginLink;

    /**
     * Create a new message instance.
     */
    public function __construct(public User $user, public string $password) {
        $this->loginLink = config('keycloak-users.mail.link');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('keycloak-users.mail.from.address'), config('keycloak-users.mail.from.name')),
            replyTo: [
                new Address(config('keycloak-users.mail.replyTo.address'), config('keycloak-users.mail.replyTo.name')),
            ],
            subject: config('keycloak-users.mail.subject'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'keycloak-users::email.new_user',
        );
    }
}
