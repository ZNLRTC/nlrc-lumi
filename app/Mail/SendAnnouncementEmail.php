<?php

namespace App\Mail;

use App\Models\Announcement;
use App\Models\Trainee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendAnnouncementEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected Announcement $announcement;
    protected Trainee $trainee;

    /**
     * Create a new message instance.
     */
    public function __construct(Announcement $announcement, Trainee $trainee)
    {
        $this->announcement = $announcement;
        $this->trainee = $trainee;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS', 'lumi@nlrc.ph'), env('MAIL_FROM_NAME', 'NLRC Lumi platform')),
            subject: 'NLRC-Lumi Announcement'
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.send-announcement',
            with: [
                'announcement' => $this->announcement,
                'trainee' => $this->trainee
            ]
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
