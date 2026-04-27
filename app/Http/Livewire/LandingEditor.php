<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Storage;

class LandingEditor extends Component
{
    use WithFileUploads;

    // Text fields
    public $hero_title;
    public $hero_subtitle;
    public $hero_button;
    public $contact_phone;
    public $contact_address;
    public $maps_url;
    public $whatsapp_url;
    public $schedule_weekdays;
    public $schedule_saturday;
    public $schedule_sunday;
    public $quote_text;

    // Image fields
    public $hero_image_upload;

    public function mount()
    {
        $this->hero_title        = SiteSetting::get('hero_title', 'En Rehavité buscamos la mejora continua.');
        $this->hero_subtitle     = SiteSetting::get('hero_subtitle', 'Te estamos esperando');
        $this->hero_button       = SiteSetting::get('hero_button', 'AGENDA TU CITA HOY');
        $this->contact_phone     = SiteSetting::get('contact_phone', '6181102286');
        $this->contact_address   = SiteSetting::get('contact_address', 'Agrario 115, Col. Burócrata, Durango, Dgo.');
        $this->maps_url          = SiteSetting::get('maps_url', '');
        $this->whatsapp_url      = SiteSetting::get('whatsapp_url', '');
        $this->schedule_weekdays = SiteSetting::get('schedule_weekdays', 'Lunes - Viernes: 10:00am – 08:00pm');
        $this->schedule_saturday = SiteSetting::get('schedule_saturday', 'Sábado: 10:00am – 02:00pm');
        $this->schedule_sunday   = SiteSetting::get('schedule_sunday', 'Domingo: Cerrado');
        $this->quote_text        = SiteSetting::get('quote_text', '');
    }

    public function updatedHeroImageUpload()
    {
        $ext = strtolower($this->hero_image_upload->getClientOriginalExtension());
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            $this->reset('hero_image_upload');
            $this->dispatch('alert', message: 'Solo se permiten imágenes JPG, PNG o WEBP.');
        }
    }

    public function save()
    {
        $this->validate([
            'hero_title'        => 'required|string|max:200',
            'hero_subtitle'     => 'nullable|string|max:200',
            'hero_button'       => 'nullable|string|max:100',
            'contact_phone'     => 'nullable|string|max:20',
            'contact_address'   => 'nullable|string|max:500',
            'maps_url'          => 'nullable|url|max:500',
            'whatsapp_url'      => 'nullable|url|max:500',
            'schedule_weekdays' => 'nullable|string|max:100',
            'schedule_saturday' => 'nullable|string|max:100',
            'schedule_sunday'   => 'nullable|string|max:100',
            'quote_text'        => 'nullable|string|max:1000',
            'hero_image_upload' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        // Save text settings
        $fields = [
            'hero_title', 'hero_subtitle', 'hero_button',
            'contact_phone', 'contact_address', 'maps_url', 'whatsapp_url',
            'schedule_weekdays', 'schedule_saturday', 'schedule_sunday', 'quote_text',
        ];
        foreach ($fields as $field) {
            SiteSetting::set($field, $this->$field);
        }

        // Save hero image if uploaded
        if ($this->hero_image_upload) {
            $oldPath = SiteSetting::get('hero_image');
            if ($oldPath) {
                Storage::disk('public')->delete($oldPath);
            }
            $path = $this->hero_image_upload->store('landing', 'public');
            SiteSetting::set('hero_image', $path);
            $this->reset('hero_image_upload');
        }

        session()->flash('success', 'Configuración de la landing page guardada correctamente.');
    }

    public function render()
    {
        $currentHeroImage = SiteSetting::get('hero_image');
        return view('livewire.landing-editor', compact('currentHeroImage'));
    }
}
