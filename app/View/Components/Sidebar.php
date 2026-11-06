<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Sidebar extends Component
{
    public string $active;

    public function __construct(string $active = '')
    {
        $this->active = $active;
    }

    public function render()
    {
        return view('components.sidebar');
    }
}