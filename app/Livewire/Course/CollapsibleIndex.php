<?php

namespace App\Livewire\Course;

use Livewire\Component;

class CollapsibleIndex extends Component
{
    public $items;

    public function render()
    {
        return view('livewire.course.collapsible-index');
    }
}
