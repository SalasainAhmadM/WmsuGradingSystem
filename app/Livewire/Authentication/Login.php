<?php

namespace App\Livewire\Authentication;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;


class Login extends Component
{

    public $detail = [
        'email'=> NULL,
        'password'=> NULL,
    ];

    protected $rules = [
        'detail.email' => 'required|email|exists:users,email',
        'detail.password' => 'required|min:6',
    ];

    protected $messages = [
        'detail.email.required' => 'The email field is required.',
        'detail.email.email' => 'Please enter a valid email address.',
        'detail.email.exists' => 'This email does not exist in our records.',
        
        'detail.password.required' => 'The password field is required.',
        'detail.password.min' => 'The password must be at least :min characters.',
    ];


    public function login(){
        $this->validate();

        $user = \App\Models\User::where('email', $this->detail['email'])->first();

       if (!$user || !Hash::check($this->detail['password'], $user->password)) {
            $this->addError('detail.password', 'The provided credentials are incorrect.');
            return;
        }


        Auth::login($user);

        Session::put('user_id', $user->id);

        $this->dispatch('notifySuccess', 
            'Logged in successfully!',
                '');
        return redirect()->intended('admin/dashboard'); 
    }
    public function render()
    {
        return view('livewire.authentication.login');
    }
}
