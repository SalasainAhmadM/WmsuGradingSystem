<?php

namespace App\Livewire\Admin\Student;

use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;

use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExportTemplate;
use Maatwebsite\Excel\Excel as ExcelFormat;
use App\Exports\MultiSheetExport;
use Illuminate\Support\Facades\Validator;
use App\Imports\ExcelParser;
use Livewire\WithFileUploads;

class StudentLists extends Component
{
    use WithPagination,WithFileUploads;

    public $title = "Student";

    public $route = "student";

    public $colleges = [];
    public $departments = [];
    public $year_levels = [];

    public $grades = [];
    public $grades_v2 = [];

    public $equivalent_grade = [];

    public $semesters = [];

    public $curriculum = [];

    public $curriculum_id, $curriculums = [];
    public $filters = [
        'search'=> NULL,
        'college_id' =>NULL,
        'department_id' =>NULL,
        'year_level_id' =>NULL,
    ];

    public $detail = [
        'user_id'=> NULL,
        'new_password' => NULL,
        'confirm_password' => NULL,
    ];

    public $excel_file_input = NULL;
    public $import = [
        'sheets' => NULL,
        'import_headers' => NULL,
        'total_valid_inserts' => NULL,
        'total_inserts' => NULL,
        'valid_insert_rows_arr' => [],
        'comments' => [],
        'default_import_headers' =>[
            'Email (*)',
            'ID (*)',
            'Year Level ID (Year Levels ;*)',
            'College ID (Colleges ;*)',
            'Department ID (Departments ;*)',
            'First Name (*)',
            'Middle Name',
            'Last Name (*)',
            'Suffix',
        ],
        'headerToFieldMap' => [
            'email (*)' => 'email',
            'id (*)' => 'code',
            'year level id (year levels ;*)' => 'year_level_id',
            'college id (colleges ;*)' => 'college_id',
            'department id (departments ;*)' => 'department_id',
            'first name (*)' => 'first_name',
            'middle name' => 'middle_name',
            'last name (*)' => 'last_name',
            'suffix' => 'suffix',
        ]
    ];



    public function mount(){

        $this->colleges = DB::table('colleges')
            ->where('is_active','=',1)
            ->get()
            ->toArray();
        $this->departments = DB::table('departments')
            ->where('is_active','=',1)
            ->get()
            ->toArray();
        $this->year_levels = DB::table('year_levels')
            ->where('is_active','=',1)
            ->get()
            ->toArray();
        $this->semesters = DB::table('semesters as s')
            ->orderBy('s.is_active','desc')
            ->orderBy('s.id', 'asc')
            ->where('is_active','=',1)
            ->get()
            ->toArray();

    }

    public function render()
    {

        $table_data = DB::table('students as s')
            ->select(
                's.id' ,
                'user_id',
                's.college_id' ,
                'department_id' ,
                'year_level' ,
                DB::raw('CONCAT_WS(" ", s.first_name, s.middle_name, s.last_name, s.suffix) AS fullname'),
                's.code' ,
                'first_name' ,
                'middle_name' ,
                'last_name' ,
                'suffix' ,
                'email' ,
                's.is_active' ,
                'c.name as college',
                'd.name as department',
                'c.code as college_code',
                'd.code as department_code',
                'yl.year_level'
            )
            ->leftJoin('colleges as c','c.id','s.college_id')
            ->leftJoin('departments as d','d.id','s.department_id')
            ->leftJoin('year_levels as yl','yl.id','s.year_level_id');
        
        if($this->filters['college_id']){
            $table_data->where('s.college_id', '=',$this->filters['college_id']);
        }

        if($this->filters['department_id']){
            if($this->filters['department_id']){
            $table_data->where('s.department_id', '=',$this->filters['department_id']);
        }
        }

        if($this->filters['year_level_id']){
            $table_data->where('s.year_level_id', '=',$this->filters['year_level_id']);
        }
        
    

        if (!empty($this->filters['search'])) {
            $table_data
            ->where('s.code','like','%'.$this->filters['search'] .'%')
            ->orwhere('s.email','like','%'.$this->filters['search'] .'%')
            ->orwhere(DB::raw('CONCAT_WS(" ", s.first_name, s.middle_name, s.last_name, s.suffix)'), 'like','%'.$this->filters['search'] .'%');
        }
        $table_data = $table_data
            ->orderBy('s.is_active','desc')
            ->orderBy('s.id', 'desc')
            ->paginate(10)->withPath(url()->current());
        return view('livewire.admin.student.student-lists',[
            'table_data'=>$table_data
        ])
        ->layout('components.layouts.admin-app',[
            'title'=>$this->title
        ]);
    }

    public function gradeLists_v2($id,$modal_id){

        
        $student = DB::table('students as s')
            ->where('s.id', $id)
            ->first();

        $this->curriculums = DB::table('curriculums as c')
            ->select(
                'c.id',
                'year_start',
                'year_end'
            )
            ->join('school_years as sy','sy.id','c.school_year_id')
            ->where('c.college_id','=',$student->college_id)
            ->where('c.department_id','=',$student->department_id)
            ->get()
            ->toArray();
            
        $currentYear = date('Y');


        $curriculum_initial = DB::table('curriculums as c')
            ->select(
                'c.id',
                'year_start',
                'year_end'
            )
            ->join('school_years as sy','sy.id','c.school_year_id')
            ->where('c.college_id','=',$student->college_id)
            ->where('c.department_id','=',$student->department_id)
            ->where('year_start','<=',$currentYear)
            ->where('year_end','=',$currentYear)
            ->first();
            
        if($curriculum_initial){
            $this->curriculum_id = $curriculum_initial->id;
        }

        $this->getCurriculum();
        
        $this->grades_v2 = DB::table('lab_lec_grades as llg')
            ->selectRaw('
                s.id as subject_row_id,
                s.subject_id,
                s.subject_code,
                sm.semester,
                cl.school_year_id,
                s.lecture_unit,
                s.laboratory_unit,
                MAX(cl.date_created) as date_created,
                SUM(CASE WHEN cl.is_lec = 1 THEN llg.grade ELSE 0 END) as lec_total_grade,
                SUM(CASE WHEN cl.is_lec = 0 THEN llg.grade ELSE 0 END) as lab_total_grade,
                SUM(CASE WHEN cl.is_lec = 1 THEN llg.grade / llg.sub_weight ELSE 0 END) as lec_normalized_total,
                SUM(CASE WHEN cl.is_lec = 0 THEN llg.grade / llg.sub_weight ELSE 0 END) as lab_normalized_total,
                SUM(CASE WHEN cl.is_lec = 1 THEN (llg.grade / llg.sub_weight) * 100 ELSE 0 END) as lec_calculated_grade,
                SUM(CASE WHEN cl.is_lec = 0 THEN (llg.grade / llg.sub_weight) * 100 ELSE 0 END) as lab_calculated_grade,
                CONCAT(sy.year_start," - ",sy.year_end) as school_year
            ')
            ->join('schedulings as cl', 'cl.id', '=', 'llg.schedule_id')
            ->join('school_years as sy','sy.id','cl.school_year_id')
            ->join('schedules as sh', 'sh.id', '=', 'cl.schedule_id')
            ->join('subjects as s', 's.id', '=', 'sh.subject_id')
            ->join('semesters as sm', 'sm.id', '=', 'cl.semester_id')
            ->where('llg.student_id', $id)
            ->groupBy(
                's.id',
                's.subject_id',
                's.subject_code',
                's.lecture_unit',
                's.laboratory_unit',
                'sm.semester',
                'cl.school_year_id',
                'sy.year_start',
                'sy.year_end'
            )
            ->orderBy('date_created', 'asc')
            ->get()
            ->toArray();


        // dd($this->grades_v2);

        $this->equivalent_grade = DB::table('point_grade_equivalent')
            ->get()
            ->toArray();
        $this->dispatch('openModal',modal_id : $modal_id);
    }

    public function updatedCurriculumId($curriculum_id){
        $this->curriculum_id = $curriculum_id;
        $this->getCurriculum();
    }

    public function getCurriculum(){
         $this->curriculum = DB::table('curriculum_subjects as cs')
            ->select(
                'cs.id',
                's.subject_id' ,
                's.subject_code' ,
                's.description',
                's.prerequisite_subject_id' ,
                'sm.semester',
                'sm.id as semester_id',
                'yl.year_level',
                'yl.id as year_level_id',
                's.lecture_unit',
                's.laboratory_unit' ,
            )
            ->where('curriculum_id','=',$this->curriculum_id)
            ->leftjoin('subjects as s','s.id','cs.subject_id')
            ->leftjoin('year_levels as yl','yl.id','cs.year_level_id')
            ->leftjoin('semesters as sm','sm.id','cs.semester_id')
            ->orderBy('yl.year_level') 
            ->orderBy('yl.year_level') 
            ->orderBy('sm.semester')    
            ->get()
            ->toArray();
    }

    public function gradeLists($id,$modal_id){
        $this->grades = DB::table('lab_lec_grades as llg')
            ->selectRaw('
                cl.id as schedule_id,
                SUM(llg.grade) as total_grade,
                SUM(llg.grade / llg.sub_weight) as normalized_total,
                ((SUM(llg.grade / llg.sub_weight) * 100)) as calculated_grade,
                llg.other,
                s.id as s_id,
                s.lecture_unit,
                s.laboratory_unit,
                sm.semester,
                s.subject_id, 
                s.subject_code,
                cl.date_created,
                cl.is_lec,
                CONCAT(sy.year_start," - ",sy.year_end) as school_year
            ')
            ->join('schedulings as cl', 'cl.id', '=', 'llg.schedule_id')
            ->join('schedules as sh', 'sh.id', '=', 'cl.schedule_id')
            ->join('subjects as s', 's.id', '=', 'sh.subject_id')
            ->join('school_years as sy', 'sy.id', '=', 'cl.school_year_id')
            ->join('semesters as sm', 'sm.id', '=', 'cl.semester_id')
            ->where('llg.student_id', $id)
            ->groupBy(
                'cl.id',               // schedule_id
                'llg.other', 
                's.lecture_unit', 
                's.laboratory_unit',
                'sm.semester',
                's.subject_id', 
                's.subject_code',
                'cl.date_created',
                'sy.year_start',
                'sy.year_end'
            )
            ->orderBy('s.id', 'asc')
            ->orderBy('cl.date_created', 'asc')
            ->get()
            ->toArray();

        // dd($this->grades);

        $this->equivalent_grade = DB::table('point_grade_equivalent')
            ->get()
            ->toArray();
        $this->dispatch('openModal',modal_id : $modal_id);
        
    }

    public function rules(){
        return [
        'detail.new_password' => [
        'required',
            Password::min(8)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised(),
            ],
        'detail.confirm_password' => 'required|same:detail.new_password',
        ];
    }
    protected $messages = [
        'detail.new_password.required' => 'The new password field is required.',
        'detail.new_password.min' => 'The new password must be at least 8 characters.',
        'detail.new_password.mixed_case' => 'The new password must contain both uppercase and lowercase letters.',
        'detail.new_password.letters' => 'The new password must include at least one letter.',
        'detail.new_password.numbers' => 'The new password must include at least one number.',
        'detail.new_password.symbols' => 'The new password must include at least one special character.',
        'detail.new_password.uncompromised' => 'This new password has appeared in a data leak. Please choose a different password.',
        
        'detail.confirm_password.required_with' => 'Please confirm your password.',
        'detail.confirm_password.required' => 'The confirm password field is required.',
        'detail.confirm_password.same' => 'The password confirmation does not match.',
    ];

    public function change_password($id,$modal_id){
        $this->detail = [
            'user_id'=> $id,
            'new_password' => NULL,
            'confirm_password' => NULL,
        ];
        $this->dispatch('openModal',modal_id:$modal_id);
    }

    public function save_password($modal_id){
        $this->validate();

        $res = DB::table('users')
            ->where('id','=',$this->detail['user_id'])
            ->update([
                'password' => Hash::make($this->detail['new_password'])
            ]);

        if($res){
            $this->dispatch('notifySuccess', 
            'Updated successfully!',
                '');
        }
         $this->dispatch('closeModal',modal_id:$modal_id);
    }

     

    public function downloadTemplate(){
        $headers = $this->import['default_import_headers'];

        $data = [];
        $title = 'Student';

        $this->dispatch('swalSuccess', ['message' => 'Successfully downloaded file!']);
        return Excel::download(new MultiSheetExport([
            new ExportTemplate( $data, $headers,$title),
            new ExportTemplate( 
                DB::table('year_levels')
                    ->select('id','year_level')
                    ->get()
                    ->toArray()
                ,array_map('ucfirst', ['ID','Year Level']),"Year Levels" ),
            new ExportTemplate( 
                DB::table('colleges')
                    ->select('id','name')
                    ->get()
                    ->toArray()
                ,array_map('ucfirst', ['ID','College']),"Colleges" ),
            new ExportTemplate( 
                DB::table('departments')
                    ->select('id','name')
                    ->get()
                    ->toArray()
                ,array_map('ucfirst', ['ID','Department']),"Departments" ),
        ]), $title.' Import Template.xlsx');
    }


    public function import_rules(){
        return [
            'detail.college_id' => 'required|exists:colleges,id',
            'detail.department_id' => 'required|exists:departments,id',
            'detail.year_level_id' => 'required|exists:year_levels,id',
            'detail.code' => 'required|string|max:100|unique:students,code',
            'detail.email' => [
                'required',
                'email',
                'unique:users,email',
                'regex:/^[a-zA-Z0-9._%+-]+@wmsu\.edu\.ph$/'
            ],
            'detail.first_name' => 'required|string|max:255',
            'detail.middle_name' => 'nullable|string|max:255',
            'detail.last_name' => 'required|string|max:255',
            'detail.suffix' => 'nullable|string|max:255',
        ];
    }


    public function messages(){
        return [
            'detail.college_id.required' => 'The college is required.',
            'detail.college_id.exists' => 'The selected college does not exist.',
            'detail.department_id.required' => 'The department is required.',
            'detail.department_id.exists' => 'The selected department does not exist.',
            'detail.year_level_id.required' => 'The year level is required.',
            'detail.year_level_id.exists' => 'The selected year level does not exist.',
            'detail.code.required' => 'The student code is required.',
            'detail.code.unique' => 'This code is already taken.',
            'detail.first_name.required' => 'First name is required.',
            'detail.last_name.required' => 'Last name is required.',
            'detail.email.email' => 'The email must be a valid email address.',
            'detail.email.required' => 'The email is required.',
            'detail.email.unique' => 'The email has already been taken.',
            'detail.email.regex' => 'The email must be @wmsu.edu.ph domain.',
            'detail.confirm_password' => 'required|same:detail.password',
            'detail.password.required' => 'The password field is required.',
            'detail.password.min' => 'The password must be at least 8 characters.',
            'detail.password.mixed_case' => 'The password must contain both uppercase and lowercase letters.',
            'detail.password.letters' => 'The password must include at least one letter.',
            'detail.password.numbers' => 'The password must include at least one number.',
            'detail.password.symbols' => 'The password must include at least one special character.',
            'detail.password.uncompromised' => 'This password has appeared in a data leak. Please choose a different password.',
            
            'detail.confirm_password.required_with' => 'Please confirm your password.',
            'detail.confirm_password.required' => 'The confirm password field is required.',
            'detail.confirm_password.same' => 'The password confirmation does not match.',
        ];
    }

    public function openImportModal($modal_id){
        $this->excel_file_input = NULL;
        self::resetImportDetails();
         $this->dispatch('openModal',modal_id:$modal_id);


    }

    public function resetImportDetails(){
        $this->import['sheets'] = NULL;
        $this->import['import_headers'] = NULL;
        $this->import['total_inserts'] = NULL;
        $this->import['total_valid_inserts'] = NULL;
        $this->import['valid_insert_rows_arr'] = [];
        $this->import['comments'] = [];
        
    }


    public function updatedExcelFileInput($value){
        self::resetImportDetails();
        // Make sure it is xlsx
        $this->validateOnly('excel_file_input', [
            'excel_file_input' => 'required|file|mimes:xlsx,xls'
        ]);

        $importer = new ExcelParser($value);
        $this->import['sheets'] = $importer->getSheets();
        $import_sheet = $this->import['sheets'][0];
        $import_sheet_data = $import_sheet['data'];

        $headers = array_map(fn($h) => strtolower(trim($h)), $import_sheet_data[0]); // Assuming $sheet[0] is the header row
        $fieldMap = [];

        $this->import['import_headers'] = $headers;
        foreach ($headers as $index => $header) {
            if (isset($this->import['headerToFieldMap'][$header])) {
                $fieldMap[$index] = $this->import['headerToFieldMap'][$header];
            }
        }

        // check if there is rows to add
        if (count($import_sheet['data']) <= 1) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'excel_file_input' => 'The imported file must contain at least one data row.',
            ]);
        }

        // check if headers are the same with import template
        $normalizedHeaders = array_map('strtolower', $headers);
        $normalizedDefaults = array_map('strtolower', $this->import['default_import_headers']);
        $diff1 = array_diff($normalizedHeaders, $normalizedDefaults);
        $diff2 = array_diff($normalizedDefaults, $normalizedHeaders);
        if (count($diff1) || count($diff2)) {
            foreach ($diff1 as $key => $value) {
                if(strlen($value)){
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'excel_file_input' => 'Headers are not the same, please download the import template.',
                    ]);
                }
            }
            foreach ($diff2 as $key => $value) {
                if(strlen($value)){
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'excel_file_input' => 'Headers are not the same, please download the import template.',
                    ]);
                }
            }
           
        }
        
        $flippedHeaderToFieldMap = array_flip($this->import['headerToFieldMap']);

        $row_error_count = 0;
        $this->import['valid_insert_rows_arr'] = [];
        $this->import['total_valid_inserts'] = 0;
        $this->import['total_inserts'] = 0;
        $this->import['comments'] = [];
        foreach ($import_sheet_data as $rowIndex => $row) {
            if($rowIndex == 0){
                continue;
            }
            $is_valid_row = false;
            foreach ($row as $key => $value) {
                if(isset($value)){
                    $is_valid_row = true; 
                }
            }
            if(!$is_valid_row){
                continue; // skip if the entire row is unpopulated 
            }
            $this->import['total_inserts']++;
            $formatted = ['detail' => []];

            foreach ($fieldMap as $colIndex => $field) {
                // Assign the value from the row to the 'detail' field in the $formatted array
                $formatted['detail'][$field] = $row[$colIndex] ?? null;
            }
            // Now validate the data
            $validator = Validator::make($formatted, $this->import_rules(), $this->messages());
            
            if ($validator->fails()) {
                $row_error_count++;  // Increment error count for this row
                
                // Get the validation errors
                $errors = $validator->errors()->toArray();
                
                // Loop through the errors to get the column index and the corresponding error message
                foreach ($errors as $fieldName => $errorMessages) {
                    $field_name_index = substr($fieldName, strrpos($fieldName, '.') + 1);
                    $import_column_key =  $flippedHeaderToFieldMap[substr($fieldName, strrpos($fieldName, '.') + 1)];
                    $column_index = array_search($import_column_key, $headers);
                    $rowIndex;
                    array_push($this->import['comments'],[
                        'row'=>$rowIndex,
                        'column'=>$column_index,
                        'error_message'=>$errorMessages
                    ]);
                }
            }else{
          
                $is_valid_insert = true;
                // get indexes
                $unique_check_arr = [
                    ['columnIndex'=> self::getIndexColumn($fieldMap,'email'),'error_message'=> "Emai; is duplicate to row "],
                    ['columnIndex'=> self::getIndexColumn($fieldMap,'code'),'error_message'=> "ID is duplicate to row "],
                ];
                // loop within the sheet, then ensure that unique row is unique.
                foreach ($import_sheet_data as $RowIdx => $sheet_row) {
                    if($RowIdx >0){
                        if($rowIndex != $RowIdx){
                            $ivr = false;
                            foreach ($sheet_row as $k => $v) {
                                if(isset($v)){
                                    $ivr = true; 
                                }
                            }
                            if(!$ivr){
                                continue; // skip if the entire row is unpopulated 
                            }
                            foreach($unique_check_arr as $uca_key =>  $uca_value){
                                if($sheet_row[$uca_value['columnIndex']] == $row[$uca_value['columnIndex']]){
                                    $is_valid_insert = false;
                                    array_push($this->import['comments'],[
                                        'row'=>$rowIndex,
                                        'column'=>$uca_value['columnIndex'],
                                        'error_message'=> $uca_value['error_message'].($RowIdx+1)
                                    ]);
                                }
                            }
                        }
                    }
                }
                if($is_valid_insert){
                    $this->import['total_valid_inserts']++;
                    array_push($this->import['valid_insert_rows_arr'],$rowIndex);
                }
            }
        }
    }


    public function getIndexColumn($fieldMap,$column){
        foreach($fieldMap as $key => $value){
            if($value == $column){
                return $key;
            }
        }
    }

    public function downloadErrorSheet(){
        $title = 'Students ';
        $export_data = [];
        foreach($this->import['sheets'] as $key =>$sheet){
            if($key == 0){
                $header = $sheet['data'][0];
                array_shift($sheet['data']);
                array_push($export_data,new ExportTemplate( $sheet['data'], $header, $sheet['sheet_title'] ,$this->import['comments']));
            }else{
                $header = $sheet['data'][0];
                array_shift($sheet['data']);
                array_push($export_data,new ExportTemplate( $sheet['data'], $header, $sheet['sheet_title']));
            }
        }
        $this->dispatch('notifySuccess', 
            'Downloaded successfully!',
                '');
        
        return Excel::download(new MultiSheetExport($export_data), $title.' Import Template (with comments).xlsx');
    }


     public function importSheet($modal_id){
        $flippedHeaderToFieldMap = array_flip($this->import['headerToFieldMap']);
        $index = 0;

        foreach($this->import['valid_insert_rows_arr'] as $key => $value){
            $item = $this->import['sheets'][0]['data'][$value];
            $record = [];
            foreach($this->import['headerToFieldMap'] as $h_key =>$h_value){
                $index = array_search($h_key, $this->import['import_headers']);
                $record[$h_value] = $item[$index];
            }
            self::insertRecord($record);
        }
        $this->dispatch('closeModal',modal_id:$modal_id);
        $this->dispatch('notifySuccess', 
            'Successfully imported '.count($this->import['valid_insert_rows_arr']).' rows!',
                '');

    }

    public function insertRecord($record){
        try {
            $res = DB::table('users')
            ->insert([
                'first_name'=> $record['first_name'],
                'middle_name'=> $record['middle_name'],
                'last_name'=> $record['last_name'],
                'suffix'=> $record['suffix'],
                'email'=> $record['email'],
                'password'=> Hash::make("Drusha01@2"),
                'admin_type'=> 3,
            ]);
        if($res){
            $user = DB::table('users')
                ->where('email','=',$record['email'])
                ->first();
            if(DB::table('students')->insert([
                'user_id'=> $user->id,
                'college_id'=> $record['college_id'],
                'department_id'=> $record['department_id'],
                'year_level_id'=> $record['year_level_id'],
                'code'=> $record['code'],
                'email'=> $record['email'],
                'first_name'=> $record['first_name'],
                'middle_name'=> $record['middle_name'],
                'last_name'=> $record['last_name'],
                'suffix'=> $record['suffix'],
            ])){
            }
        }
        } catch (\Exception $e) {
            echo $e->getMessage();
        } 
    }
}
