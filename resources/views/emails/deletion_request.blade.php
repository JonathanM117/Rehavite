<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Solicitud de Eliminación</title>
<style>
    body { font-family: 'Segoe UI', Arial, sans-serif; background:#f0f4f8; margin:0; padding:20px; }
    .container { max-width:600px; margin:0 auto; background:#fff; border-radius:12px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,0.08); }
    .header { background:#0f3460; padding:28px 32px; text-align:center; }
    .header h1 { color:#fff; margin:0; font-size:22px; }
    .header p { color:rgba(255,255,255,0.75); margin:6px 0 0; font-size:14px; }
    .body { padding:28px 32px; }
    .alert { background:#fff3cd; border-left:4px solid #f59e0b; padding:14px 18px; border-radius:6px; margin-bottom:24px; }
    .alert p { margin:0; font-size:14px; color:#92400e; }
    .patient-card { background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:18px; margin-bottom:24px; }
    .patient-card h3 { margin:0 0 10px; color:#0f3460; font-size:16px; }
    .patient-card p { margin:4px 0; font-size:13px; color:#555; }
    .patient-card strong { color:#1a1a2e; }
    .actions { text-align:center; margin:24px 0; }
    .btn { display:inline-block; padding:13px 24px; border-radius:8px; text-decoration:none; font-size:14px; font-weight:600; margin:6px; }
    .btn-danger  { background:#ef4444; color:#fff; }
    .btn-success { background:#10b981; color:#fff; }
    .btn-blue    { background:#3b82f6; color:#fff; }
    .footer { background:#f8fafc; padding:18px 32px; text-align:center; font-size:12px; color:#94a3b8; border-top:1px solid #e2e8f0; }
    .expiry { font-size:12px; color:#94a3b8; margin-top:16px; text-align:center; }
</style>
</head>
<body>
<div class="container">

    <div class="header">
        <h1>🗑 Solicitud de Eliminación de Paciente</h1>
        <p>Rehavité — Sistema de Gestión Clínica</p>
    </div>

    <div class="body">
        <div class="alert">
            <p>
                <strong>{{ $requestedBy->name }}</strong> ha solicitado la eliminación del siguiente paciente.
                El expediente ha sido suspendido temporalmente hasta su decisión.
            </p>
        </div>

        <div class="patient-card">
            <h3>{{ $patient->full_name }}</h3>
            <p><strong>Expediente:</strong> {{ $patient->expediente_id }}</p>
            <p><strong>Diagnóstico:</strong> {{ $patient->diagnosis ?? '—' }}</p>
            <p><strong>Fisioterapeuta:</strong> {{ $patient->user?->name ?? '—' }}</p>
            <p><strong>Registro:</strong> {{ $patient->created_at->format('d/m/Y') }}</p>
            <p><strong>Solicitud:</strong> {{ now()->format('d/m/Y H:i') }} hrs</p>
        </div>

        <p style="font-size:14px; color:#555; text-align:center; margin-bottom:20px;">
            Por favor seleccione una acción para este paciente:
        </p>

        <div class="actions">
            <a href="{{ $confirmUrl }}" class="btn btn-danger">
                🗑 Confirmar Eliminación
            </a>
            <a href="{{ $rejectUrl }}" class="btn btn-success">
                ✅ Rechazar Solicitud
            </a>
            <a href="{{ $archiveUrl }}" class="btn btn-blue">
                📦 Archivar Paciente
            </a>
        </div>

        <p class="expiry">
            ⚠ Estos enlaces son válidos por <strong>7 días</strong> a partir de esta solicitud.<br>
            Después deberá gestionar el paciente directamente desde el sistema.
        </p>
    </div>

    <div class="footer">
        Rehavité · Sistema de Gestión Clínica · {{ now()->format('Y') }}<br>
        Si no reconoce esta solicitud, puede ignorar este correo.
    </div>

</div>
</body>
</html>
