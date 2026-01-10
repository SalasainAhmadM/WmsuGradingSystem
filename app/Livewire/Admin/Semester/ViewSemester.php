<?php

namespace App\Livewire\Admin\Semester;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class ViewSemester extends Component
{
    public $title = "Semester";

    public function render()
    {
        return view('livewire.admin.semester.view-semester')
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
