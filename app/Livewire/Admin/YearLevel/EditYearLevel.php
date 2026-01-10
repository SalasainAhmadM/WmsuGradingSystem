<?php

namespace App\Livewire\Admin\YearLevel;

use Livewire\Component;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class EditYearLevel extends Component
{
    public $title = "Year Level";

    public $route = 'year-level';

    public function mount($id){
        $detail = DB::table('year_levels')
            ->where('id','=',$id)
            ->first();
        $this->detail = [
            'id' => $detail->id,
            'year_level' => $detail->year_level,
        ];
    }

    public $detail = [
        'id'=> NULL,
        'year_level'=> NULL,
    ];

   protected function rules(){
        return [
            'detail.year_level' => [
                'required',
                'string',
                Rule::unique('semesters', 'semester')->ignore(($this->detail['id'] ?? 'NULL')),
            ]
        ];
    }

    protected $messages = [
        'detail.year_level.required' => 'Year level is required.',
        'detail.year_level.string' => 'Year level must be a valid string.',
        'detail.year_level.unique' => 'This year level already exists.',
    ];

    public function save(){
        $this->validate();

        $updated = DB::table('year_levels')
            ->where('id','=',$this->detail['id'])
            ->update([
                'year_level'=>$this->detail['year_level']
            ]);

        if ($updated) {
            // You can dispatch success notification or redirect here
        }
        $this->dispatch('notifySuccess', 
        'Updated successfully!',
            route($this->route.'-lists'));
    }
    public function render()
    {
        return view('livewire.admin.year-level.edit-year-level')
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
