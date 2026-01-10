<?php

namespace App\Livewire\Admin\GradeEquivalent;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class AddGradeEquivalent extends Component
{

    public $title = "Grade Equivalent";

    public $route ='grade-equivalent';

    public $detail = [
        'minimum'=> NULL,
        'maximum' => NULL,  
        'grade'=> NULL,
    ];


    public function saveAdd(){
        $this->validate([
            'detail.minimum' => 'required|numeric',
            'detail.maximum' => 'required|numeric|gt:detail.minimum',
            'detail.grade' => 'required|string|max:5',
        ], [
            'detail.minimum.required' => 'The minimum value is required.',
            'detail.minimum.numeric'  => 'The minimum must be a number.',
            
            'detail.maximum.required' => 'The maximum value is required.',
            'detail.maximum.numeric'  => 'The maximum must be a number.',
            'detail.maximum.gt'       => 'The maximum must be greater than the minimum.',
            
            'detail.grade.required'   => 'The grade field is required.',
            'detail.grade.string'     => 'The grade must be a string.',
            'detail.grade.max'        => 'The grade may not be greater than 5 characters.',
        ]);

        $overlap = DB::table('point_grade_equivalent')
            ->where(function ($query) {
                $query->whereBetween('minimum', [$this->detail['minimum'], $this->detail['maximum']])
                    ->orWhereBetween('maximum', [$this->detail['minimum'], $this->detail['maximum']])
                    ->orWhere(function ($q) {
                        $q->where('minimum', '<', $this->detail['minimum'])
                            ->where('maximum', '>', $this->detail['maximum']);
                    });
            })
            ->exists();

        if ($overlap) {
            $this->addError('detail.minimum', 'This range overlaps with an existing entry.');
            $this->addError('detail.maximum', 'This range overlaps with an existing entry.');
            return;
        }


        if(DB::table('point_grade_equivalent')->insert([
            $this->detail])){
            $this->dispatch('notifySuccess', 
            'Added successfully!',
                route($this->route.'-lists'));
        }
    }

    public function render()
    {
        return view('livewire.admin.grade-equivalent.add-grade-equivalent')
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
