<?php

namespace App\Livewire\Admin\SchoolYear;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class DeleteSchoolYear extends Component
{
    public $title = "School Year";
    public $route = 'school-year';

    public function render()
    {
        return view('livewire.admin.school-year.delete-school-year')
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
