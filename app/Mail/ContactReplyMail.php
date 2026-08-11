<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ContactMessage $contactMessage,
        public string $replySubject,
        public string $replyBody,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                (string) config('mail.from.address', 'noreply@heartlovepics.com'),
                (string) config('mail.from.name', 'HeartLovePics'),
            ),
            subject: $this->replySubject,
        );
    }

    public function content(): Content
    {
        return new Content(
            text: 'emails.contact-reply-text',
            html: 'emails.contact-reply',
        );
    }
}
