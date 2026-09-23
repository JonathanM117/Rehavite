<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\SystemSetting;

class SiteSettings extends Component
{
    public $trash_days = 15;
    public $audit_group_minutes = 10;
    public $saved = false;

    public function mount()
    {
        $this->trash_days = SystemSetting::get('trash_days', 15);
        $this->audit_group_minutes = SystemSetting::get('audit_group_minutes', 10);
    }

    public function save()
    {
        $this->validate([
            'trash_days'           => 'required|integer|min:1|max:365',
            'audit_group_minutes'  => 'required|integer|min:1|max:120',
        ]);

        SystemSetting::set('trash_days', $this->trash_days);
        SystemSetting::set('audit_group_minutes', $this->audit_group_minutes);

        $this->saved = true;

        // Auto-hide after 3 seconds
        $this->dispatch('settings-saved');
    }

    public function render()
    {
        return view('livewire.site-settings');
    }
}
