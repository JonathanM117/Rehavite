<?php
// Storage Link Creator - BORRAR DESPUÉS DE USAR
$target = '/home3/rehavite/rehavite_app/storage/app/public';
$link   = '/home3/rehavite/public_html/storage';

echo "<pre style='background:#1a1a1a;color:#00ff00;padding:20px;font-family:monospace;'>";
echo "═══ STORAGE LINK SETUP ═══\n\n";

// Si ya existe el link o carpeta, reportar
if (is_link($link)) {
    echo "⚠ Ya existe un symlink en public_html/storage\n";
    echo "  Apunta a: " . readlink($link) . "\n";
    unlink($link);
    echo "  → Eliminado para recrear.\n\n";
} elseif (is_dir($link)) {
    echo "⚠ Existe una carpeta real (no symlink) en public_html/storage\n";
    echo "  Se mantendrá — bórrala manualmente si quieres el symlink.\n\n";
}

// Verificar que el target existe
if (!is_dir($target)) {
    echo "✗ ERROR: El directorio target no existe:\n  $target\n";
    echo "\nCreando estructura de storage...\n";
    @mkdir($target, 0755, true);
}

// Crear symlink
if (symlink($target, $link)) {
    echo "✓ Symlink creado exitosamente!\n";
    echo "  $link\n  → $target\n\n";
    echo "✓ Las imágenes de storage ahora son accesibles\n";
} else {
    echo "✗ No se pudo crear symlink (Hostgator puede impedirlo)\n\n";
    echo "ALTERNATIVA: Copiando archivos directamente...\n";
    // Fallback: copiar en lugar de symlink
    if (!is_dir($link)) {
        @mkdir($link, 0755, true);
        echo "✓ Carpeta storage/ creada en public_html\n";
        echo "  Deberás subir manualmente las imágenes a:\n";
        echo "  public_html/storage/imagenes/\n";
    }
}

echo "\n═══ BORRA ESTE ARCHIVO DEL SERVIDOR ═══\n";
echo "</pre>";
