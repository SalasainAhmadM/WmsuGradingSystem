<?php

namespace App\Livewire\Admin\Curriculum;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Validator;

class CurriculumLists extends Component
{
    use WithPagination;
    public $title = "Curriculum";

    public $route = "curriculum";

    public function rules(){
        return [
            'detail.school_year_id' => 'required|exists:school_years,id',
            'detail.prospectus'     => 'required|string',
            'detail.effective_date' => 'required|date',
        ];
    }

    public function messages(){
        return [
            'detail.school_year_id.required'    => 'The school year is required.',
            'detail.school_year_id.exists'      => 'The selected school year does not exist.',
            'detail.prospectus.required'     => 'The prospectus is required.',
            'detail.prospectus.string'       => 'The prospectus must be a valid text.',
            'detail.effective_date.required' => 'The effective date is required.',
            'detail.effective_date.date'     => 'The effective date must be a valid date.',
        ];
    }
    public $curriculum = [
        'id'=> NULL,
        'school_year_id' => NULL,
        'college_id' => NULL,
        'department_id' => NULL,
        'prospectus' => NULL,
        'is_editable' => true,
    ];

    public $detail =[
        'id'=>NULL,
        'school_year_id'=>NULL,
        'department_id'=> NULL,
        'college_id' => NULL,
        'prospectus'=> NULL,
        'effective_date'=> NULL,
    ];
    public $school_years;

    public function mount($college,$department){
        $this->curriculum['college'] = $college;
        $this->curriculum['department'] = $department;

        $this->detail['college_id'] = DB::table('colleges')->where('code','=',$college)->first()->id;
        $this->detail['department_id'] = DB::table('departments')->where('code','=',$department)->first()->id;

        $this->school_years = DB::table('school_years')
            ->get()
            ->toArray();
    }
    public function render(){

         $table_data = DB::table('curriculums as c')
            ->select(
                'c.id',
                'school_year_id',
                'department_id',
                'college_id',
                'year_start',
                'year_end',
                'prospectus',
                'effective_date'
            )
            ->join('school_years as sy','sy.id','c.school_year_id')
            ->where('college_id','=',$this->detail['college_id'])
            ->where('department_id','=',$this->detail['department_id'])
            ->orderBy('c.id', 'desc')
            ->paginate(10)->withPath(url()->current());
        
        return view('livewire.admin.curriculum.curriculum-lists',[
            'table_data' =>$table_data
        ])
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }

    public function add($modal_id){
        $this->detail =[
            'id'=>NULL,
            'school_year_id'=>NULL,
            'department_id'=> $this->detail['department_id'],
            'college_id' => $this->detail['college_id'],
            'prospectus'=> NULL,
            'effective_date'=> NULL,
        ];
        $this->dispatch('openModal',modal_id:$modal_id);
    }

    public function saveAdd($modal_id){
        $this->validate();

        if(DB::table('curriculums')
            ->where('department_id','=',$this->detail['department_id'])    
            ->where('college_id','=',$this->detail['college_id'])    
            ->where('school_year_id','=',$this->detail['school_year_id'])
            ->first()    
        ){
            throw \Illuminate\Validation\ValidationException::withMessages([
                'detail.school_year_id' => 'Curriculum exist for the selected school year.',
            ]);  
        }

        if(DB::table('curriculums')->insert($this->detail)){
            $this->dispatch('closeModal',modal_id:$modal_id);

            $this->dispatch('notifySuccess', 
            'Added successfully!',
                "");
        }
    }

    public function view($id,$modal_id){
        $detail = DB::table('curriculums')
            ->where('id','=',$id)
            ->first();
        $this->detail =[
            'id'=>$detail->id,
            'school_year_id'=>NULL,
            'department_id'=> $this->detail['department_id'],
            'college_id' => $this->detail['college_id'],
            'prospectus'=> NULL,
            'effective_date'=> NULL,
        ];
        $this->dispatch('openModal',modal_id:$modal_id);
    }
    
    public function delete($modal_id){
        DB::table('curriculums')
            ->where('id','=',$this->detail['id'])
            ->delete();
        $this->dispatch('closeModal',modal_id:$modal_id);

    }
}
