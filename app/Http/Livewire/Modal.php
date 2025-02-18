<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Modal extends Component
{
    public bool $show = false;
    public string $name = '';

    protected $listeners = [
        'modal.open' => 'openModal',
        'modal.close' => 'closeModal'
    ];

    public function openModal($name)
    {
        $this->name = $name;
        $this->show = true;
    }

    public function closeModal()
    {
        $this->show = false;
        $this->name = '';
    }

    public function render()
    {
        return view('livewire.modal');
    }
}
