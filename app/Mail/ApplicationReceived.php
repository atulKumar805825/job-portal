<?php
namespace App\Mail;

use App\Models\Application;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\{Content, Envelope};

// Sent to the candidate after applying
class ApplicationReceived extends Mailable
{
    public function __construct(public Application $application) {}
    public function envelope(): Envelope {
        return new Envelope(subject: 'Application received: ' . $this->application->job->title);
    }
    public function content(): Content { return new Content(view: 'emails.applied'); }
}
