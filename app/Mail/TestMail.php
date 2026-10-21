<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class TestMail extends Mailable
{
    public function __construct(
        public string $recipientName = 'User',
        public string $recipientEmail = ''
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'MyBlog SMTP Test Email');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.test');
    }
}