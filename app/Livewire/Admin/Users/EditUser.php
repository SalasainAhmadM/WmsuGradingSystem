<?php

namespace App\Livewire\Admin\Users;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class EditUser extends Component
{
    public $title = "User";
    
    public function render()
    {
        return view('livewire.admin.users.edit-user')
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
