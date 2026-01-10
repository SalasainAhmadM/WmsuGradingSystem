<?php

namespace App\Livewire\Admin\GradeEquivalent;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class ActivateGradeEquivalent extends Component
{
    public function render()
    {
        return view('livewire.admin.grade-equivalent.activate-grade-equivalent');
    }
}
