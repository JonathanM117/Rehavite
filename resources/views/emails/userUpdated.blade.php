<!DOCTYPE html>
<html>
<head>
    <title>Actualización de cuenta en Rehavité</title>
</head>
<body>
    <h3>Estimado/a {{ $user->name }},</h3>

    <p>Te informamos que los datos de tu cuenta en <strong>Rehavite</strong> han sido actualizados correctamente.</p>

    <h4>Información actualizada:</h4>
    <ul>
        <li><strong>Nombre completo:</strong> {{ $user->name }}</li>
        <li><strong>Correo electrónico:</strong> {{ $user->email }}</li>
        <li><strong>Contraseña:</strong> {{ $user->password }}</li>
        <li><strong>Última actualización:</strong> {{ $user->updated_at->format('d/m/Y H:i') }}</li>
    </ul>

    <p>Si tú no realizaste este cambio, por favor contáctanos de inmediato.</p>

    <p>Gracias por seguir confiando en <strong>Rehavité</strong>.</p>

    <p>Atentamente,</p>
    <p>El equipo de <strong>Rehavité</strong></p>
</body>
</html>
