<?php

namespace App\Livewire\Admin\Dashboard;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class Dashboard extends Component
{
    public $title = 'Dashboard';
    public function render()
    {
        return view('livewire.admin.dashboard.dashboard')
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
