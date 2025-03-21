<?php

namespace App\Livewire\Settings;

use Livewire\Component;

class Appearance extends Component
{
    public $appearance = 'light';
    
    public function mount()
    {
        // Memastikan selalu menggunakan light mode
        $this->appearance = 'light';
    }
    
    public function render()
    {
        return view('livewire.settings.appearance');
    }
}
