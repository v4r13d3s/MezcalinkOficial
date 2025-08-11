<?php

namespace App\Livewire;

use Livewire\Component;
use App\Livewire\livewire;

class Navbar extends Component
{
    public $menuOpen = false;
    public $isClosing = false;

    public function toggleMenu()
    {
        if ($this->menuOpen) {
            // Si está abierto, inicia animación de cierre
            $this->isClosing = true;
            // Espera 200ms antes de cerrar realmente (igual que la duración de la animación)
            $this->dispatch('close-menu');
        } else {
            $this->menuOpen = true;
            $this->isClosing = false;
        }
    }

    public function render()
    {
        return view('livewire.navbar');
    }
}
