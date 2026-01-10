<?php

namespace App\Livewire\Admin\GradeEquivalent;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class DeleteGradeEquivalent extends Component
{

    public $title = "Grade Equivalent";

    public $route ='grade-equivalent';

    public $detail = [
        'id'=> NULL,
        'minimum'=> NULL,
        'maximum' => NULL,  
        'grade'=> NULL,
    ];

    
    public function mount($id){
        $detail = DB::table('point_grade_equivalent')
            ->where('id','=',$id)
            ->first();
        $this->detail = [
        'id'=> $detail->id,
        'minimum'=> $detail->minimum,
        'maximum' => $detail->maximum,  
        'grade'=> $detail->grade,
    ];

    }
    public function save(){
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
            ->where('id', '!=', $this->detail['id']) // Exclude current record
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



        if(DB::table('point_grade_equivalent')
            ->where('id','=',$this->detail['id'])
            ->delete()){
            }
        $this->dispatch('notifySuccess', 
        'Deleted successfully!',
            route($this->route.'-lists'));
    }
    public function render()
    {
        return view('livewire.admin.grade-equivalent.delete-grade-equivalent')
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }
}
