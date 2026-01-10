<?php

namespace App\Livewire\Admin\EnrolledStudent;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class DeleteEnrolledStudent extends Component
{
    public function render()
    {
        return view('livewire.admin.enrolled-student.delete-enrolled-student');
    }
}
