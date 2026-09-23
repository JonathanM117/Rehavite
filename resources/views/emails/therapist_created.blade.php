<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bienvenido a Rehavité</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            border: 1px solid #e2e8f0;
        }
        .header {
            background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
            padding: 40px 20px;
            text-align: center;
            color: white;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            font-weight: 700;
        }
        .content {
            padding: 40px 30px;
            line-height: 1.6;
        }
        .content p {
            margin-bottom: 20px;
        }
        .credentials-card {
            background-color: #f1f5f9;
            border-radius: 8px;
            padding: 20px;
            margin: 24px 0;
            border-left: 4px solid #14b8a6;
        }
        .credentials-card div {
            margin-bottom: 8px;
        }
        .credentials-card div:last-child {
            margin-bottom: 0;
        }
        .btn-container {
            text-align: center;
            margin-top: 30px;
        }
        .btn {
            background-color: #14b8a6;
            color: white !important;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            display: inline-block;
            box-shadow: 0 4px 6px -1px rgba(20, 184, 166, 0.2);
        }
        .footer {
            background-color: #f8fafc;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>¡Bienvenido a Rehavité!</h1>
        </div>
        <div class="content">
            <p>Hola <strong>{{ $name }}</strong>,</p>
            <p>Tu cuenta como Fisioterapeuta en la plataforma Rehavité ha sido creada exitosamente. A continuación se presentan tus credenciales para acceder al sistema:</p>
            
            <div class="credentials-card">
                <div><strong>Enlace de acceso:</strong> <a href="{{ $loginUrl }}" style="color: #0d9488;">{{ $loginUrl }}</a></div>
                <div><strong>Correo electrónico:</strong> {{ $email }}</div>
                <div><strong>Contraseña temporal:</strong> <span style="font-family: monospace; font-size: 14px; background: #e2e8f0; padding: 2px 6px; border-radius: 4px;">{{ $password }}</span></div>
            </div>

            <p>Te recomendamos cambiar tu contraseña una vez que inicies sesión en tu perfil por motivos de seguridad.</p>

            <div class="btn-container">
                <a href="{{ $loginUrl }}" class="btn">Iniciar Sesión</a>
            </div>
        </div>
        <div class="footer">
            Este es un correo automático. Por favor no respondas a este mensaje.<br>
            &copy; {{ date('Y') }} Rehavité. Todos los derechos reservados.
        </div>
    </div>
</body>
</html>
