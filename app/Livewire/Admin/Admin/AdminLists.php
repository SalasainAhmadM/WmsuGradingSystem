<?php

namespace App\Livewire\Admin\Admin;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class AdminLists extends Component
{
    public $title = "Admin";
    public function render()
    {
        return view('livewire.admin.admin.admin-lists')
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
