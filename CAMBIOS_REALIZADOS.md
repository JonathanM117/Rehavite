# Seguimiento de cambios — Rehavite

> Generado durante la sesión de trabajo. Cada punto de las listas originales se marca como hecho (✅) o ya existente previamente (☑️, sin cambios necesarios). Al final hay un listado de carpetas/archivos modificados, en el formato solicitado, para subir manualmente al servidor.

## Decisiones tomadas (confirmadas con el cliente)

1. **"Consultas" → "Episodio Clínico"**: se usa "Episodio Clínico" en tabs/listados y "Detalle del Episodio" como título de la vista de detalle. El campo "Motivo de Consulta" (dentro del episodio) no se tocó porque ya existía con ese nombre.
2. **Módulo de Pagos**: se creó una página global `/panel/pagos` con el listado de TODOS los pagos de todos los pacientes, con filtro por paciente y fisioterapeuta, y botón para registrar un pago nuevo. Se eliminó el tab "Pagos" del expediente del paciente.
3. **Filtros del dashboard**: se quitaron "Todos los fisios", "Todos los estados" y "Fecha de nacimiento" de la lista de Pacientes y se agregaron como controles en el Dashboard, que redirigen a la lista de pacientes ya filtrada (reutilizando el query string que la lista ya soportaba).
4. **Relación Documentos-Episodios**: se preparó la relación muchos-a-muchos entre Documentos Clínicos y Episodios (tabla pivote `clinical_document_consultation` + tab "Documentos" dentro del episodio para vincular/desvincular documentos del repositorio). NO se tocó "Estudios" ni "Fotos Clínicas" (siguen siendo módulos separados con archivo propio); ese es un cambio de arquitectura mayor que queda fuera de esta pasada — quedó anotado como pendiente futuro más abajo.
5. **Correo en lista de pacientes**: se eliminó de la vista principal de Pacientes (poco utilizado). Sigue disponible dentro del expediente/edición del paciente.

---

## GESTIÓN CLÍNICA (columna lateral izquierda)

- [x] Cambiar "Agenda y citas" por "Agenda"
- [x] Agregar módulo de Pagos en el sidebar y quitarlo del expediente del paciente

## PANTALLA DE INICIO > PACIENTES

- [x] Eliminar primera columna de expediente (#)
- [x] Poner el número de expediente debajo del nombre del paciente
- [x] Cambiar "Diagnóstico" por "Padecimiento"
- [x] Cambiar columna "Teléfono" por "Contacto"
- [x] Quitar el e-mail de la vista principal (decisión: poco utilizado)
- [x] Columna "Acciones": solo botón Ver / clic en el nombre. Archivar/eliminar solo dentro del expediente
- [x] Botones "Archivados" y "Papelera" al final de la lista, esquina inferior derecha
- [x] Filtros "Todos los fisios", "Todos los estados" y "Fecha de nacimiento" solo visibles en Dashboard

## "Archivos del paciente" → "Documentos Clínicos"

- [x] Renombrar "Archivos del Paciente" a "Documentos Clínicos"
- [x] Tipos de documento: Consentimiento informado, Informe médico, Receta médica, Interconsulta / Referencia, Incapacidad médica, Documento administrativo, Otro
- [x] Placeholder de descripción de ejemplo ("Ej. Receta médica del Dr. Juan Pérez para tratamiento de dolor lumbar")
- [x] Cambiar "Archivo" por "Documento*"
- [x] Ayuda: "PDF, JPG, PNG (máx. 20 MB)" + validación acorde (mimes pdf/jpg/jpeg/png, máx 20480 KB)
- [x] Eliminar botón "Cancelar" de la esquina superior derecha (el toggle "Subir Documento ↔ Cancelar" del header ya no aparece mientras el formulario está abierto; solo queda el botón "Cancelar" del propio formulario)
- [x] Filtro por tipo de documento
- [x] Formato de fecha "12 Jul 2026"
- [x] Estado del documento (🟢 Vigente / 🟡 Actualizar / ⚪ Histórico) automático por tipo, con reglas por tipo de documento, + checkbox "Gestionar estado manualmente" en el formulario (crear/editar)
- [x] ☑️ Botón eliminar en Notas de Evolución — ya existía en el código, no requirió cambios

## "Detalle de Consulta" → "Detalle del Episodio"

- [x] Renombrar vista y tab "Consultas" → "Episodio Clínico"
- [x] Preparar relación Documentos ↔ Episodios (repositorio maestro, tab "Documentos" dentro del episodio para vincular/desvincular documentos existentes del paciente)

## Expediente clínico (vista del paciente)

- [x] Eliminar módulo de pagos del expediente
- [x] Menú de tres líneas: "Archivar/Eliminar" (admin) / "Solicitar eliminación" (fisio)
- [x] "Historial" solo visible para administrador
- [x] Eliminar el estado del paciente de la vista general (header)

## Pacientes > Consultas

- [x] Ver decisión #1 (unificado con "Episodio Clínico")

## Ronda 2 — Ajustes sobre lo ya construido (a partir de capturas anotadas)

- [x] En "Detalle del Episodio", las pestañas "Documentos", "Ejercicios", "Escalas Clínicas" y "Fotos Clínicas" ahora viven dentro de un menú desplegable ("Más ▾"), en ese orden. Las pestañas "Información Principal", "Notas de Evolución" y "Estudios" quedan visibles como antes.
- [x] Se quitaron los íconos de la barra de pestañas superior del episodio (solo texto).
- [x] En el expediente del paciente, los botones "PDF" y "Excel" se unificaron en un solo botón "Exportar ▾" (solo visible para administrador).
- [x] En la lista de Episodios del expediente: se agrupan por año de creación (2026, 2025, …) y cada tarjeta ahora muestra el número de sesiones (notas de evolución) y la fecha de la última sesión, además del diagnóstico/motivo y estado que ya existían.
- [x] Corregido el botón "Vincular" en la pestaña Documentos del episodio: el `<select>` no avisaba al servidor cuál documento se había elegido sino hasta la siguiente acción, así que el botón quedaba deshabilitado y el clic no hacía nada. Ahora el select notifica el cambio de inmediato.

---

## Pendiente / fuera de alcance de esta pasada

- **Refactor de "Estudios" y "Fotos Clínicas"** para que dejen de tener archivo propio y en su lugar muestren documentos del repositorio vinculados al episodio (como describen las "Notas/Funciones" del pedido original). Es un cambio de arquitectura mayor (migración de datos existentes incluida) que se dejó fuera de esta pasada por decisión conjunta; la relación documento↔episodio ya quedó preparada para soportarlo cuando se decida abordarlo.

## Verificación realizada

Se levantó el proyecto localmente (XAMPP + `php artisan serve`), se corrieron las 2 migraciones nuevas contra la base de datos local, y se probó en navegador: sidebar, dashboard con filtros, lista de pacientes (columnas, acciones, botones inferiores), expediente del paciente (menú de tres líneas, tabs renombrados, sin tab de Pagos, sin estado en el header), subida de documentos (tipos, placeholder, ayuda de tamaño, filtro, formato de fecha, estados automáticos con emoji), episodio clínico (título renombrado, tab "Documentos" vinculando el repositorio) y el nuevo módulo global de Pagos.

Durante la verificación se encontraron y corrigieron 2 errores introducidos en el proceso de edición (no relacionados con la lista de pedidos, pero necesarios para que todo funcionara):
- Un BOM (marca de codificación UTF-8) quedó al inicio de `patient-detail.blade.php` y `patient-index.blade.php` tras una edición por script, lo que rompía la detección del componente Livewire y dejaba los tabs sin funcionar. Ya corregido.
- Un `x-data` de Alpine.js mal alcanzado en el formulario de subir documento (bug preexistente en el archivo original) generaba errores de consola. Ya corregido de paso.

## Ronda 3 — Corrección visual del menú "Más"

- [x] El menú desplegable "Más" (Documentos/Ejercicios/Escalas/Fotos) del episodio se abría pero quedaba tapado visualmente por la tarjeta de contenido de abajo (problema de orden de apilamiento / z-index, no de que el menú no funcionara). Se corrigió dándole posición y z-index propios al contenedor de la barra de tabs; se aplicó el mismo ajuste preventivo al header del expediente del paciente (menú de tres líneas y "Exportar"), que usa el mismo patrón.

---

## Archivos modificados (para subir al servidor)

routes
-admin.php

app/Http/Controllers/Admin
-PaymentController.php
-HomeController.php

app/Http/Livewire
-PatientDetail.php
-ClinicalDocuments.php
-ConsultationDetail.php

app/Models
-ClinicalDocument.php
-Consultation.php

database/migrations
-2026_09_07_000001_add_status_fields_to_clinical_documents_table.php (nuevo)
-2026_09_07_000002_create_clinical_document_consultation_table.php (nuevo)

resources/views/layouts
-admin.blade.php

resources/views/admin
-index.blade.php
-payments (carpeta nueva)
 *index.blade.php (nuevo)

resources/views/livewire
-patient-detail.blade.php
-patient-index.blade.php
-clinical-documents.blade.php
-consultation-detail.blade.php

(raíz del proyecto)
-CAMBIOS_REALIZADOS.md (nuevo — este archivo de seguimiento, no es necesario subirlo al servidor, es solo documentación interna)

**Importante al subir:** después de subir `database/migrations/`, correr en el servidor:
```
php artisan migrate
```
para crear las columnas de estado de documentos y la tabla pivote de documentos-episodios.

---

## Archivos modificados SOLO en la Ronda 2 y 3 (los últimos 2 pedidos)

> Estos dos archivos ya estaban en el listado general de arriba (fueron tocados desde la Ronda 1), pero recibieron cambios adicionales en estas dos últimas peticiones. Si ya subiste la Ronda 1, con volver a subir estos dos archivos ya quedas al día — no hace falta tocar nada más.

resources/views/livewire
-consultation-detail.blade.php
-patient-detail.blade.php

**Qué cambió en cada uno:**
- `consultation-detail.blade.php`: menú "Más" (Documentos/Ejercicios/Escalas Clínicas/Fotos Clínicas) en lugar de pestañas sueltas, sin íconos en la barra de tabs, corrección del botón "Vincular" en Documentos, y corrección del z-index para que el menú "Más" no quede tapado.
- `patient-detail.blade.php`: botón "Exportar" (unifica PDF/Excel), tarjetas de episodios agrupadas por año con "sesiones" y "última sesión", y el mismo ajuste preventivo de z-index en el header.
