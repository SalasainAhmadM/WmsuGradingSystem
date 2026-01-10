<?php

namespace App\Livewire\Admin\Users;

use Livewire\Component;

class UserLists extends Component
{

    public $title = "User";

    public function render()
    {
        return view('livewire.admin.users.user-lists')
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
