@component('mail::message')
# Invitacion de Registro

Te invitamos a formar parte de nuestra aplicacion.

@component('mail::button', ['url' => 'http://localhost/register'])
    Registrarte
@endcomponent

Gracias por usar nuestra aplicacion, <br>
Tauro Gas
@endcomponent