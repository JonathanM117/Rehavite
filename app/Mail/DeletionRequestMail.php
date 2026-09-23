<?php

namespace App\Mail;

use App\Models\Patient;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class DeletionRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $confirmUrl;
    public string $rejectUrl;
    public string $archiveUrl;

    public function __construct(
        public Patient $patient,
        public User $requestedBy
    ) {
        // Signed URLs valid for 7 days
        $this->confirmUrl = URL::signedRoute('admin.patients.deletion.confirm', ['patient' => $patient->id], now()->addDays(7));
        $this->rejectUrl  = URL::signedRoute('admin.patients.deletion.reject',  ['patient' => $patient->id], now()->addDays(7));
        $this->archiveUrl = URL::signedRoute('admin.patients.deletion.archive', ['patient' => $patient->id], now()->addDays(7));
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🗑 Solicitud de eliminación: ' . $this->patient->full_name,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.deletion_request',
        );
    }
}
