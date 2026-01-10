<?php

namespace App\Livewire\Admin\Users;

use Livewire\Component;

class ViewUser extends Component
{
    public $title = "User";

    public function render()
    {
        return view('livewire.admin.users.view-user')
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
