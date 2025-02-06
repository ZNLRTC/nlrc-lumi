<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class ImportedNewAccountEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected string $email;
    protected array $data;

    /**
     * Create a new message instance.
     */
    public function __construct(string $email, array $data)
    {
        $this->email = $email;
        $this->data = $data;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('lumi@nlrc.ph', 'NLRC Lumi platform'),
            subject: 'Welcome to study Finnish!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.imported-new-account',
            with: [
                'email' => $this->email,
                'password' => $this->data['password'],
                'agency_name' => $this->data['agency'],
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
