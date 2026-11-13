<?php

namespace App\Support;

/**
 * Catálogos estáticos del formulario de Expediente (identificación, contacto,
 * entorno, antecedentes, interrogatorio, eventos). Fuente única para los
 * <select>, las casillas y las fichas sugeridas, usada tanto al crear como al
 * editar un paciente.
 */
class ExpedienteCatalogo
{
    public static function listas(): array
    {
        return [
            'duenoTel'      => ['Del paciente', 'De un familiar o tutor', 'Otro'],
            'fuente'        => ['Google Maps', 'Recomendación de paciente', 'Médico remitente', 'Ubicación', 'Instagram', 'Facebook', 'TikTok', 'Sitio web', 'Otro'],
            'zona'          => ['Urbana', 'Suburbana', 'Rural'],
            'casa'          => ['Propia', 'Rentada', 'Compartida', 'Prestada', 'Institución o asilo'],
            'transporte'    => ['Propio', 'Público', 'Mixto', 'Dependiente de otra persona', 'Caminando'],
            'escolaridad'   => ['Sin escolaridad', 'Preescolar', 'Primaria', 'Secundaria', 'Bachillerato', 'Técnico', 'Licenciatura', 'Posgrado'],
            'dependencia'   => ['Independiente', 'De los padres', 'De la pareja', 'De los hijos', 'De otro familiar', 'Pensión o jubilación'],
            'lateralidad'   => ['Diestro(a)', 'Zurdo(a)', 'Ambidiestro(a)'],
            'civil'         => ['Soltero(a)', 'Casado(a)', 'Unión libre', 'Separado(a)', 'Divorciado(a)', 'Viudo(a)'],
            'complexion'    => ['Delgada', 'Mediana', 'Robusta'],
            'laboral'       => ['Sedentaria', 'Ligera', 'Moderada', 'Pesada', 'Repetitiva', 'Mixta'],
            'dieta'         => ['Balanceada', 'Alta en carbohidratos', 'Alta en grasas', 'Baja en proteína', 'Irregular', 'Restrictiva o especial'],
            'avd'           => ['Independiente', 'Parcialmente dependiente', 'Dependiente'],
            'salud'         => ['Bueno', 'Regular', 'Malo'],
            'sueno'         => ['7 a 8 horas', 'Más de 8 horas', 'Menos de 6 horas', 'Sueño no reparador', 'Insomnio', 'Usa medicamento para dormir'],
            'barreras'      => ['Escaleras para entrar', 'Escaleras dentro de casa', 'Baño no accesible', 'Puertas o pasillos estrechos', 'Desniveles o banquetas', 'Edificio sin elevador', 'Ninguna'],
            'animales'      => ['Perro', 'Gato', 'Aves', 'Otros', 'Ninguno'],
            'toxicos'       => ['Tabaco', 'Alcohol', 'Drogas', 'Cafeína en exceso', 'Niega'],
            'alergias'      => ['Medicamentos', 'Alimentos', 'Ambientales', 'Látex', 'Niega'],
            'parentesco'    => ['Madre', 'Padre', 'Hijo(a)', 'Cónyuge o pareja', 'Hermano(a)', 'Abuelo(a)', 'Tutor legal', 'Amigo(a)', 'Otro'],
            'parentescoAhf' => ['Madre', 'Padre', 'Hermano(a)', 'Abuelos', 'Hijo(a)'],
        ];
    }

    /**
     * Región corporal: cada una trae sus diagnósticos sugeridos (dx = caso
     * conservador, qx = posoperatorio) para las fichas del diagnóstico principal.
     */
    public static function regiones(): array
    {
        return [
            'cervical' => ['n' => 'Columna cervical', 'dx' => ['Cervicalgia', 'Cervicobraquialgia', 'Hernia discal cervical', 'Latigazo cervical', 'Tortícolis'], 'qx' => ['Posop. de artrodesis cervical', 'Posop. de discectomía cervical', 'Posop. de laminectomía cervical']],
            'dorsal' => ['n' => 'Columna dorsal', 'dx' => ['Dorsalgia', 'Escoliosis', 'Hipercifosis', 'Síndrome costovertebral'], 'qx' => ['Posop. de artrodesis dorsal', 'Posop. de corrección de escoliosis']],
            'lumbar' => ['n' => 'Columna lumbar', 'dx' => ['Lumbalgia', 'Lumbalgia mecánica', 'Lumbociática', 'Hernia discal lumbar', 'Estenosis lumbar', 'Espondilolistesis'], 'qx' => ['Posop. de artrodesis lumbar', 'Posop. de discectomía lumbar', 'Posop. de laminectomía lumbar', 'Posop. de descompresión']],
            'hombro' => ['n' => 'Hombro', 'dx' => ['Síndrome de pinzamiento', 'Tendinopatía del manguito rotador', 'Capsulitis adhesiva', 'Bursitis subacromial', 'Luxación de hombro'], 'qx' => ['Posop. de reparación de manguito rotador', 'Posop. de prótesis de hombro', 'Posop. de estabilización por luxación', 'Posop. de acromioplastia', 'Posop. de fractura de húmero proximal']],
            'codo' => ['n' => 'Codo', 'dx' => ['Epicondilitis lateral', 'Epitrocleítis', 'Bursitis olecraneana', 'Rigidez de codo'], 'qx' => ['Posop. de fractura de olécranon', 'Posop. de fractura de cabeza radial', 'Posop. de liberación de epicóndilo']],
            'mano' => ['n' => 'Muñeca y mano', 'dx' => ['Síndrome del túnel del carpo', 'Tenosinovitis de De Quervain', 'Rizartrosis', 'Dedo en gatillo', 'Fractura de muñeca en tratamiento conservador'], 'qx' => ['Posop. de fractura de muñeca (radio distal)', 'Posop. de liberación del túnel del carpo', 'Posop. de fractura de escafoides', 'Posop. de tenorrafia de mano']],
            'cadera' => ['n' => 'Cadera', 'dx' => ['Coxartrosis', 'Bursitis trocantérica', 'Pinzamiento femoroacetabular', 'Síndrome del piramidal'], 'qx' => ['Posop. de prótesis total de cadera', 'Posop. de fractura de cadera', 'Posop. de artroscopia de cadera', 'Posop. de osteotomía de cadera']],
            'rodilla' => ['n' => 'Rodilla', 'dx' => ['Gonartrosis', 'Lesión de LCA', 'Lesión meniscal', 'Síndrome patelofemoral', 'Tendinopatía rotuliana'], 'qx' => ['Posop. de prótesis total de rodilla', 'Posop. de plastía de LCA', 'Posop. de meniscectomía', 'Posop. de fractura de platillo tibial', 'Posop. de realineación rotuliana']],
            'pie' => ['n' => 'Tobillo y pie', 'dx' => ['Esguince de tobillo', 'Fascitis plantar', 'Tendinopatía aquílea', 'Hallux valgus', 'Metatarsalgia', 'Fractura de tobillo en tratamiento conservador'], 'qx' => ['Posop. de fractura de tobillo', 'Posop. de reparación del tendón de Aquiles', 'Posop. de hallux valgus', 'Posop. de artrodesis de tobillo']],
            'pelvis' => ['n' => 'Pelvis y suelo pélvico', 'dx' => ['Disfunción de suelo pélvico', 'Incontinencia urinaria de esfuerzo', 'Dolor pélvico crónico', 'Diástasis abdominal'], 'qx' => ['Posop. de cesárea', 'Posop. de histerectomía', 'Posop. de corrección de prolapso', 'Posop. de hernia inguinal', 'Posop. de prostatectomía']],
            'neuro' => ['n' => 'Neurológico central', 'dx' => ['Hemiparesia post-EVC', 'Parálisis cerebral', 'Esclerosis múltiple', 'Lesión medular', 'Enfermedad de Parkinson'], 'qx' => ['Posop. de craneotomía', 'Posop. de descompresión medular', 'Posop. de derivación ventricular']],
            'general' => ['n' => 'Generalizado', 'dx' => ['Fibromialgia', 'Desacondicionamiento físico', 'Rehabilitación post-COVID', 'Linfedema', 'Síndrome de inmovilidad'], 'qx' => ['Posop. de cirugía abdominal mayor', 'Posop. de cirugía cardíaca', 'Posop. de cirugía bariátrica', 'Posop. de amputación']],
        ];
    }

    /**
     * Matriz de antecedentes (familia / paciente). Las fichas acotan la
     * frontera de cada renglón (p. ej. Diabetes trae DM1/DM2/Gestacional,
     * para no mezclarlo con Metabólicas).
     */
    public static function antecedentes(): array
    {
        return [
            'diabetes' => ['n' => 'Diabetes', 'f' => ['DM1', 'DM2', 'Gestacional', 'Prediabetes']],
            'hta'      => ['n' => 'Hipertensión', 'f' => ['Hipertensión arterial', 'Hipotensión', 'Preeclampsia']],
            'convul'   => ['n' => 'Convulsiones', 'f' => ['Epilepsia', 'Crisis febriles', 'Crisis aislada']],
            'neo'      => ['n' => 'Neoplasias', 'f' => ['Mama', 'Colon', 'Pulmón', 'Próstata', 'Cervicouterino']],
            'card'     => ['n' => 'Cardíacas', 'f' => ['Infarto', 'Arritmias', 'Enfermedad coronaria', 'Insuficiencia cardíaca', 'Valvulopatía']],
            'vasc'     => ['n' => 'Vasculares', 'f' => ['Várices', 'Trombosis', 'Insuficiencia venosa', 'Linfedema']],
            'pulm'     => ['n' => 'Pulmonares', 'f' => ['Asma', 'EPOC', 'Bronquitis crónica', 'Fibrosis', 'Apnea del sueño']],
            'metab'    => ['n' => 'Metabólicas', 'f' => ['Obesidad', 'Hipotiroidismo', 'Hipertiroidismo', 'Dislipidemia', 'Gota']],
            'congen'   => ['n' => 'Congénitas y síndromes', 'f' => ['Sx Down', 'Marfan', 'Ehlers-Danlos', 'TEA', 'Parálisis cerebral', 'Displasia de cadera']],
            'neuro'    => ['n' => 'Neurológicas', 'f' => ['Parkinson', 'Alzheimer', 'EVC', 'Esclerosis múltiple', 'Neuropatía']],
            'autoinm'  => ['n' => 'Autoinmunes', 'f' => ['Artritis reumatoide', 'Lupus', 'Esclerodermia', 'Fibromialgia', 'Sjögren']],
            'psiq'     => ['n' => 'Psiquiátricas', 'f' => ['Ansiedad', 'Depresión', 'Trastorno bipolar', 'TDAH']],
        ];
    }

    /**
     * Interrogatorio por síntomas (qué siente el paciente hoy, no qué
     * enfermedad tiene). "bandera" indica qué Bandera Clínica alimenta ese
     * síntoma si contesta "Sí".
     */
    public static function interrogatorio(): array
    {
        return [
            'visual'   => ['n' => 'Alteraciones visuales', 'f' => ['Visión borrosa', 'Diplopía', 'Pérdida de visión', 'Fotofobia']],
            'auditivo' => ['n' => 'Alteraciones auditivas', 'f' => ['Hipoacusia', 'Tinnitus', 'Vértigo']],
            'sensib'   => ['n' => 'Alteraciones de la sensibilidad', 'f' => ['Parestesias', 'Hipoestesia', 'Dolor neuropático', 'Anestesia en silla de montar'], 'bandera' => 'Anestesia en silla de montar · Signos neurológicos progresivos'],
            'musculo'  => ['n' => 'Musculoesquelético', 'f' => ['Dolor localizado', 'Debilidad muscular', 'Limitación de movimiento', 'Rigidez matutina', 'Inestabilidad articular'], 'bandera' => 'Debilidad progresiva'],
            'disnea'   => ['n' => 'Falta de aire o fatiga al esfuerzo', 'f' => ['En reposo', 'Al caminar', 'Al subir escaleras'], 'bandera' => 'Disnea'],
            'torax'    => ['n' => 'Dolor torácico', 'f' => ['En reposo', 'Al esfuerzo', 'Irradiado a brazo o mandíbula'], 'bandera' => 'Dolor torácico'],
            'mareo'    => ['n' => 'Mareo o desmayo', 'f' => ['Al levantarse', 'Durante el esfuerzo', 'Espontáneo'], 'bandera' => 'Mareo o síncope'],
            'gastro'   => ['n' => 'Gastrointestinal', 'f' => ['Dolor abdominal', 'Náusea', 'Reflujo', 'Estreñimiento']],
            'genito'   => ['n' => 'Genitourinario', 'f' => ['Dolor pélvico', 'Incontinencia', 'Urgencia urinaria'], 'bandera' => 'Incontinencia urinaria/fecal reciente'],
            'piel'     => ['n' => 'Piel', 'f' => ['Cicatrices', 'Lesiones', 'Cambios de coloración', 'Edema']],
            'otros'    => ['n' => 'Otros', 'f' => []],
        ];
    }

    /**
     * Listas repetibles de "Eventos y medicación".
     */
    public static function listasRepetibles(): array
    {
        return [
            'hosp'  => ['n' => 'Hospitalizaciones', 'col' => 'Motivo', 'fecha' => true, 'f' => ['Cirugía', 'Accidente', 'Enfermedad aguda']],
            'cirug' => ['n' => 'Cirugías', 'col' => 'Cirugía', 'fecha' => true, 'f' => ['Rodilla', 'Hombro', 'Columna', 'Cesárea', 'Apendicectomía']],
            'traum' => ['n' => 'Traumatismos', 'col' => 'Descripción', 'fecha' => true, 'f' => ['Fractura', 'Esguince', 'Luxación', 'Contusión', 'Craneoencefálico']],
            'medic' => ['n' => 'Medicamentos actuales', 'col' => 'Medicamento', 'seg' => 'Dosis e indicación', 'fecha' => false, 'f' => ['Analgésico', 'Antiinflamatorio', 'Antihipertensivo', 'Hipoglucemiante', 'Ansiolítico']],
        ];
    }

    public static function meses(): array
    {
        return ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
    }

    /**
     * Opciones de "¿Cuándo fue?": lo clínicamente útil es qué tan atrás fue,
     * no la fecha exacta.
     */
    public static function opcionesCuando(): array
    {
        $y = (int) now()->format('Y');
        $o = ['' => '¿Cuándo fue?', (string) $y => "Este año · {$y}", (string) ($y - 1) => 'El año pasado · ' . ($y - 1)];
        for ($i = 2; $i <= 5; $i++) {
            $o[(string) ($y - $i)] = "Hace {$i} años · " . ($y - $i);
        }
        $o[($y - 10) . '-' . ($y - 6)] = 'Hace 6 a 10 años · ' . ($y - 10) . ' a ' . ($y - 6);
        $o[($y - 20) . '-' . ($y - 11)] = 'Hace 11 a 20 años · ' . ($y - 20) . ' a ' . ($y - 11);
        $o['antes-' . ($y - 20)] = 'Hace más de 20 años · antes de ' . ($y - 20);
        $o['infancia'] = 'En la infancia';
        $o['no-recuerda'] = 'No recuerda';

        return $o;
    }
}
