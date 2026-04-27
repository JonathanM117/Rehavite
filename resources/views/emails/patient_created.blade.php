<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Nuevo Paciente Registrado</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    
    <div style="text-align: center; margin-bottom: 20px;">
        <h2 style="color: #2c3e50;">Rehavit&eacute;</h2>
    </div>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef;">
        <h3 style="margin-top: 0; color: #1abc9c;">Nuevo Paciente Registrado</h3>
        <p>Se ha registrado un nuevo paciente en la plataforma de Rehavit&eacute;.</p>
        
        <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
            <tr>
                <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Nombre:</strong></td>
                <td style="padding: 8px 0; border-bottom: 1px solid #eee;">{{ $patient->full_name }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Expediente:</strong></td>
                <td style="padding: 8px 0; border-bottom: 1px solid #eee;">{{ $patient->expediente_id }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Fisioterapeuta Asignado:</strong></td>
                <td style="padding: 8px 0; border-bottom: 1px solid #eee;">{{ $patient->user->name ?? 'No asignado' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Tel&eacute;fono:</strong></td>
                <td style="padding: 8px 0; border-bottom: 1px solid #eee;">{{ $patient->phone ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; border-bottom: 1px solid #eee;"><strong>Diagn&oacute;stico Inicial:</strong></td>
                <td style="padding: 8px 0; border-bottom: 1px solid #eee;">{{ $patient->diagnosis ?? 'N/A' }}</td>
            </tr>
        </table>

        <div style="margin-top: 25px; text-align: center;">
            <a href="{{ route('admin.patients.show', $patient->id) }}" style="background: #1abc9c; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; font-weight: bold;">Ver Expediente</a>
        </div>
    </div>

    <div style="text-align: center; margin-top: 20px; font-size: 12px; color: #777;">
        <p>Este es un mensaje autom&aacute;tico del sistema Rehavit&eacute;. Por favor, no respondas a este correo.</p>
    </div>

</body>
</html>
