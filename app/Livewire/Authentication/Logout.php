<?php

namespace App\Livewire\Authentication;

use Livewire\Component;
use Illuminate\Support\Facades\Session;

class Logout extends Component
{

    public function boot(){
        Session::forget('user_id');
        return redirect(route('login'));
    }
    public function render()
    {
        return view('livewire.authentication.logout');
    }
}
