<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Banco de Ejercicios</title>
<style>
body{font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,sans-serif;background:#f6f8f8;color:#1d2426;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;text-align:center;padding:20px;}
.box{max-width:440px}
.ic{font-size:34px;margin-bottom:14px}
h1{font-size:19px;color:#0E6E6E;margin:0 0 10px}
p{color:#6b7578;font-size:14px;line-height:1.6;margin:0}
</style>
</head>
<body>
<div class="box">
    <div class="ic">🗂️</div>
    <h1>Banco de ejercicios no disponible</h1>
    @if($isAdmin ?? false)
        <p>Aún no se ha subido el archivo del banco de ejercicios. Un administrador debe subirlo desde <b>Centro de Control &rarr; Banco de Ejercicios</b>.</p>
    @else
        <p>Esta sección aún no está disponible. Solicita a un administrador que realice los cambios necesarios.</p>
    @endif
</div>
</body>
</html>
