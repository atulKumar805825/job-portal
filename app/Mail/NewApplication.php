<?php
namespace App\Mail;

use App\Models\Application;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\{Content, Envelope};

// Sent to admin(s) when someone applies
class NewApplication extends Mailable
{
    public function __construct(public Application $application) {}
    public function envelope(): Envelope {
        return new Envelope(subject: 'New application for ' . $this->application->job->title);
    }
    public function content(): Content { return new Content(view: 'emails.new-application'); }
}
