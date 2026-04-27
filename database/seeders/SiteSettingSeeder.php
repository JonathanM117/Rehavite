<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SiteSetting;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Landing page hero
            ['key' => 'hero_title',    'value' => 'En Rehavité buscamos la mejora continua.',     'type' => 'text',     'label' => 'Título principal (Hero)',        'group' => 'landing'],
            ['key' => 'hero_subtitle', 'value' => 'Te estamos esperando',                          'type' => 'text',     'label' => 'Subtítulo Hero',                 'group' => 'landing'],
            ['key' => 'hero_button',   'value' => 'AGENDA TU CITA HOY',                           'type' => 'text',     'label' => 'Texto del botón Hero',           'group' => 'landing'],
            ['key' => 'hero_image',    'value' => null,                                            'type' => 'image',    'label' => 'Imagen de fondo Hero',           'group' => 'landing'],

            // Contact info
            ['key' => 'contact_phone', 'value' => '6181102286',                                   'type' => 'text',     'label' => 'Teléfono / WhatsApp',            'group' => 'contact'],
            ['key' => 'contact_address','value' => 'Agrario 115, esq. con Pino Suárez, Col. Burócrata, C.P. 34279, Durango, Durango.', 'type' => 'textarea', 'label' => 'Dirección', 'group' => 'contact'],
            ['key' => 'maps_url',      'value' => 'https://maps.app.goo.gl/xqHfuRjY91BcJbCR8',  'type' => 'text',     'label' => 'URL de Google Maps (botón)',     'group' => 'contact'],
            ['key' => 'whatsapp_url',  'value' => 'https://wa.me/526181102286',                   'type' => 'text',     'label' => 'URL de WhatsApp',                'group' => 'contact'],

            // Schedule
            ['key' => 'schedule_weekdays',  'value' => 'Lunes - Viernes: 10:00am – 08:00pm',     'type' => 'text',     'label' => 'Horario entre semana',           'group' => 'schedule'],
            ['key' => 'schedule_saturday',  'value' => 'Sábado: 10:00am – 02:00pm',              'type' => 'text',     'label' => 'Horario Sábado',                 'group' => 'schedule'],
            ['key' => 'schedule_sunday',    'value' => 'Domingo: Cerrado',                        'type' => 'text',     'label' => 'Horario Domingo',                'group' => 'schedule'],

            // Quote section
            ['key' => 'quote_text',    'value' => '"Nos mueve la idea de ayudar a nuestros pacientes a tener una mejor calidad de vida a través de nuestros servicios de fisioterapia".', 'type' => 'textarea', 'label' => 'Frase motivacional', 'group' => 'landing'],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}
