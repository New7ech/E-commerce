<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomPasswordResetLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $token;
    public string $email;
    public string $resetUrl;

    /**
     * Create a new message instance.
     *
     * @param string $email The user's email address.
     * @param string $token The password reset token.
     */
    public function __construct(string $email, string $token)
    {
        $this->email = $email;
        $this->token = $token;
        // Note: The route 'custom.password.reset' is not yet defined in this subtask.
        // It will be defined in the next subtask (Part 3: Resetting the Password).
        // For now, we construct the URL knowing its future parameters.
        $this->resetUrl = route('custom.password.reset', [
            'token' => $this->token,
            'email' => $this->email
        ]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Your Password',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.custom-password-reset-link',
            with: [
                'resetUrl' => $this->resetUrl,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
