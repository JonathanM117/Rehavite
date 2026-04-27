<!DOCTYPE html>
<html>
<head>
    <title>Bienvenido a Rehavite</title>
</head>
<body>
    <h3>Estimado/a {{ $user->name }},</h3>

    <p>Nos complace informarte que tu registro en <strong>Rehavite</strong> ha sido completado con éxito. Ahora puedes acceder a todas nuestras funciones y continuar con tus actividades.</p>

    <h4>Detalles de tu cuenta:</h4>
    <ul>
        <li><strong>Correo electrónico:</strong> {{ $user->email }}</li>
        <li><strong>Contraseña:</strong> {{ $user->password }}</li>
        <li><strong>Fecha de creación de la cuenta:</strong> {{ $user->created_at }}</li>
    </ul>

    <p>Te recomendamos que cambies tu contraseña después de acceder por primera vez para mayor seguridad.</p>

    <p>Si tienes alguna pregunta o necesitas asistencia, no dudes en contactarnos.</p>

    <p>Atentamente,</p>
    <p>El equipo de <strong>Rehavite</strong></p>
</body>
</html>
