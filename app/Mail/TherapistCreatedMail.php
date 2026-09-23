<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TherapistCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email;
    public $password;
    public $loginUrl;

    public function __construct(string $name, string $email, string $password)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->loginUrl = route('login');
    }

    public function build()
    {
        return $this->subject('Bienvenido a Rehavité - Credenciales de Acceso')
                    ->view('emails.therapist_created');
    }
}
