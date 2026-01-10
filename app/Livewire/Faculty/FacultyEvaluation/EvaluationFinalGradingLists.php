<?php

namespace App\Livewire\Faculty\FacultyEvaluation;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Collection;


class EvaluationFinalGradingLists extends Component
{

    use WithPagination;
    public $title = "Evaluation";

    public $route = "evaluation";

    public $colleges = [];

    public $students = [];

    public $departments = [];

    public $school_years = [];

    public $semesters = [];

    public $subjects = [];

    public $year_levels = [];

    public $terms = [];
    public $laboratory_grade = [];

    public $school_work_types = [];

    public $school_works = [];

    public $temp_terms = [];
    public $schedule = NULL;
    public $final_grades = [];
    public $filters = [
        'search' => '',
        'remarks' => '',
    ];
    public $detail = [
        'student_id' => NULL,
        'schedule_id' => NULL,
        'term_id' => NULL,
    ];

    public $term_weight = [
        'term_id' => NULL,
        'weight' => NULL,
        'lecture_weight' => NULL,
        'laboratory_weight' => NULL,

    ];

    public $school_work_type = [
        'id' => NULL,
        'schedule_id' => NULL,
        'term_id' => NULL,
        'lab_lec_id' => NULL,
        'school_work_type' => NULL,
        'weight' => NULL,
        'number_order' => NULL,
    ];

    public $school_work = [
        'id' => NULL,
        'schedule_id' => NULL,
        'term_id' => NULL,
        'school_work_name' => NULL,
        'school_work_type_id' => NULL,
        'max_score' => NULL,
        'schedule_date' => NULL,
        'number_order' => NULL,
    ];

    public $current_school_work_type = [];

    public $dayMap = [
        'M' => Carbon::MONDAY,
        'T' => Carbon::TUESDAY,
        'W' => Carbon::WEDNESDAY,
        'R' => Carbon::THURSDAY, // Common for Thursday
        'F' => Carbon::FRIDAY,
        'S' => Carbon::SATURDAY,
        'U' => Carbon::SUNDAY,   // or 'N' if you're using ISO-8601 (1–7)
    ];
    public $customDayMap = [
        'Sun' => 0,
        'M' => 1,
        'T' => 2,
        'W' => 3,
        'TH' => 4,
        'F' => 5,
        'S' => 6,
    ];

    public $student_scores = [];
    public $school_year;
    public $semester;
    public $school_year_id;
    public $semester_id;

    public $laboratory_schedules = [];

    public $laboratory_terms = [];
    public $laboratory_schedules_weight = [];

    public $lecture_weight = NULL;

    public $point_grade_equivalent = [];

    public function mount($school_year, $semester, $schedule_id)
    {
        $this->school_year = $school_year;
        $this->semester = $semester;
        $this->school_year_id = DB::table('school_years')->where(DB::raw('concat(year_start,"-",year_end)'), '=', $school_year)->first()->id;
        $this->semester_id = DB::table('semesters')->where(DB::raw('semester'), '=', $semester)->first()->id;

        $this->detail['schedule_id'] = $schedule_id;
        $this->colleges = DB::table('colleges')
            ->where('is_active', '=', 1)
            ->get()
            ->toArray();

        $this->departments = DB::table('departments')
            ->where('is_active', '=', 1)
            ->get()
            ->toArray();

        self::getDetails();
        if ($this->schedule == null) {
            return redirect(route('my-schedule-school-years-lists'));
        }
        self::getlaboratory_schedules();
        self::terms($this->detail['schedule_id']);
        self::getLabLectureWeight();

        if (count($this->terms)) {
            $this->detail['term_id'] = $this->terms[0]->id;
        }
        self::school_work_types($this->detail['schedule_id']);

        $this->term_weight['term_id'] = $this->detail['term_id'];
        self::fetch_terms();

        self::initilize_attendance();
        self::pointGradeEquivalent();
    }

    public function pointGradeEquivalent()
    {
        $this->point_grade_equivalent = DB::table('point_grade_equivalent')->get()->toArray();
    }

    public function initilize_attendance()
    {
        $curriculum_detail = DB::table('schedulings as cl')
            ->select(
                'sh.id as schedule_id',
                'sh.faculty_id',
                'sh.room_id',
                'sh.code',
                'sh.schedule_from',
                'sh.schedule_to',
                'sh.day',
                'sh.is_lec',
                'sm.semester',
                'sm.date_start_date',
                'sm.date_start_month',
                'sm.date_end_date',
                'sm.date_end_month',
                'sy.year_start',
                'sy.year_end',
            )
            ->join('schedules as sh', 'sh.id', 'cl.schedule_id')
            ->join('semesters as sm', 'sm.id', 'cl.semester_id')
            ->join('school_years as sy', 'sy.id', 'cl.school_year_id')
            ->first();

        $start_semester_date = date('Y-m-d', strtotime($curriculum_detail->date_start_date . '-' . $curriculum_detail->date_start_month . '-' . $curriculum_detail->year_start));
        $end_semester_date = date('Y-m-d', strtotime($curriculum_detail->date_end_date . '-' . $curriculum_detail->date_end_month . '-' . $curriculum_detail->year_end));
        $selectedDays = json_decode($curriculum_detail->day);

        $start = Carbon::parse($start_semester_date);
        $end = Carbon::parse($end_semester_date);
        $targetWeekdays = collect($selectedDays)
            ->map(fn($code) => $this->customDayMap[$code])
            ->filter(); // Remove any invalid mappings

        // Generate matching dates
        $matchingDates = collect();
        $current = $start->copy();


        while ($current <= $end) {
            if ($targetWeekdays->contains($current->dayOfWeek)) {
                $matchingDates->push($current->toDateString());
            }
            $current->addDay();
        }

        $this->current_school_work_type = DB::table('school_works_types')
            ->where('school_work_type', '=', 'Attendance')
            ->where('schedule_id', '=', $this->detail['schedule_id'])
            ->where('term_id', '=', $this->detail['term_id'])
            ->first();

        $attendance_dates = DB::table('school_works')
            ->where('school_work_type_id', '=', $this->current_school_work_type->id)
            ->where('schedule_id', '=', $this->detail['schedule_id'])
            ->where('term_id', '=', $this->detail['term_id'])
            ->get()
            ->toArray();
        if (count($attendance_dates) <= 0) {

            foreach ($matchingDates as $key => $value) {
                $attendance_name = 'Attendance for ' . Carbon::parse($value)->format('F, d Y');
                if (
                    !DB::table('school_works')
                        ->where('school_work_name', '=', $attendance_name)
                        ->where('schedule_id', '=', $this->detail['schedule_id'])
                        ->where('term_id', '=', $this->detail['term_id'])
                        ->first()
                ) {
                    DB::table('school_works')
                        ->insert([
                            'id' => NULL,
                            'schedule_id' => $this->detail['schedule_id'],
                            'term_id' => $this->detail['term_id'],
                            'school_work_name' => $attendance_name,
                            'school_work_type_id' => $this->current_school_work_type->id,
                            'max_score' => 1,
                            'schedule_date' => $value,
                            'number_order' => NULL,
                        ]);
                }
            }
        }

    }

    public function UpdatedDetailTermId($term_id)
    {
        $this->detail['term_id'] = $term_id;
        self::school_work_types($this->detail['schedule_id']);
        $this->term_weight['term_id'] = $this->detail['term_id'];
        self::fetch_terms();
    }

    public function render()
    {
        $table_data = DB::table('enrolled_students as es')
            ->select(
                's.id',
                's.college_id',
                'department_id',
                'year_level',
                DB::raw('CONCAT_WS(" ", s.first_name, s.middle_name, s.last_name, s.suffix) AS fullname'),
                's.code',
                'first_name',
                'middle_name',
                'last_name',
                'suffix',
                'email',
                's.is_active',
                'c.name as college',
                'd.name as department',
                'c.code as college_code',
                'd.code as department_code',
                'yl.year_level'
            )
            ->leftJoin('students as s', 's.id', 'es.student_id')
            ->leftJoin('colleges as c', 'c.id', 's.college_id')
            ->leftJoin('departments as d', 'd.id', 's.department_id')
            ->leftJoin('year_levels as yl', 'yl.id', 's.year_level_id')
            ->where('es.schedule_id', '=', $this->detail['schedule_id']);


        if (!empty($this->filters['search'])) {
            $table_data
                ->where('s.code', 'like', '%' . $this->filters['search'] . '%')
                ->orwhere('s.email', 'like', '%' . $this->filters['search'] . '%')
                ->orwhere(DB::raw('CONCAT_WS(" ", s.first_name, s.middle_name, s.last_name, s.suffix)'), 'like', '%' . $this->filters['search'] . '%');
        }
        if (!empty($this->filters['remarks'])) {
            $table_data->whereIn('s.id', function ($query) {
                $query->select('fg.student_id')
                    ->from('final_grades as fg')
                    ->where('fg.schedule_id', '=', $this->detail['schedule_id'])
                    ->where('fg.remarks', '=', $this->filters['remarks']);
            });
        }
        $table_data = $table_data
            ->orderBy('s.is_active', 'desc')
            ->orderBy('s.id', 'desc')
            ->paginate(10)->withPath(url()->current());

        $student_id = $table_data->pluck('id');

        foreach ($student_id as $v_key => $v_value) {
            foreach ($this->school_work_types as $key => $value) {
                $school_works = DB::table('school_works_types as swt')
                    ->select(
                        'swt.id as school_work_type_id',
                        'sw.id',
                        'sw.max_score',
                        'score',
                        'sws.id as score_id',
                    )
                    ->leftjoin('school_works as sw', 'sw.school_work_type_id', 'swt.id')
                    ->leftjoin('school_work_scores as sws', 'sws.school_work_id', 'sw.id')
                    ->where('swt.schedule_id', '=', $this->detail['schedule_id'])
                    ->where('swt.term_id', '=', $this->detail['term_id'])
                    ->where('swt.id', '=', $value->id)
                    ->where('sws.student_id', '=', $v_value)
                    ->first();

                $student_school_works = DB::table('school_works as sw')
                    ->select(
                        'sw.id',
                        'sw.schedule_id',
                        'sw.term_id',
                        'school_work_name',
                        'school_work_type_id',
                        'sw.max_score',
                        'schedule_date',
                        'student_id',
                        'score',
                        'school_work_id'

                    )
                    ->leftjoin('school_work_scores as sws', 'sws.school_work_id', 'sw.id')
                    ->where('sw.schedule_id', '=', $this->detail['schedule_id'])
                    ->where('sw.term_id', '=', $this->detail['term_id'])
                    ->where('sw.school_work_type_id', '=', $value->id)
                    ->get()
                    ->toArray();

                foreach ($student_school_works as $ssw_key => $ssw_value) {
                    if (
                        !DB::table('school_work_scores as sws')
                            ->where('sws.schedule_id', '=', $this->detail['schedule_id'])
                            ->where('sws.term_id', '=', $this->detail['term_id'])
                            ->where('sws.student_id', '=', $v_value)
                            ->where('sws.school_work_id', '=', $ssw_value->id)
                            ->first()
                    ) {
                        DB::table('school_work_scores')
                            ->insert([
                                'id' => NULL,
                                'schedule_id' => $this->detail['schedule_id'],
                                'student_id' => $v_value,
                                'term_id' => $this->detail['term_id'],
                                'school_work_id' => $ssw_value->id,
                                'score' => NULL,
                                'max_score' => $ssw_value->max_score,
                            ]);
                    }
                }
            }
        }

        self::fetch_terms();
        self::student_scores($student_id);

        self::getFinalGrades($student_id);

        return view('livewire.faculty.faculty-evaluation.evaluation-final-grading-lists', [
            'table_data' => $table_data
        ])
            ->layout('components.layouts.admin-app', [
                'title' => $this->title
            ]);
    }

    public function terms($schedule_id)
    {
        $this->terms = DB::table('terms')
            ->where('schedule_id', '=', $schedule_id)
            ->orderBy('term_order', 'asc')
            ->get()
            ->toArray();

        if (count($this->laboratory_schedules)) {
            $this->laboratory_terms = DB::table('terms')
                ->where('schedule_id', '=', $this->laboratory_schedules[0]->id)
                ->orderBy('term_order', 'asc')
                ->get()
                ->toArray();
        }
        if (count($this->terms) <= 0) {
            $midterm_id = DB::table('terms')
                ->insertGetId([
                    'id' => NULL,
                    'schedule_id' => $schedule_id,
                    'term_name' => 'Midterm',
                    'weight' => 40.0,
                    'term_order' => 1,
                ]);

            $finalterm_id = DB::table('terms')
                ->insertGetId([
                    'id' => NULL,
                    'schedule_id' => $schedule_id,
                    'term_name' => 'Finalterm',
                    'weight' => 60.0,
                    'term_order' => 2,
                ]);
            // lab lec
            DB::table(table: 'lab_lec')
                ->insertGetId([
                    'id' => NULL,
                    'schedule_id' => $schedule_id,
                    'term_id' => $midterm_id,
                    'sub_weight' => 50.0,
                    'is_lecture' => $this->schedule->is_lec,
                ]);

            DB::table(table: 'lab_lec')
                ->insertGetId([
                    'id' => NULL,
                    'schedule_id' => $schedule_id,
                    'term_id' => $finalterm_id,
                    'sub_weight' => 50.0,
                    'is_lecture' => $this->schedule->is_lec,
                ]);


            DB::table('school_works_types')
                ->insert([
                    'id' => NULL,
                    'schedule_id' => $schedule_id,
                    'term_id' => $midterm_id,
                    'lab_lec_id' => NULL,
                    'school_work_type' => 'Attendance',
                    'weight' => 0,
                    'number_order' => 1,
                ]);
            DB::table('school_works_types')
                ->insert([
                    'id' => NULL,
                    'schedule_id' => $schedule_id,
                    'term_id' => $finalterm_id,
                    'lab_lec_id' => NULL,
                    'school_work_type' => 'Attendance',
                    'weight' => 0,
                    'number_order' => 1,
                ]);
        }
    }

    public $school_work_type_value = [];
    public function school_work_types($schedule_id)
    {
        $this->school_work_types = DB::table('school_works_types')
            ->where('schedule_id', '=', $schedule_id)
            ->where('term_id', '=', $this->detail['term_id'])
            ->orderBy('number_order', 'asc')
            ->get()
            ->toArray();

        $this->school_work_type_value = [];
        foreach ($this->school_work_types as $key => $value) {
            array_push($this->school_work_type_value, ['val' => $value->weight]);
        }
    }


    public function open_school_work_types_modal($modal_id)
    {
        self::school_work_types($this->detail['schedule_id']);

        $total = DB::table('school_works_types')
            ->select(DB::raw('count(*) as total'))
            ->where('schedule_id', '=', $this->detail['schedule_id'])
            ->where('term_id', '=', $this->detail['term_id'])
            ->first();

        $this->school_work_type = [
            'id' => NULL,
            'schedule_id' => $this->detail['schedule_id'],
            'term_id' => $this->detail['term_id'],
            'lab_lec_id' => NULL,
            'school_work_type' => NULL,
            'weight' => 0,
            'number_order' => (intval($total->total) + 1),
        ];

        $this->dispatch('openModal', modal_id: $modal_id);
    }

    public function viewDetails($modal_id)
    {
        self::getDetails();
        $this->dispatch('openModal', modal_id: $modal_id);
    }

    public function getDetails()
    {
        $this->schedule = DB::table('schedulings as cl')
            ->select(
                'cl.id',
                's.college_id',
                's.department_id',
                's.description',
                's.prerequisite_subject_id',
                'c.name as college_name',
                'd.name as department_name',
                'c.code as college_code',
                'd.code as department_code',
                'pr.subject_id as prerequisite_subject_id',
                'pr.subject_code as prerequisite_subject_code',
                'r.code as room_code',
                'r.name as room_name',
                's.is_active',
                'sh.schedule_from',
                'sh.schedule_to',
                'sh.day',
                'sh.is_lec',
                'sh.subject_id',
                'cl.room_id',
                'cl.schedule_id',
                'cl.faculty_id',
                DB::raw('CONCAT(sy.year_start," - ",sy.year_end) as school_year'),
                DB::raw('CONCAT(c.code," ",c.name) as college'),
                DB::raw('CONCAT(d.code," ",d.name) as department'),
                DB::raw('CONCAT_WS(" ", u.first_name, u.middle_name, u.last_name, u.suffix) AS faculty_fullname'),
                DB::raw('sm.semester'),
                DB::raw('yl.year_level'),
                DB::raw('CONCAT(s.subject_id," - ",s.subject_code) as subject'),
                DB::raw("CONCAT(DATE_FORMAT(sh.schedule_from, '%h:%i %p'), ' ', DATE_FORMAT(sh.schedule_to, '%h:%i %p')) as schedule"),
                's.lecture_unit',
                's.laboratory_unit',
                DB::raw('CONCAT(r.code," ",r.name) as room'),

            )
            ->leftJoin('school_years as sy', 'sy.id', 'cl.school_year_id')
            ->leftJoin('rooms as r', 'r.id', 'cl.room_id')
            ->leftJoin('schedules as sh', 'sh.id', 'cl.schedule_id')
            ->leftJoin('subjects as s', 's.id', 'sh.subject_id')
            ->leftJoin('faculty as f', 'f.id', 'cl.faculty_id')
            ->leftJoin('users as u', 'u.id', 'f.user_id')
            ->leftJoin('colleges as c', 'c.id', 's.college_id')
            ->leftJoin('departments as d', 'd.id', 's.department_id')
            ->leftjoin('subjects as pr', 'pr.id', 's.prerequisite_subject_id')
            ->leftjoin('semesters as sm', 'sm.id', 'cl.semester_id')
            ->leftjoin('year_levels as yl', 'yl.id', 'cl.year_level_id')
            ->where('cl.id', '=', $this->detail['schedule_id'])
            ->first();
    }

    public function add_school_work_type()
    {
        $this->validate(
            [
                'school_work_type.school_work_type' => 'required|string',
                'school_work_type.weight' => 'required|numeric|min:0.1',
            ],
            [
                'school_work_type.school_work_type.required' => 'The school work type is required.',
                'school_work_type.school_work_type.string' => 'The school work type must be a valid string.',
                'school_work_type.weight.required' => 'The weight is required.',
                'school_work_type.weight.numeric' => 'The weight must be a valid number.',
                'school_work_type.weight.min' => 'The weight must be greater than zero.',
            ]
        );

        if (
            DB::table('school_works_types')
                ->where('school_work_type', '=', $this->school_work_type['school_work_type'])
                ->where('schedule_id', '=', $this->detail['schedule_id'])
                ->where('term_id', '=', $this->detail['term_id'])
                ->first()
        ) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'school_work_type.school_work_type' => 'School work type exists',
            ]);
        }

        $weight = DB::table('school_works_types')
            ->select(DB::raw('sum(weight) as total_weight'))
            ->where('schedule_id', '=', $this->detail['schedule_id'])
            ->where('term_id', '=', $this->detail['term_id'])
            ->first();

        if ($weight->total_weight + $this->school_work_type['weight'] > 100) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'school_work_type.weight' => 'The weight exceeds ' . (100 - $weight->total_weight),
            ]);
        }

        $res = DB::table('school_works_types')
            ->insert($this->school_work_type);

        if ($res) {
            $this->dispatch(
                'notifySuccess',
                'Added successfully!',
                ''
            );
            $total = DB::table('school_works_types')
                ->select(DB::raw('count(*) as total'))
                ->where('schedule_id', '=', $this->detail['schedule_id'])
                ->where('term_id', '=', $this->detail['term_id'])
                ->first();

            $this->school_work_type = [
                'id' => NULL,
                'schedule_id' => $this->detail['schedule_id'],
                'term_id' => $this->detail['term_id'],
                'lab_lec_id' => NULL,
                'school_work_type' => NULL,
                'weight' => 0,
                'number_order' => (intval($total->total) + 1),
            ];
        }
        self::school_work_types($this->detail['schedule_id']);
    }

    public function deleteSchoolWorkType($id)
    {
        $res = DB::table('school_works_types')
            ->where('id', '=', $id)
            ->delete();
        if ($res) {
            $this->dispatch(
                'notifySuccess',
                'Deleted successfully!',
                ''
            );
        }
        self::school_work_types($this->detail['schedule_id']);

    }

    public function getlaboratory_schedules()
    {
        $this->laboratory_schedules = DB::table('schedulings as cl')
            ->select(
                DB::raw('CONCAT_WS(" ", u.first_name, u.middle_name, u.last_name, u.suffix) AS fullname'),
                DB::raw("DATE_FORMAT(sh.schedule_from, '%h:%i %p') as schedule_from"),
                DB::raw("DATE_FORMAT(sh.schedule_to, '%h:%i %p') as schedule_to"),
                'sh.day',
                DB::raw('sum(ll.sub_weight) as sum'),
                DB::raw('sum(ll.sub_weight)/count(*) as ave'),
                DB::raw('count(*) as count'),
                'cl.id'
            )
            ->join('schedules as sh', 'cl.schedule_id', 'sh.id')
            ->join('subjects as s', 'sh.subject_id', 's.id')
            ->join('lab_lec as ll', 'll.schedule_id', 'cl.id')
            ->join('faculty as f', 'cl.faculty_id', 'f.id')
            ->leftJoin('users as u', 'u.id', 'f.user_id')
            ->where('cl.is_lec', '=', 0)
            ->where('cl.school_year_id', '=', $this->school_year_id)
            ->where('cl.semester_id', '=', $this->semester_id)
            ->where('cl.college_id', '=', $this->schedule->college_id)
            ->where('cl.department_id', '=', $this->schedule->department_id)
            ->where('sh.subject_id', '=', $this->schedule->subject_id)
            ->groupBy(
                'u.first_name',
                'u.middle_name',
                'u.last_name',
                'u.suffix',
                'sh.day',
                'sh.schedule_from',
                'sh.schedule_to',
                'cl.id'
            )
            ->get()
            ->toArray();
    }

    public function open_lablect_weight($modal_id)
    {

        self::getLabLectureWeight();
        $this->dispatch('openModal', modal_id: $modal_id);

    }

    public function getLabLectureWeight()
    {
        $lecture_schedules = DB::table('schedulings as cl')
            ->select(
                DB::raw('CONCAT_WS(" ", u.first_name, u.middle_name, u.last_name, u.suffix) AS fullname'),
                DB::raw("DATE_FORMAT(sh.schedule_from, '%h:%i %p') as schedule_from"),
                DB::raw("DATE_FORMAT(sh.schedule_to, '%h:%i %p') as schedule_to"),
                'sh.day',
                DB::raw('sum(ll.sub_weight) as sum'),
                DB::raw('sum(ll.sub_weight)/count(*) as ave'),
                DB::raw('count(*) as count'),
                'cl.id'
            )
            ->join('schedules as sh', 'cl.schedule_id', 'sh.id')
            ->join('subjects as s', 'sh.subject_id', 's.id')
            ->where('cl.id', '=', $this->detail['schedule_id'])
            ->join('lab_lec as ll', 'll.schedule_id', 'cl.id')
            ->join('faculty as f', 'cl.faculty_id', 'f.id')
            ->leftJoin('users as u', 'u.id', 'f.user_id')
            ->groupBy(
                'u.first_name',
                'u.middle_name',
                'u.last_name',
                'u.suffix',
                'sh.day',
                'sh.schedule_from',
                'sh.schedule_to',
                'cl.id'
            )
            ->get()
            ->first();

        $this->lecture_weight = $lecture_schedules->ave;
        self::getlaboratory_schedules();

        $this->laboratory_schedules = (array) $this->laboratory_schedules;

        $this->laboratory_schedules_weight = [];
        foreach ($this->laboratory_schedules as $key => $value) {
            array_push(
                $this->laboratory_schedules_weight,
                [
                    'weight' => $value->ave,
                    'id' => $value->id
                ]
            );
        }
    }

    public function updateLabWeight($modal_id)
    {

        DB::table('lab_lec')
            ->where('schedule_id', '=', $this->detail['schedule_id'])
            ->update([
                'sub_weight' => $this->lecture_weight
            ]);

        foreach ($this->laboratory_schedules_weight as $key => $value) {
            DB::table('lab_lec')
                ->where('schedule_id', '=', $value['id'])
                ->update([
                    'sub_weight' => $value['weight']
                ]);
        }
        $this->dispatch(
            'notifySuccess',
            'Updated successfully!',
            ''
        );
        $this->dispatch('closeModal', modal_id: $modal_id);
    }

    public function updateLabLecGrades($lab_lec_id, $student_id, $var)
    {
        if (
            DB::table('lab_lec_grades')
                ->where('id', '=', $lab_lec_id)
                ->where('student_id', '=', $student_id)
                ->update([
                    'grade' => NULL,
                    'other' => $var,
                ])
        ) {
            $this->dispatch(
                'notifySuccess',
                'Updated successfully!',
                ''
            );
        }
    }

    public function updateSchoolWorkName($id, $newName)
    {
        DB::table('school_works')
            ->where('id', '=', $id)
            ->update(['school_work_name' => $newName]);

        self::school_works();
    }

    public function updateSchoolWorkDate($id, $newDate)
    {
        DB::table('school_works')
            ->where('id', '=', $id)
            ->update(['schedule_date' => $newDate]);

        self::school_works();
    }
    public function updateSchoolWorkScore($id, $newScore)
    {
        DB::table('school_works')
            ->where('id', '=', $id)
            ->update(['max_score' => $newScore]);

        self::school_works();
    }


    public function updateSchoolWorktype($id, $weight)
    {
        $total_weight = DB::table('school_works_types')
            ->select(DB::raw('sum(weight) as total_weight'))
            ->where('schedule_id', '=', $this->detail['schedule_id'])
            ->where('term_id', '=', $this->detail['term_id'])
            ->where('id', '<>', $id)
            ->first();

        if ($total_weight->total_weight + intval($weight) > 100) {
            $this->dispatch(
                'notifyWarning',
                'The weight exceeds ' . (100 - $total_weight->total_weight),
                ''
            );
            self::school_work_types($this->detail['schedule_id']);
            return;
        }

        $res = DB::table('school_works_types')
            ->where('id', '=', $id)
            ->update([
                'weight' => intval($weight)
            ]);
        if ($res) {
            $this->dispatch(
                'notifySuccess',
                'Updated successfully!',
                ''
            );
        }
        self::school_work_types($this->detail['schedule_id']);

    }

    public function open_school_work_modal($modal_id)
    {

        $total = DB::table('school_works')
            ->select(DB::raw('count(*) as total'))
            ->where('schedule_id', '=', $this->detail['schedule_id'])
            ->where('term_id', '=', $this->detail['term_id'])
            ->first();
        $this->school_work = [
            'id' => NULL,
            'schedule_id' => $this->detail['schedule_id'],
            'term_id' => $this->detail['term_id'],
            'school_work_name' => NULL,
            'school_work_type_id' => NULL,
            'max_score' => NULL,
            'schedule_date' => NULL,
            'number_order' => intval($total->total) + 1,
        ];

        self::school_works();
        $this->dispatch('openModal', modal_id: $modal_id);
    }

    public function add_school_work($modal_id)
    {
        $this->validate([
            'school_work.school_work_name' => 'required|string',
            'school_work.schedule_date' => 'required|date',
            'school_work.max_score' => 'required|numeric|min:1',
            'school_work.school_work_type_id' => 'required|exists:school_works_types,id',
        ], [
            'school_work.school_work_name.required' => 'The school work name is required.',
            'school_work.school_work_name.string' => 'The school work name must be a string.',
            'school_work.max_score.required' => 'The maximum score is required.',
            'school_work.max_score.numeric' => 'The maximum score must be a number.',
            'school_work.max_score.min' => 'The maximum score must be at least 1.',
            'school_work.schedule_date.required' => 'The schedule date is required.',
            'school_work.schedule_date.date' => 'The schedule date must be a valid date.',
            'school_work.school_work_type_id.required' => 'The school work type is required.',
            'school_work.school_work_type_id.exists' => 'The selected school work type is invalid.',
        ]);

        if (
            DB::table('school_works')
                ->where('school_work_name', '=', $this->school_work['school_work_name'])
                ->where('schedule_id', '=', $this->detail['schedule_id'])
                ->where('term_id', '=', $this->detail['term_id'])
                ->first()
        ) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'school_work.school_work_name' => 'School work exists',
            ]);
        }

        $res = DB::table('school_works')
            ->insert($this->school_work);
        if ($res) {
            $this->dispatch(
                'notifySuccess',
                'Added successfully!',
                ''
            );
            $total = DB::table('school_works')
                ->select(DB::raw('count(*) as total'))
                ->where('schedule_id', '=', $this->detail['schedule_id'])
                ->where('term_id', '=', $this->detail['term_id'])
                ->first();
            $this->school_work = [
                'id' => NULL,
                'schedule_id' => $this->detail['schedule_id'],
                'term_id' => $this->detail['term_id'],
                'school_work_name' => NULL,
                'school_work_type_id' => $this->school_work['school_work_type_id'],
                'max_score' => NULL,
                'schedule_date' => NULL,
                'number_order' => intval($total->total) + 1,
            ];
        }
        self::school_works();
    }

    public function UpdatedSchoolWorkSchoolWorkTypeId($school_work_type_id)
    {
        $this->school_work['school_work_type_id'] = $school_work_type_id;

        self::school_works();
    }

    public function deleteSchoolWork($id)
    {
        $res = DB::table('school_works')
            ->where('id', '=', $id)
            ->delete();
        if ($res) {
            $this->dispatch(
                'notifySuccess',
                'Deleted successfully!',
                ''
            );
        }
        self::school_works();
    }

    public function school_works()
    {
        $this->school_works = DB::table('school_works')
            ->where('schedule_id', '=', $this->detail['schedule_id'])
            ->where('term_id', '=', $this->detail['term_id'])
            ->where('school_work_type_id', '=', $this->school_work['school_work_type_id'])
            ->orderBy('number_order', 'asc')
            ->get()
            ->toArray();
    }

    public function student_scores($student_ids)
    {
        $this->student_scores = [];
        foreach ($student_ids as $v_key => $v_value) {
            $scores = [];
            foreach ($this->school_work_types as $key => $value) {
                $school_works = DB::table('school_works_types as swt')
                    ->select(
                        'swt.id as school_work_type_id',
                        'swt.weight',
                        'sw.id',
                        'sw.max_score',
                        'score',
                        'sws.id as score_id',
                    )
                    ->leftjoin('school_works as sw', 'sw.school_work_type_id', 'swt.id')
                    ->leftjoin('school_work_scores as sws', 'sws.school_work_id', 'sw.id')
                    ->where('swt.schedule_id', '=', $this->detail['schedule_id'])
                    ->where('swt.term_id', '=', $this->detail['term_id'])
                    ->where('swt.id', '=', $value->id)
                    ->where(function ($query) use ($v_value) {
                        $query->whereNull('sws.student_id') // if no score yet
                            ->orWhere('sws.student_id', $v_value);
                    })
                    // ->leftjoin('school_work_scores as sws','sws.school_work_id','sw.id')
                    ->orderBy('sw.number_order', 'asc')
                    ->get()
                    ->toArray();
                if ($school_works) {
                    foreach ($school_works as $s_key => $s_value) {
                        if ($s_value->id) {
                            array_push($scores, [
                                'score_id' => $s_value->score_id,
                                'schedule_id' => $this->detail['schedule_id'],
                                'student_id' => $v_value,
                                'term_id' => $this->detail['term_id'],
                                'school_work_id' => $s_value->id,
                                'school_work_type_id' => $s_value->school_work_type_id,
                                'weight' => $s_value->weight,
                                'key' => $key,
                                'score' => $s_value->score,
                                'max_score' => $s_value->max_score,
                            ]);
                        }
                    }
                    array_push($scores, [
                        'score_id' => NULL,
                        'schedule_id' => $this->detail['schedule_id'],
                        'term_id' => $this->detail['term_id'],
                        'student_id' => $v_value,
                        'weight' => $s_value->weight,
                        'school_work_id' => NULL,
                        'school_work_type_id' => NULL,
                        'key' => $key,
                        'score' => NULL,
                        'max_score' => NULL,
                    ]);
                }
            }
            array_push($this->student_scores, $scores);
        }
        $school_work_types = DB::table('school_works_types as swt')
            ->where('swt.schedule_id', '=', $this->detail['schedule_id'])
            ->where('swt.term_id', '=', $this->detail['term_id'])
            ->leftjoin('school_works as sw', 'sw.school_work_type_id', 'swt.id')
            ->orderBy('swt.number_order', 'asc')
            ->get()
            ->toArray();

    }

    public function updateScore(
        $score_id,
        $schedule_id,
        $student_id,
        $term_id,
        $school_work_id,
        $score,
        $max_score,
    ) {
        $score = (strlen($score) ? $score : NULL);
        if ($score > $max_score) {
            $this->dispatch(
                'notifyWarning',
                'Score exceeds ' . $max_score . ' !',
                ''
            );
            return;
        }
        if ($score_id) {
            DB::table('school_work_scores')
                ->where('id', '=', $score_id)
                ->update([
                    'schedule_id' => $schedule_id,
                    'student_id' => $student_id,
                    'term_id' => $term_id,
                    'school_work_id' => $school_work_id,
                    'score' => $score,
                    'max_score' => $max_score,
                ]);
        } else {
            DB::table('school_work_scores')
                ->insert([
                    'id' => NULL,
                    'schedule_id' => $schedule_id,
                    'student_id' => $student_id,
                    'term_id' => $term_id,
                    'school_work_id' => $school_work_id,
                    'score' => $score,
                    'max_score' => $max_score,
                ]);
        }
        $this->dispatch(
            'notifySuccess',
            'Updated successfully!',
            ''
        );
    }

    public function open_term_weight($modal_id)
    {
        $this->term_weight['term_id'] = $this->detail['term_id'];
        self::fetch_terms();
        $this->temp_terms = [];
        foreach ($this->terms as $key => $value) {
            array_push($this->temp_terms, [
                'id' => $value->id,
                'weight' => floatval($value->weight),
                'term_name' => $value->term_name
            ]);
        }
        $this->dispatch('openModal', modal_id: $modal_id);
    }

    public function UpdatedTermWeightTermId()
    {
        self::fetch_terms();
    }


    public function fetch_terms()
    {
        $detail = DB::table('terms')
            ->where('schedule_id', '=', $this->detail['schedule_id'])
            ->where('id', '=', $this->term_weight['term_id'])
            ->first();

        // dd($detail );


        // if($this->schedule->lecture_unit > 0){
        //     $lab_lec = DB::table('lab_lec')
        //         ->where('schedule_id','=',$this->detail['schedule_id'])
        //         ->where('term_id','=',$this->term_weight['term_id'])
        //         ->where('is_lecture','=',1)
        //         ->first();
        //     $this->term_weight['lecture_weight'] = $lab_lec->sub_weight;
        // }

        // if($this->schedule->lecture_unit > 0){
        //     $lab_lec = DB::table('lab_lec')  
        //         ->where('schedule_id','=',$this->detail['schedule_id'])
        //         ->where('term_id','=',$this->term_weight['term_id'])
        //         ->where('is_lecture','=',0)
        //         ->first();
        //     $this->term_weight['lecture_weight'] = $lab_lec->sub_weight;
        // }

        $this->term_weight['weight'] = $detail->weight;
    }

    public function updateWeight($modal_id)
    {

        foreach ($this->temp_terms as $key => $value) {
            $res = DB::table('terms')
                ->where('schedule_id', '=', $this->detail['schedule_id'])
                ->where('id', '=', $value['id'])
                ->update([
                    'weight' => floatval($value['weight'])
                ]);
        }
        $this->dispatch(
            'notifySuccess',
            'Updated successfully!',
            ''
        );
        self::terms($this->detail['schedule_id']);
    }

    public function viewAttendance($modal_id)
    {
        $this->dispatch('openModal', modal_id: $modal_id);
        $this->dispatch('openFacultyAttendanceModal', [
            'obj' => [
                'schedule_id' => $this->detail['schedule_id'],
                'school_year' => $this->school_year,
                'semester' => $this->semester,
                'term_id' => $this->detail['term_id'],
            ]
        ]);
    }

    public function getFinalGrades($student_ids)
    {
        $this->final_grades = [];

        foreach ($student_ids as $student_id) {
            // Fetch the final grade record from the database
            $final_grade = DB::table('final_grades')
                ->where('schedule_id', '=', $this->detail['schedule_id'])
                ->where('student_id', '=', $student_id)
                ->first();

            if ($final_grade) {
                $this->final_grades[$student_id] = [
                    'lecture_grade' => $final_grade->lecture_grade,
                    'laboratory_grade' => $final_grade->laboratory_grade,
                    'total_grade' => $final_grade->total_grade,
                    'weighted_grade' => $final_grade->weighted_grade,
                    'remarks' => $final_grade->remarks,
                ];
            } else {
                // If no record exists, set default null values
                $this->final_grades[$student_id] = [
                    'lecture_grade' => null,
                    'laboratory_grade' => null,
                    'total_grade' => null,
                    'weighted_grade' => null,
                    'remarks' => null,
                ];
            }
        }
    }
}
