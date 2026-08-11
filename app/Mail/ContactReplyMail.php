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
        $fromAddress = (string) config('mail.from.address', 'noreply@heartlovepics.com');
        $fromName = (string) config('mail.from.name', 'HeartLovePics');

        return new Envelope(
            from: new Address($fromAddress, $fromName),
            subject: $this->replySubject,
            using: [
                function ($message) use ($fromAddress, $fromName) {
                    // Force envelope sender / Return-Path for native sendmail.
                    $message->sender($fromAddress, $fromName);
                    $message->returnPath($fromAddress);
                },
            ],
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
