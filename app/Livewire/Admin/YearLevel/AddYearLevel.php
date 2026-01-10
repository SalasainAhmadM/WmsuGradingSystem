<?php

namespace App\Livewire\Admin\YearLevel;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class AddYearLevel extends Component
{
    public $title = "Year Level";

    public $route = 'year-level';

    public $detail = [
        'year_level'=> NULL,
    ];

    protected $rules = [
        'detail.year_level' => 'required|string|unique:year_levels,year_level',
    ];

    protected $messages = [
        'detail.year_level.required' => 'Year level is required.',
        'detail.year_level.string' => 'Year level must be a valid string.',
        'detail.year_level.unique' => 'This year level already exists.',
    ];

    public function save(){
        $this->validate();

        $inserted = DB::table('year_levels')->insert($this->detail);

        if ($inserted) {
            // You can dispatch success notification or redirect here
            $this->dispatch('notifySuccess', 
            'Added successfully!',
                route($this->route.'-lists'));
            
        }
    }
    public function render()
    {
        return view('livewire.admin.year-level.add-year-level')
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
