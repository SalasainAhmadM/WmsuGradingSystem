<?php

namespace App\Livewire\Faculty\FacultyEvaluation;
use Livewire\Component;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;
use Carbon\Carbon;
use Illuminate\Support\Collection;


class FacultyEvaluationLists extends Component
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

    // public $temp_terms = [];
    public $schedule = NULL;

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

    public $lecture_weights = [];
    public $lecture_weight  = NULL;

    public $point_grade_equivalent = [];
    public $filters = [
        'search' => '',
        'remarks' => '',
    ];
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

        self::autoUpdateRemarks();

        self::updateFinalGrades();
        self::storeLaboratoryValues();
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
        self::autoUpdateRemarks();
        self::updateFinalGrades();
        self::storeLaboratoryValues();
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

        // Search filter - wrap in where clause to prevent OR from affecting remarks filter
        if (!empty($this->filters['search'])) {
            $table_data->where(function ($query) {
                $query->where('s.code', 'like', '%' . $this->filters['search'] . '%')
                    ->orWhere('s.email', 'like', '%' . $this->filters['search'] . '%')
                    ->orWhere(DB::raw('CONCAT_WS(" ", s.first_name, s.middle_name, s.last_name, s.suffix)'), 'like', '%' . $this->filters['search'] . '%');
            });
        }

        // Remarks filter - filter by term-specific remarks
        if (!empty($this->filters['remarks'])) {
            $table_data->whereIn('s.id', function ($query) {
                $query->select('tg.student_id')
                    ->from('term_grades as tg')
                    ->where('tg.schedule_id', '=', $this->detail['schedule_id'])
                    ->where('tg.term_id', '=', $this->detail['term_id'])
                    ->where('tg.remarks', '=', $this->filters['remarks']);
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

        return view('livewire.faculty.faculty-evaluation.faculty-evaluation-lists', [
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
        $terms = DB::table('terms')
            ->where('schedule_id', '=', $this->detail['schedule_id'])
            ->orderBy('term_order', 'asc')
            ->get();

        $this->lecture_weights = [];
        foreach ($terms as $term) {
            $ll = DB::table('lab_lec')
                ->where('schedule_id', '=', $this->detail['schedule_id'])
                ->where('term_id',    '=', $term->id)
                ->first();

            $this->lecture_weights[] = [
                'term_id'   => $term->id,
                'term_name' => $term->term_name,
                'weight'    => $ll ? floatval($ll->sub_weight) : 50.0,
            ];
        }

        $current = collect($this->lecture_weights)
            ->firstWhere('term_id', $this->detail['term_id']);
        $this->lecture_weight = $current ? $current['weight'] : 50.0;

        self::getlaboratory_schedules();
        $this->laboratory_schedules = (array) $this->laboratory_schedules;

        $this->laboratory_schedules_weight = [];
        foreach ($this->laboratory_schedules as $lab) {
            $lab_terms = DB::table('terms')
                ->where('schedule_id', '=', $lab->id)
                ->orderBy('term_order', 'asc')
                ->get();

            $term_weights = [];
            foreach ($lab_terms as $lt) {
                $ll = DB::table('lab_lec')
                    ->where('schedule_id', '=', $lab->id)
                    ->where('term_id',    '=', $lt->id)
                    ->first();

                $term_weights[] = [
                    'term_id'   => $lt->id,
                    'term_name' => $lt->term_name,
                    'weight'    => $ll ? floatval($ll->sub_weight) : 50.0,
                ];
            }

            $this->laboratory_schedules_weight[] = [
                'id'           => $lab->id,
                'term_weights' => $term_weights,
            ];
        }
    }

    public function updateLabWeight($modal_id)
    {
        foreach ($this->lecture_weights as $lw) {
            DB::table('lab_lec')
                ->where('schedule_id', '=', $this->detail['schedule_id'])
                ->where('term_id',    '=', $lw['term_id'])
                ->update(['sub_weight' => $lw['weight']]);
        }

        foreach ($this->laboratory_schedules_weight as $lab) {
            foreach ($lab['term_weights'] as $tw) {
                DB::table('lab_lec')
                    ->where('schedule_id', '=', $lab['id'])
                    ->where('term_id',    '=', $tw['term_id'])
                    ->update(['sub_weight' => $tw['weight']]);
            }
        }

        $current = collect($this->lecture_weights)
            ->firstWhere('term_id', $this->detail['term_id']);
        $this->lecture_weight = $current ? $current['weight'] : 50.0;

        $this->dispatch('notifySuccess', 'Updated successfully!', '');
        $this->dispatch('closeModal', modal_id: $modal_id);
        self::updateFinalGrades();
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
            self::calculateAndStoreFinalGrade($student_id);
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

        self::autoUpdateRemarks();
        self::calculateAndStoreFinalGrade($student_id);
        $this->js('window.location.reload()');
    }

    // public function open_term_weight($modal_id)
    // {
    //     $this->term_weight['term_id'] = $this->detail['term_id'];
    //     self::fetch_terms();
    //     $this->temp_terms = [];
    //     foreach ($this->terms as $key => $value) {
    //         array_push($this->temp_terms, [
    //             'id' => $value->id,
    //             'weight' => floatval($value->weight),
    //             'term_name' => $value->term_name
    //         ]);
    //     }
    //     $this->dispatch('openModal', modal_id: $modal_id);
    // }

    // public function UpdatedTermWeightTermId()
    // {
    //     self::fetch_terms();
    // }


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

    // public function updateWeight($modal_id)
    // {

    //     foreach ($this->temp_terms as $key => $value) {
    //         $res = DB::table('terms')
    //             ->where('schedule_id', '=', $this->detail['schedule_id'])
    //             ->where('id', '=', $value['id'])
    //             ->update([
    //                 'weight' => floatval($value['weight'])
    //             ]);
    //     }
    //     $this->dispatch(
    //         'notifySuccess',
    //         'Updated successfully!',
    //         ''
    //     );
    //     self::terms($this->detail['schedule_id']);
    //     self::updateFinalGrades();
    // }

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

    public function updateRemarks($student_id, $remarks)
    {
        // Check if term_grades record exists for this term
        $term_grade_exists = DB::table('term_grades')
            ->where('schedule_id', '=', $this->detail['schedule_id'])
            ->where('student_id', '=', $student_id)
            ->where('term_id', '=', $this->detail['term_id'])
            ->exists();

        if ($term_grade_exists) {
            // Update existing record
            DB::table('term_grades')
                ->where('schedule_id', '=', $this->detail['schedule_id'])
                ->where('student_id', '=', $student_id)
                ->where('term_id', '=', $this->detail['term_id'])
                ->update([
                    'remarks' => $remarks ?: null,
                ]);
        } else {
            // Insert new record
            DB::table('term_grades')
                ->insert([
                    'schedule_id' => $this->detail['schedule_id'],
                    'student_id' => $student_id,
                    'term_id' => $this->detail['term_id'],
                    'remarks' => $remarks ?: null,
                    'grade' => null,
                    'other' => null,
                ]);
        }

        $this->dispatch('notifySuccess', 'Remarks updated successfully!', '');
        self::calculateAndStoreFinalGrade($student_id);
    }

    /**
     * Auto-update remarks for all students based on their grades
     * Only updates if remarks is null or empty
     */
    public function autoUpdateRemarks()
    {
        // Get all enrolled students for this schedule
        $students = DB::table('enrolled_students as es')
            ->select('s.id')
            ->leftJoin('students as s', 's.id', 'es.student_id')
            ->where('es.schedule_id', '=', $this->detail['schedule_id'])
            ->get();

        foreach ($students as $student) {
            // Check if remarks already exist for this term
            $term_grade = DB::table('term_grades')
                ->where('schedule_id', '=', $this->detail['schedule_id'])
                ->where('student_id', '=', $student->id)
                ->where('term_id', '=', $this->detail['term_id'])
                ->first();

            // Only auto-update if remarks is null or empty
            if (!$term_grade || empty($term_grade->remarks)) {
                $calculated_remark = $this->calculateRemark($student->id);

                if ($calculated_remark) {
                    if ($term_grade) {
                        // Update existing record
                        DB::table('term_grades')
                            ->where('schedule_id', '=', $this->detail['schedule_id'])
                            ->where('student_id', '=', $student->id)
                            ->where('term_id', '=', $this->detail['term_id'])
                            ->update([
                                'remarks' => $calculated_remark,
                            ]);
                    } else {
                        // Insert new record
                        DB::table('term_grades')
                            ->insert([
                                'schedule_id' => $this->detail['schedule_id'],
                                'student_id' => $student->id,
                                'term_id' => $this->detail['term_id'],
                                'remarks' => $calculated_remark,
                                'grade' => null,
                                'other' => null,
                            ]);
                    }
                }
            }
        }
    }

    /**
     * Calculate the remark for a specific student based on their grades
     * Returns: 'INC', 'DROP', 'PASSED', 'FAILED', or null
     */
    private function calculateRemark($student_id)
    {
        // Check for INC status in current term
        $has_inc = DB::table('term_grades')
            ->where('schedule_id', '=', $this->detail['schedule_id'])
            ->where('student_id', '=', $student_id)
            ->where('term_id', '=', $this->detail['term_id'])
            ->where('other', '=', 'INC')
            ->exists();

        $has_lab_inc = DB::table('lab_lec_grades')
            ->where('schedule_id', '=', $this->detail['schedule_id'])
            ->where('student_id', '=', $student_id)
            ->where('other', '=', 'INC')
            ->exists();

        // Check for DROP status in current term
        $has_drop = DB::table('term_grades')
            ->where('schedule_id', '=', $this->detail['schedule_id'])
            ->where('student_id', '=', $student_id)
            ->where('term_id', '=', $this->detail['term_id'])
            ->where('other', '=', 'DROP')
            ->exists();

        $has_lab_drop = DB::table('lab_lec_grades')
            ->where('schedule_id', '=', $this->detail['schedule_id'])
            ->where('student_id', '=', $student_id)
            ->where('other', '=', 'DROP')
            ->exists();

        // Priority 1: INC status
        if ($has_inc || $has_lab_inc) {
            return 'INC';
        }

        // Priority 2: DROP status
        if ($has_drop || $has_lab_drop) {
            return 'DROP';
        }

        // Priority 3: Calculate based on grade
        $lab_lec_grades = DB::table('lab_lec_grades')
            ->where('schedule_id', '=', $this->detail['schedule_id'])
            ->where('student_id', '=', $student_id)
            ->first();

        $total_lab_lec_grade = 0;
        $total_lab_lec_grade_average = 0;

        // Calculate Lecture grade if applicable
        if ($this->schedule && $this->schedule->is_lec) {
            $total_lab_lec_grade_average += 1;
            if ($lab_lec_grades != null && floatval($lab_lec_grades->grade)) {
                // Get current term weight
                $current_term = collect($this->terms)->firstWhere('id', $this->detail['term_id']);
                $term_weight_percent = $current_term ? $current_term->weight : 100;

                // Calculate scaled lecture grade
                $actual_grade_percent = ($lab_lec_grades->grade / $lab_lec_grades->sub_weight) * 100;
                $scaled_lecture_grade = ($actual_grade_percent / $term_weight_percent) * 10000;
                $total_lab_lec_grade += $scaled_lecture_grade;
            }
        }

        // Calculate Laboratory grade if applicable
        if ($this->schedule && ($this->schedule->laboratory_unit > 0 || $this->schedule->is_lec == 0)) {
            if (count($this->laboratory_schedules) > 0) {
                $lab_lec_grade = DB::table('lab_lec_grades')
                    ->where('schedule_id', '=', $this->laboratory_schedules[0]->id)
                    ->where('student_id', '=', $student_id)
                    ->first();

                $total_lab_lec_grade_average += 1;

                if ($lab_lec_grade != null && floatval($lab_lec_grade->grade)) {
                    $total_lab_lec_grade += floatval($lab_lec_grade->grade) ?
                        floatval($lab_lec_grade->grade / $lab_lec_grade->sub_weight * 100 * 100) : 0;
                }
            }
        }

        // Calculate final grade
        $final_grade = ($total_lab_lec_grade_average > 0 && floatval($total_lab_lec_grade)) ?
            ($total_lab_lec_grade / $total_lab_lec_grade_average) : 0;

        // Determine PASSED or FAILED based on grade
        if ($final_grade > 0) {
            $passing_grade = 3.0;

            // Check against point grade equivalent
            foreach ($this->point_grade_equivalent as $p_value) {
                if ($final_grade >= $p_value->minimum && $final_grade < $p_value->maximum + 1) {
                    if (floatval($p_value->grade) <= $passing_grade) {
                        return 'PASSED';
                    } else {
                        return 'FAILED';
                    }
                }
            }

            // Fallback: 75 is passing
            return $final_grade >= 75 ? 'PASSED' : 'FAILED';
        }

        // No grade calculated yet
        return null;
    }

    public function updateFinalGrades()
    {
        // Get all enrolled students
        $students = DB::table('enrolled_students as es')
            ->select('s.id')
            ->leftJoin('students as s', 's.id', 'es.student_id')
            ->where('es.schedule_id', '=', $this->detail['schedule_id'])
            ->get();

        foreach ($students as $student) {
            // Calculate cumulative lecture grade across all terms
            $this->calculateAndStoreFinalGrade($student->id);
        }
    }

    /**
     * Calculate and store final grade for a specific student
     * Includes both Lecture and Laboratory grades
     */
    public function calculateAndStoreFinalGrade($student_id)
    {
        $total_lecture_grade = 0;
        $term_count = 0;
        
        // Variables to store individual term weighted totals (from red square area)
        $midterm_grade_value = null;
        $finalterm_grade_value = null;

        // Get all terms for this schedule
        $all_terms = DB::table('terms')
            ->where('schedule_id', '=', $this->detail['schedule_id'])
            ->orderBy('term_order', 'asc')
            ->get();

        // Track if we have any valid grades
        $has_any_grades = false;

        // For each term, calculate the weighted total (Lecture + Laboratory)
        foreach ($all_terms as $term) {
            // Get the term grade (this is the "Total" column value from green square)
            $term_grade = DB::table('term_grades')
                ->where('schedule_id', '=', $this->detail['schedule_id'])
                ->where('student_id', '=', $student_id)
                ->where('term_id', '=', $term->id)
                ->first();

            $term_lecture_value = null;
            $term_laboratory_value = null;
            $term_weighted_total = null;
            
            // Get lab/lec weight for this term
            $lab_lec_weight_term = DB::table('lab_lec')
                ->where('schedule_id', '=', $this->detail['schedule_id'])
                ->where('term_id', '=', $term->id)
                ->first();
            
            $term_lecture_weight_percent = $lab_lec_weight_term ? floatval($lab_lec_weight_term->sub_weight) : 80;
            $term_laboratory_weight_percent = 100 - $term_lecture_weight_percent;
            
            // Track this term for counting
            $term_has_data = false;

            if ($term_grade && floatval($term_grade->grade)) {
                $term_weight_percent = $term->weight;
                
                // Convert grade back to 0-100 scale (this is the "Total" column value - green square)
                $term_total_value = ($term_grade->grade / ($term_weight_percent / 100)) * 100;
                
                // Calculate Lecture value for this term (if applicable)
                if ($this->schedule && $this->schedule->is_lec) {
                    // Lecture = Total × (Lecture Weight / 100)
                    $term_lecture_value = $term_total_value * ($term_lecture_weight_percent / 100);
                    
                    // Add to cumulative for final lecture grade calculation
                    $total_lecture_grade += $term_total_value;
                }
                
                // Calculate Laboratory value for this term (if applicable)
                if ($this->schedule && ($this->schedule->laboratory_unit > 0 || $this->schedule->is_lec == 0)) {
                    $term_type = $term->term_name;
                    
                    if ($this->schedule->is_lec == 1) {
                        // For lecture schedules with lab component, get from lab_values
                        $lab_value_term = DB::table('lab_values')
                            ->where('student_id', '=', $student_id)
                            ->where('term_type', '=', $term_type)
                            ->first();
                        
                        if ($lab_value_term && floatval($lab_value_term->value_lab)) {
                            $scaled_laboratory_grade = floatval($lab_value_term->value_lab);
                            // Laboratory = Scaled Lab Grade × (Lab Weight / 100)
                            $term_laboratory_value = $scaled_laboratory_grade * ($term_laboratory_weight_percent / 100);
                        }
                    } else {
                        // For pure laboratory schedules, Laboratory = Total × (Lab Weight / 100)
                        $term_laboratory_value = $term_total_value * ($term_laboratory_weight_percent / 100);
                    }
                }
                
                // Calculate weighted total for this term (the red square value)
                if ($this->schedule && $this->schedule->is_lec) {
                    // For lecture schedules, sum of lecture and laboratory
                    if ($term_lecture_value !== null && $term_laboratory_value !== null) {
                        $term_weighted_total = $term_lecture_value + $term_laboratory_value;
                    } elseif ($term_lecture_value !== null) {
                        $term_weighted_total = $term_lecture_value;
                    }
                } else {
                    // For pure lab schedules, just use the laboratory value
                    $term_weighted_total = $term_laboratory_value;
                }
                
                // Store based on term name
                $term_name_lower = strtolower(trim($term->term_name));
                if ($term_name_lower === 'midterm') {
                    $midterm_grade_value = $term_weighted_total;
                } elseif ($term_name_lower === 'finalterm') {
                    $finalterm_grade_value = $term_weighted_total;
                }
                
                $has_any_grades = true;
                $term_has_data = true;
            }

            // Only count this term if it had valid data
            if ($term_has_data) {
                $term_count++;
            }
        }

        // Calculate final averages
        $final_lecture_value = null;
        $final_laboratory_value = null;
        $total_grade = null;
        $weighted_grade = null;
        $remarks = null;

        // Calculate lecture average
        if ($term_count > 0 && $this->schedule && $this->schedule->is_lec && $total_lecture_grade > 0) {
            $final_lecture_value = $total_lecture_grade / $term_count;
        }

        // Calculate LABORATORY average from lab_values table
        if ($this->schedule && ($this->schedule->laboratory_unit > 0 || $this->schedule->is_lec == 0)) {
            // Get Midterm and Finalterm lab values
            $lab_midterm = DB::table('lab_values')
                ->where('student_id', '=', $student_id)
                ->where('term_type', '=', 'Midterm')
                ->first();

            $lab_finalterm = DB::table('lab_values')
                ->where('student_id', '=', $student_id)
                ->where('term_type', '=', 'Finalterm')
                ->first();

            $midterm_value = 0;
            $finalterm_value = 0;
            $lab_count = 0;

            // Get Midterm value
            if ($lab_midterm && floatval($lab_midterm->value_lab) > 0) {
                $midterm_value = floatval($lab_midterm->value_lab);
                $lab_count++;
            }

            // Get Finalterm value
            if ($lab_finalterm && floatval($lab_finalterm->value_lab) > 0) {
                $finalterm_value = floatval($lab_finalterm->value_lab);
                $lab_count++;
            }

            // Calculate average
            if ($lab_count > 0) {
                $final_laboratory_value = (($midterm_value + $finalterm_value) / $lab_count);
                $has_any_grades = true;
            }
        }

        // Calculate total grade (handle null/empty values)
        if ($final_lecture_value !== null && $final_laboratory_value !== null) {
            // Both lecture and laboratory exist - average them
            $total_grade = ($final_lecture_value + $final_laboratory_value) / 2;
        } elseif ($final_lecture_value !== null) {
            // Only lecture exists
            $total_grade = $final_lecture_value;
        } elseif ($final_laboratory_value !== null) {
            // Only laboratory exists
            $total_grade = $final_laboratory_value;
        }

        // Calculate weighted grade and remarks based on total grade
        if ($total_grade !== null && $total_grade > 0) {
            // Check if any term has INC status
            $has_inc = DB::table('term_grades')
                ->where('schedule_id', '=', $this->detail['schedule_id'])
                ->where('student_id', '=', $student_id)
                ->where('other', '=', 'INC')
                ->exists();

            $has_lab_inc = DB::table('lab_values')
                ->where('student_id', '=', $student_id)
                ->where('schedule_id', '=', $this->detail['schedule_id'])
                ->whereRaw("LOWER(TRIM(term_type)) = 'inc'")
                ->exists();

            // Check if any term has DROP status
            $has_drop = DB::table('term_grades')
                ->where('schedule_id', '=', $this->detail['schedule_id'])
                ->where('student_id', '=', $student_id)
                ->where('other', '=', 'DROP')
                ->exists();

            $has_lab_drop = DB::table('lab_values')
                ->where('student_id', '=', $student_id)
                ->where('schedule_id', '=', $this->detail['schedule_id'])
                ->whereRaw("LOWER(TRIM(term_type)) = 'drop'")
                ->exists();

            // Determine remarks based on priority
            if ($has_inc || $has_lab_inc) {
                $remarks = 'INC';
                $grade_equivalent = DB::table('point_grade_equivalent')
                    ->where('minimum', '<=', $total_grade)
                    ->where('maximum', '>=', $total_grade)
                    ->first();

                if ($grade_equivalent) {
                    $weighted_grade = floatval($grade_equivalent->grade);
                }
            } elseif ($has_drop || $has_lab_drop) {
                $remarks = 'DROP';
                $grade_equivalent = DB::table('point_grade_equivalent')
                    ->where('minimum', '<=', $total_grade)
                    ->where('maximum', '>=', $total_grade)
                    ->first();

                if ($grade_equivalent) {
                    $weighted_grade = floatval($grade_equivalent->grade);
                }
            } else {
                $grade_equivalent = DB::table('point_grade_equivalent')
                    ->where('minimum', '<=', $total_grade)
                    ->where('maximum', '>=', $total_grade)
                    ->first();

                if ($grade_equivalent) {
                    $weighted_grade = floatval($grade_equivalent->grade);
                    $passing_grade = 3.0;
                    if ($weighted_grade <= $passing_grade) {
                        $remarks = 'PASSED';
                    } else {
                        $remarks = 'FAILED';
                    }
                } else {
                    $weighted_grade = null;
                    $remarks = $total_grade >= 75 ? 'PASSED' : 'FAILED';
                }
            }
        } elseif ($has_any_grades) {
            $has_inc = DB::table('term_grades')
                ->where('schedule_id', '=', $this->detail['schedule_id'])
                ->where('student_id', '=', $student_id)
                ->where('other', '=', 'INC')
                ->exists();

            $has_lab_inc = DB::table('lab_values')
                ->where('student_id', '=', $student_id)
                ->where('schedule_id', '=', $this->detail['schedule_id'])
                ->whereRaw("LOWER(TRIM(term_type)) = 'inc'")
                ->exists();

            $has_drop = DB::table('term_grades')
                ->where('schedule_id', '=', $this->detail['schedule_id'])
                ->where('student_id', '=', $student_id)
                ->where('other', '=', 'DROP')
                ->exists();

            $has_lab_drop = DB::table('lab_values')
                ->where('student_id', '=', $student_id)
                ->where('schedule_id', '=', $this->detail['schedule_id'])
                ->whereRaw("LOWER(TRIM(term_type)) = 'drop'")
                ->exists();

            if ($has_inc || $has_lab_inc) {
                $remarks = 'INC';
            } elseif ($has_drop || $has_lab_drop) {
                $remarks = 'DROP';
            }
        }

        // Store or update the final grade record
        $final_grade_exists = DB::table('final_grades')
            ->where('schedule_id', '=', $this->detail['schedule_id'])
            ->where('student_id', '=', $student_id)
            ->exists();

        if ($final_grade_exists) {
            DB::table('final_grades')
                ->where('schedule_id', '=', $this->detail['schedule_id'])
                ->where('student_id', '=', $student_id)
                ->update([
                    'lecture_grade' => $final_lecture_value,
                    'laboratory_grade' => $final_laboratory_value,
                    'midterm_grades' => $midterm_grade_value, // Now stores weighted total (Lecture + Lab)
                    'finalterm_grades' => $finalterm_grade_value, // Now stores weighted total (Lecture + Lab)
                    'total_grade' => $total_grade,
                    'weighted_grade' => $weighted_grade,
                    'remarks' => $remarks,
                    'updated_at' => now(),
                ]);
        } else {
            DB::table('final_grades')
                ->insert([
                    'schedule_id' => $this->detail['schedule_id'],
                    'student_id' => $student_id,
                    'lecture_grade' => $final_lecture_value,
                    'laboratory_grade' => $final_laboratory_value,
                    'midterm_grades' => $midterm_grade_value, // Now stores weighted total (Lecture + Lab)
                    'finalterm_grades' => $finalterm_grade_value, // Now stores weighted total (Lecture + Lab)
                    'total_grade' => $total_grade,
                    'weighted_grade' => $weighted_grade,
                    'remarks' => $remarks,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
        }
    }
    public function exportCSV()
    {
        // Get all students for this schedule
        $students = DB::table('enrolled_students as es')
            ->select(
                's.id',
                's.code',
                DB::raw('CONCAT_WS(" ", s.first_name, s.middle_name, s.last_name, s.suffix) AS fullname'),
                'c.name as college',
                'd.name as department',
                'yl.year_level'
            )
            ->leftJoin('students as s', 's.id', 'es.student_id')
            ->leftJoin('colleges as c', 'c.id', 's.college_id')
            ->leftJoin('departments as d', 'd.id', 's.department_id')
            ->leftJoin('year_levels as yl', 'yl.id', 's.year_level_id')
            ->where('es.schedule_id', '=', $this->detail['schedule_id'])
            ->orderBy('s.is_active', 'desc')
            ->orderBy('s.id', 'desc')
            ->get();

        // Apply filters
        if (!empty($this->filters['search'])) {
            $students = $students->filter(function ($student) {
                return stripos($student->code, $this->filters['search']) !== false ||
                    stripos($student->fullname, $this->filters['search']) !== false;
            });
        }

        if (!empty($this->filters['remarks'])) {
            $student_ids = DB::table('term_grades')
                ->where('schedule_id', '=', $this->detail['schedule_id'])
                ->where('term_id', '=', $this->detail['term_id'])
                ->where('remarks', '=', $this->filters['remarks'])
                ->pluck('student_id')
                ->toArray();
            $students = $students->whereIn('id', $student_ids);
        }

        // Get current term info
        $current_term = collect($this->terms)->firstWhere('id', $this->detail['term_id']);
        $term_name = $current_term ? $current_term->term_name : 'Term';

        // Get Lab Lec Weight values
        $lab_lec_weight = DB::table('lab_lec')
            ->where('schedule_id', '=', $this->detail['schedule_id'])
            ->where('term_id', '=', $this->detail['term_id'])
            ->first();

        $lecture_weight_percent = $lab_lec_weight ? floatval($lab_lec_weight->sub_weight) : 80;
        $laboratory_weight_percent = 100 - $lecture_weight_percent;

        // Prepare CSV
        $filename = 'evaluation_' . $term_name . '_' . $this->school_year . '_' . $this->semester . '_' . now()->timezone('Asia/Manila')->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($students, $term_name, $lecture_weight_percent, $laboratory_weight_percent) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header row
            $header = ['#', 'Student Code', 'Student Name', 'College', 'Department', 'Year Level'];

            // Add school work type columns
            foreach ($this->school_work_types as $swt) {
                if ($swt->weight > 0 && $swt->id != $this->current_school_work_type->id) {
                    $header[] = $swt->school_work_type . ' (%)';
                }
            }

            $header[] = 'Total';

            if ($this->schedule->is_lec) {
                $header[] = 'Lecture (Weighted ' . number_format($lecture_weight_percent, 2) . '%)';
            }
            if ($this->schedule->laboratory_unit > 0 || $this->schedule->is_lec == 0) {
                $header[] = 'Laboratory (Weighted ' . number_format($laboratory_weight_percent, 2) . '%)';
            }
            if ($this->schedule->is_lec) {
                $header[] = 'Total (Weighted Avg)';
            }
            $header[] = 'Weighted Grade';
            $header[] = 'Remarks';

            fputcsv($file, $header);

            // Get weight totals
            $weight = DB::table('school_works_types')
                ->select(DB::raw('sum(weight) as total_weight'))
                ->where('schedule_id', '=', $this->detail['schedule_id'])
                ->where('term_id', '=', $this->detail['term_id'])
                ->first();

            // Data rows
            $counter = 1;
            foreach ($students as $student) {
                $row = [
                    $counter++,
                    $student->code,
                    $student->fullname,
                    $student->college ?? 'N/A',
                    $student->department ?? 'N/A',
                    $student->year_level ?? 'N/A'
                ];

                // Calculate school work scores
                $total_grade = 0;
                foreach ($this->school_work_types as $swt) {
                    if ($swt->weight > 0 && $swt->id != $this->current_school_work_type->id) {
                        $school_works = DB::table('school_works as sw')
                            ->select('sw.id', 'sw.max_score', 'sws.score')
                            ->leftJoin('school_work_scores as sws', 'sws.school_work_id', 'sw.id')
                            ->where('sw.school_work_type_id', '=', $swt->id)
                            ->where('sw.schedule_id', '=', $this->detail['schedule_id'])
                            ->where('sw.term_id', '=', $this->detail['term_id'])
                            ->where(function ($query) use ($student) {
                                $query->whereNull('sws.student_id')->orWhere('sws.student_id', $student->id);
                            })
                            ->get();

                        $school_work_count = 0;
                        $school_work_average = 0;
                        foreach ($school_works as $sw) {
                            if ($sw->score !== null && $sw->max_score > 0) {
                                $school_work_average += ($sw->score / $sw->max_score);
                                $school_work_count++;
                            }
                        }

                        if ($school_work_count > 0) {
                            $sub_total = $school_work_average / $school_work_count;
                            $row[] = number_format($sub_total * 100, 2, '.', '');
                            $school_work_type_weight = $weight->total_weight ? ($swt->weight / $weight->total_weight * 100) : 0;
                            $total_grade += ($sub_total * $school_work_type_weight / 100);
                        } else {
                            $row[] = '';
                        }
                    }
                }

                // Total column
                $row[] = $total_grade > 0 ? number_format($total_grade * 100, 2, '.', '') : '';

                // Variables to store component grades
                $lecture_component = null;
                $laboratory_component = null;

                // Lecture Grade (Weighted)
                if ($this->schedule->is_lec) {
                    $lab_lec_grades = DB::table('lab_lec_grades')
                        ->where('schedule_id', '=', $this->detail['schedule_id'])
                        ->where('student_id', '=', $student->id)
                        ->first();

                    if ($lab_lec_grades != null && floatval($lab_lec_grades->grade)) {
                        $current_term = collect($this->terms)->firstWhere('id', $this->detail['term_id']);
                        $term_weight_percent = $current_term ? $current_term->weight : 100;
                        $actual_grade_percent = ($lab_lec_grades->grade / $lab_lec_grades->sub_weight) * 100;
                        $scaled_lecture_grade = ($actual_grade_percent / $term_weight_percent) * 10000;
                        
                        // Apply lecture weight
                        $weighted_lecture_grade = $scaled_lecture_grade * ($lecture_weight_percent / 100);
                        $lecture_component = $scaled_lecture_grade;
                        
                        $row[] = number_format($weighted_lecture_grade, 2, '.', '');
                    } else {
                        $row[] = $lab_lec_grades ? $lab_lec_grades->other : '';
                    }
                }

                // Laboratory Grade (Weighted)
                if ($this->schedule->laboratory_unit > 0 || $this->schedule->is_lec == 0) {
                    $current_term = collect($this->terms)->firstWhere('id', $this->detail['term_id']);
                    $term_type = $current_term ? $current_term->term_name : 'Midterm';

                    if ($this->schedule->is_lec == 1) {
                        $lab_value = DB::table('lab_values')
                            ->where('student_id', '=', $student->id)
                            ->where('term_type', '=', $term_type)
                            ->first();

                        if ($lab_value && floatval($lab_value->value_lab)) {
                            $scaled_laboratory_grade = floatval($lab_value->value_lab);
                            
                            // Apply laboratory weight
                            $weighted_laboratory_grade = $scaled_laboratory_grade * ($laboratory_weight_percent / 100);
                            $laboratory_component = $scaled_laboratory_grade;
                            
                            $row[] = number_format($weighted_laboratory_grade, 2, '.', '');
                        } else {
                            $row[] = '0.00';
                        }
                    } else {
                        // For laboratory schedules
                        $lab_lec_grade = DB::table('lab_lec_grades')
                            ->where('schedule_id', '=', $this->detail['schedule_id'])
                            ->where('student_id', '=', $student->id)
                            ->first();

                        // Get lab weight
                        $lab_lec_weight_lab = DB::table('lab_lec')
                            ->where('schedule_id', '=', $this->detail['schedule_id'])
                            ->where('term_id', '=', $this->detail['term_id'])
                            ->first();
                        
                        $lab_weight_percent = $lab_lec_weight_lab ? floatval($lab_lec_weight_lab->sub_weight) : 100;

                        if ($lab_lec_grade != null && floatval($lab_lec_grade->grade)) {
                            $term_weight_percent = $current_term ? $current_term->weight : 100;
                            $actual_lab_grade_percent = ($lab_lec_grade->grade / $lab_lec_grade->sub_weight) * 100;
                            $scaled_laboratory_grade = ($actual_lab_grade_percent / $term_weight_percent) * 10000;
                            
                            // Apply laboratory weight
                            $weighted_laboratory_grade = $scaled_laboratory_grade * ($lab_weight_percent / 100);
                            $laboratory_component = $scaled_laboratory_grade;
                            
                            $row[] = number_format($weighted_laboratory_grade, 2, '.', '');
                        } else {
                            $row[] = $lab_lec_grade ? $lab_lec_grade->other : '0.00';
                        }
                    }
                }

                // Total (Weighted Average) - only for lecture schedules
                if ($this->schedule->is_lec) {
                    $weighted_average = 0;
                    if ($lecture_component !== null && $laboratory_component !== null) {
                        $weighted_average = ($lecture_component * ($lecture_weight_percent / 100)) + 
                                        ($laboratory_component * ($laboratory_weight_percent / 100));
                    } elseif ($lecture_component !== null) {
                        $weighted_average = $lecture_component;
                    }
                    $row[] = $weighted_average > 0 ? number_format($weighted_average, 2, '.', '') : '0.00';
                } else {
                    // For lab-only schedules, use laboratory component directly
                    $weighted_average = $laboratory_component ?? 0;
                }

                // Weighted Grade
                $weighted_grade = '';
                if ($weighted_average > 0) {
                    foreach ($this->point_grade_equivalent as $p_value) {
                        if ($weighted_average >= $p_value->minimum && $weighted_average < $p_value->maximum + 1) {
                            $weighted_grade = $p_value->grade;
                            break;
                        }
                    }
                }
                $row[] = $weighted_grade;

                // Remarks
                $term_grade_record = DB::table('term_grades')
                    ->where('schedule_id', '=', $this->detail['schedule_id'])
                    ->where('student_id', '=', $student->id)
                    ->where('term_id', '=', $this->detail['term_id'])
                    ->first();

                $row[] = $term_grade_record && $term_grade_record->remarks ? $term_grade_record->remarks : 'N/A';

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportExcel()
    {
        // Get all students for this schedule
        $students = DB::table('enrolled_students as es')
            ->select(
                's.id',
                's.code',
                DB::raw('CONCAT_WS(" ", s.first_name, s.middle_name, s.last_name, s.suffix) AS fullname'),
                'c.name as college',
                'd.name as department',
                'yl.year_level'
            )
            ->leftJoin('students as s', 's.id', 'es.student_id')
            ->leftJoin('colleges as c', 'c.id', 's.college_id')
            ->leftJoin('departments as d', 'd.id', 's.department_id')
            ->leftJoin('year_levels as yl', 'yl.id', 's.year_level_id')
            ->where('es.schedule_id', '=', $this->detail['schedule_id'])
            ->orderBy('s.is_active', 'desc')
            ->orderBy('s.id', 'desc')
            ->get();

        // Apply filters
        if (!empty($this->filters['search'])) {
            $students = $students->filter(function ($student) {
                return stripos($student->code, $this->filters['search']) !== false ||
                    stripos($student->fullname, $this->filters['search']) !== false;
            });
        }

        if (!empty($this->filters['remarks'])) {
            $student_ids = DB::table('term_grades')
                ->where('schedule_id', '=', $this->detail['schedule_id'])
                ->where('term_id', '=', $this->detail['term_id'])
                ->where('remarks', '=', $this->filters['remarks'])
                ->pluck('student_id')
                ->toArray();
            $students = $students->whereIn('id', $student_ids);
        }

        // Get current term info
        $current_term = collect($this->terms)->firstWhere('id', $this->detail['term_id']);
        $term_name = $current_term ? $current_term->term_name : 'Term';

        // Get Lab Lec Weight values
        $lab_lec_weight = DB::table('lab_lec')
            ->where('schedule_id', '=', $this->detail['schedule_id'])
            ->where('term_id', '=', $this->detail['term_id'])
            ->first();

        $lecture_weight_percent = $lab_lec_weight ? floatval($lab_lec_weight->sub_weight) : 80;
        $laboratory_weight_percent = 100 - $lecture_weight_percent;

        // Create Excel content
        $filename = 'evaluation_' . $term_name . '_' . $this->school_year . '_' . $this->semester . '_' . now()->timezone('Asia/Manila')->format('Y-m-d_His') . '.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($students, $term_name, $lecture_weight_percent, $laboratory_weight_percent) {
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
            echo '<head>';
            echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
            echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>';
            echo '<x:Name>Evaluation</x:Name>';
            echo '<x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet>';
            echo '</x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
            echo '<style>';
            echo 'table { border-collapse: collapse; width: 100%; }';
            echo 'th, td { border: 1px solid black; padding: 8px; text-align: left; }';
            echo 'th { background-color: #952323; color: white; font-weight: bold; }';
            echo '.text-center { text-align: center; }';
            echo '</style>';
            echo '</head><body>';

            // Add title and schedule info
            echo '<h2>' . htmlspecialchars($term_name) . ' Evaluation Report</h2>';
            echo '<p><strong>School Year:</strong> ' . htmlspecialchars($this->school_year) . '</p>';
            echo '<p><strong>Semester:</strong> ' . htmlspecialchars($this->semester) . '</p>';
            if ($this->schedule) {
                echo '<p><strong>Subject:</strong> ' . htmlspecialchars($this->schedule->subject) . '</p>';
                echo '<p><strong>Faculty:</strong> ' . htmlspecialchars($this->schedule->faculty_fullname) . '</p>';
            }
            echo '<p><strong>Lab/Lecture Weight:</strong> Lecture ' . number_format($lecture_weight_percent, 2) . '% / Laboratory ' . number_format($laboratory_weight_percent, 2) . '%</p>';
            echo '<br>';

            echo '<table>';

            // Header row
            echo '<thead><tr>';
            echo '<th>#</th>';
            echo '<th>Student Code</th>';
            echo '<th>Student Name</th>';
            echo '<th>College</th>';
            echo '<th>Department</th>';
            echo '<th>Year Level</th>';

            // Add school work type columns dynamically
            foreach ($this->school_work_types as $swt) {
                if ($swt->weight > 0 && $swt->id != $this->current_school_work_type->id) {
                    echo '<th>' . htmlspecialchars($swt->school_work_type) . ' (%)</th>';
                }
            }

            echo '<th>Total</th>';

            if ($this->schedule->is_lec) {
                echo '<th>Lecture (Weighted ' . number_format($lecture_weight_percent, 2) . '%)</th>';
            }
            if ($this->schedule->laboratory_unit > 0 || $this->schedule->is_lec == 0) {
                echo '<th>Laboratory (Weighted ' . number_format($laboratory_weight_percent, 2) . '%)</th>';
            }
            if ($this->schedule->is_lec) {
                echo '<th>Total (Weighted Avg)</th>';
            }
            echo '<th>Weighted Grade</th>';
            echo '<th>Remarks</th>';
            echo '</tr></thead><tbody>';

            // Get weight totals
            $weight = DB::table('school_works_types')
                ->select(DB::raw('sum(weight) as total_weight'))
                ->where('schedule_id', '=', $this->detail['schedule_id'])
                ->where('term_id', '=', $this->detail['term_id'])
                ->first();

            // Data rows
            $counter = 1;
            foreach ($students as $student) {
                echo '<tr>';
                echo '<td class="text-center">' . $counter++ . '</td>';
                echo '<td>' . htmlspecialchars($student->code) . '</td>';
                echo '<td>' . htmlspecialchars($student->fullname) . '</td>';
                echo '<td>' . htmlspecialchars($student->college ?? 'N/A') . '</td>';
                echo '<td>' . htmlspecialchars($student->department ?? 'N/A') . '</td>';
                echo '<td>' . htmlspecialchars($student->year_level ?? 'N/A') . '</td>';

                // Calculate school work scores
                $total_grade = 0;
                foreach ($this->school_work_types as $key => $swt) {
                    if ($swt->weight > 0 && $swt->id != $this->current_school_work_type->id) {
                        $school_works = DB::table('school_works as sw')
                            ->select('sw.id', 'sw.max_score', 'sws.score')
                            ->leftJoin('school_work_scores as sws', 'sws.school_work_id', 'sw.id')
                            ->where('sw.school_work_type_id', '=', $swt->id)
                            ->where('sw.schedule_id', '=', $this->detail['schedule_id'])
                            ->where('sw.term_id', '=', $this->detail['term_id'])
                            ->where(function ($query) use ($student) {
                                $query->whereNull('sws.student_id')
                                    ->orWhere('sws.student_id', $student->id);
                            })
                            ->get();

                        $school_work_count = 0;
                        $school_work_average = 0;
                        foreach ($school_works as $sw) {
                            if ($sw->score !== null && $sw->max_score > 0) {
                                $school_work_average += ($sw->score / $sw->max_score);
                                $school_work_count++;
                            }
                        }

                        if ($school_work_count > 0) {
                            $sub_total = $school_work_average / $school_work_count;
                            echo '<td class="text-center">' . number_format($sub_total * 100, 2, '.', '') . '</td>';
                            $school_work_type_weight = $weight->total_weight ? ($swt->weight / $weight->total_weight * 100) : 0;
                            $total_grade += ($sub_total * $school_work_type_weight / 100);
                        } else {
                            echo '<td class="text-center"></td>';
                        }
                    }
                }

                // Total column
                echo '<td class="text-center">' . ($total_grade > 0 ? number_format($total_grade * 100, 2, '.', '') : '') . '</td>';

                // Variables to store component grades
                $lecture_component = null;
                $laboratory_component = null;

                // Lecture Grade (Weighted)
                if ($this->schedule->is_lec) {
                    $lab_lec_grades = DB::table('lab_lec_grades')
                        ->where('schedule_id', '=', $this->detail['schedule_id'])
                        ->where('student_id', '=', $student->id)
                        ->first();

                    if ($lab_lec_grades != null && floatval($lab_lec_grades->grade)) {
                        $current_term = collect($this->terms)->firstWhere('id', $this->detail['term_id']);
                        $term_weight_percent = $current_term ? $current_term->weight : 100;
                        $actual_grade_percent = ($lab_lec_grades->grade / $lab_lec_grades->sub_weight) * 100;
                        $scaled_lecture_grade = ($actual_grade_percent / $term_weight_percent) * 10000;
                        
                        // Apply lecture weight
                        $weighted_lecture_grade = $scaled_lecture_grade * ($lecture_weight_percent / 100);
                        $lecture_component = $scaled_lecture_grade;
                        
                        echo '<td class="text-center">' . number_format($weighted_lecture_grade, 2, '.', '') . '</td>';
                    } else {
                        echo '<td class="text-center">' . htmlspecialchars($lab_lec_grades ? $lab_lec_grades->other : '') . '</td>';
                    }
                }

                // Laboratory Grade (Weighted)
                if ($this->schedule->laboratory_unit > 0 || $this->schedule->is_lec == 0) {
                    $current_term = collect($this->terms)->firstWhere('id', $this->detail['term_id']);
                    $term_type = $current_term ? $current_term->term_name : 'Midterm';

                    if ($this->schedule->is_lec == 1) {
                        // For lecture schedules, get from lab_values
                        $lab_value = DB::table('lab_values')
                            ->where('student_id', '=', $student->id)
                            ->where('term_type', '=', $term_type)
                            ->first();

                        if ($lab_value && floatval($lab_value->value_lab)) {
                            $scaled_laboratory_grade = floatval($lab_value->value_lab);
                            
                            // Apply laboratory weight
                            $weighted_laboratory_grade = $scaled_laboratory_grade * ($laboratory_weight_percent / 100);
                            $laboratory_component = $scaled_laboratory_grade;
                            
                            echo '<td class="text-center">' . number_format($weighted_laboratory_grade, 2, '.', '') . '</td>';
                        } else {
                            echo '<td class="text-center">0.00</td>';
                        }
                    } else {
                        // For laboratory schedules
                        $lab_lec_grade = DB::table('lab_lec_grades')
                            ->where('schedule_id', '=', $this->detail['schedule_id'])
                            ->where('student_id', '=', $student->id)
                            ->first();

                        // Get lab weight
                        $lab_lec_weight_lab = DB::table('lab_lec')
                            ->where('schedule_id', '=', $this->detail['schedule_id'])
                            ->where('term_id', '=', $this->detail['term_id'])
                            ->first();
                        
                        $lab_weight_percent = $lab_lec_weight_lab ? floatval($lab_lec_weight_lab->sub_weight) : 100;

                        if ($lab_lec_grade != null && floatval($lab_lec_grade->grade)) {
                            $term_weight_percent = $current_term ? $current_term->weight : 100;
                            $actual_lab_grade_percent = ($lab_lec_grade->grade / $lab_lec_grade->sub_weight) * 100;
                            $scaled_laboratory_grade = ($actual_lab_grade_percent / $term_weight_percent) * 10000;
                            
                            // Apply laboratory weight
                            $weighted_laboratory_grade = $scaled_laboratory_grade * ($lab_weight_percent / 100);
                            $laboratory_component = $scaled_laboratory_grade;
                            
                            echo '<td class="text-center">' . number_format($weighted_laboratory_grade, 2, '.', '') . '</td>';
                        } else {
                            echo '<td class="text-center">' . htmlspecialchars($lab_lec_grade ? $lab_lec_grade->other : '0.00') . '</td>';
                        }
                    }
                }

                // Total (Weighted Average) - only for lecture schedules
                if ($this->schedule->is_lec) {
                    $weighted_average = 0;
                    if ($lecture_component !== null && $laboratory_component !== null) {
                        $weighted_average = ($lecture_component * ($lecture_weight_percent / 100)) + 
                                        ($laboratory_component * ($laboratory_weight_percent / 100));
                    } elseif ($lecture_component !== null) {
                        $weighted_average = $lecture_component;
                    }
                    echo '<td class="text-center" style="background-color: #FFF3CD; font-weight: bold;">' .
                        ($weighted_average > 0 ? number_format($weighted_average, 2, '.', '') : '0.00') .
                        '</td>';
                } else {
                    // For lab-only schedules, use laboratory component directly
                    $weighted_average = $laboratory_component ?? 0;
                }

                // Weighted Grade (Point Grade)
                $weighted_grade = '';
                if ($weighted_average > 0) {
                    foreach ($this->point_grade_equivalent as $p_value) {
                        if ($weighted_average >= $p_value->minimum && $weighted_average < $p_value->maximum + 1) {
                            $weighted_grade = $p_value->grade;
                            break;
                        }
                    }
                }
                echo '<td class="text-center">' . htmlspecialchars($weighted_grade) . '</td>';

                // Remarks
                $term_grade_record = DB::table('term_grades')
                    ->where('schedule_id', '=', $this->detail['schedule_id'])
                    ->where('student_id', '=', $student->id)
                    ->where('term_id', '=', $this->detail['term_id'])
                    ->first();

                $remarks = $term_grade_record && $term_grade_record->remarks
                    ? $term_grade_record->remarks
                    : 'N/A';

                $bg_color = match ($remarks) {
                    'PASSED' => '#198754',
                    'FAILED' => '#dc3545',
                    'INC' => '#ffc107',
                    'DROP' => '#6c757d',
                    default => '#f8f9fa'
                };

                $text_color = match ($remarks) {
                    'PASSED' => '#ffffff',
                    'FAILED' => '#ffffff',
                    'INC' => '#000000',
                    'DROP' => '#ffffff',
                    default => '#000000'
                };

                echo '<td class="text-center" style="background-color: ' . $bg_color . '; color: ' . $text_color . '; font-weight: bold;">' .
                    htmlspecialchars($remarks) .
                    '</td>';

                echo '</tr>';
            }

            echo '</tbody></table>';
            echo '<br><p><em>Generated on: ' . now()->timezone('Asia/Manila')->format('F d, Y h:i A') . ' </em></p>';
            echo '</body></html>';
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportCSVLabType()
    {
        // Get all students for this schedule
        $students = DB::table('enrolled_students as es')
            ->select(
                's.id',
                's.code',
                DB::raw('CONCAT_WS(" ", s.first_name, s.middle_name, s.last_name, s.suffix) AS fullname'),
                'c.name as college',
                'd.name as department',
                'yl.year_level'
            )
            ->leftJoin('students as s', 's.id', 'es.student_id')
            ->leftJoin('colleges as c', 'c.id', 's.college_id')
            ->leftJoin('departments as d', 'd.id', 's.department_id')
            ->leftJoin('year_levels as yl', 'yl.id', 's.year_level_id')
            ->where('es.schedule_id', '=', $this->detail['schedule_id'])
            ->orderBy('s.is_active', 'desc')
            ->orderBy('s.id', 'desc')
            ->get();

        // Apply filters
        if (!empty($this->filters['search'])) {
            $students = $students->filter(function ($student) {
                return stripos($student->code, $this->filters['search']) !== false ||
                    stripos($student->fullname, $this->filters['search']) !== false;
            });
        }

        if (!empty($this->filters['remarks'])) {
            $student_ids = DB::table('term_grades')
                ->where('schedule_id', '=', $this->detail['schedule_id'])
                ->where('term_id', '=', $this->detail['term_id'])
                ->where('remarks', '=', $this->filters['remarks'])
                ->pluck('student_id')
                ->toArray();
            $students = $students->whereIn('id', $student_ids);
        }

        // Get current term info
        $current_term = collect($this->terms)->firstWhere('id', $this->detail['term_id']);
        $term_name = $current_term ? $current_term->term_name : 'Term';
        
        // Get lab weight
        $lab_lec_weight_lab = DB::table('lab_lec')
            ->where('schedule_id', '=', $this->detail['schedule_id'])
            ->where('term_id', '=', $this->detail['term_id'])
            ->first();
        
        $lab_weight_percent = $lab_lec_weight_lab ? floatval($lab_lec_weight_lab->sub_weight) : 100;

        // Prepare CSV content
        $filename = 'evaluation_lab_' . $term_name . '_' . $this->school_year . '_' . $this->semester . '_' . now()->timezone('Asia/Manila')->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($students, $term_name, $lab_weight_percent) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header row
            $header = ['#', 'Student Code', 'Student Name', 'College', 'Department', 'Year Level'];

            // Add school work type columns
            foreach ($this->school_work_types as $swt) {
                if ($swt->weight > 0 && $swt->id != $this->current_school_work_type->id) {
                    $header[] = $swt->school_work_type . ' (%)';
                }
            }

            $header[] = 'Total';
            $header[] = 'Laboratory (Weighted ' . number_format($lab_weight_percent, 2) . '%)';
            $header[] = 'Weighted Grade';
            $header[] = 'Remarks';

            fputcsv($file, $header);

            // Get weight totals
            $weight = DB::table('school_works_types')
                ->select(DB::raw('sum(weight) as total_weight'))
                ->where('schedule_id', '=', $this->detail['schedule_id'])
                ->where('term_id', '=', $this->detail['term_id'])
                ->first();

            // Data rows
            $counter = 1;
            foreach ($students as $student) {
                $row = [
                    $counter++,
                    $student->code,
                    $student->fullname,
                    $student->college ?? 'N/A',
                    $student->department ?? 'N/A',
                    $student->year_level ?? 'N/A'
                ];

                // Calculate school work scores
                $total_grade = 0;
                foreach ($this->school_work_types as $swt) {
                    if ($swt->weight > 0 && $swt->id != $this->current_school_work_type->id) {
                        $school_works = DB::table('school_works as sw')
                            ->select('sw.id', 'sw.max_score', 'sws.score')
                            ->leftJoin('school_work_scores as sws', 'sws.school_work_id', 'sw.id')
                            ->where('sw.school_work_type_id', '=', $swt->id)
                            ->where('sw.schedule_id', '=', $this->detail['schedule_id'])
                            ->where('sw.term_id', '=', $this->detail['term_id'])
                            ->where(function ($query) use ($student) {
                                $query->whereNull('sws.student_id')->orWhere('sws.student_id', $student->id);
                            })
                            ->get();

                        $school_work_count = 0;
                        $school_work_average = 0;
                        foreach ($school_works as $sw) {
                            if ($sw->score !== null && $sw->max_score > 0) {
                                $school_work_average += ($sw->score / $sw->max_score);
                                $school_work_count++;
                            }
                        }

                        if ($school_work_count > 0) {
                            $sub_total = $school_work_average / $school_work_count;
                            $row[] = number_format($sub_total * 100, 2, '.', '');
                            $school_work_type_weight = $weight->total_weight ? ($swt->weight / $weight->total_weight * 100) : 0;
                            $total_grade += ($sub_total * $school_work_type_weight / 100);
                        } else {
                            $row[] = '';
                        }
                    }
                }

                // Total column
                $row[] = $total_grade > 0 ? number_format($total_grade * 100, 2, '.', '') : '';

                // Laboratory Grade (Weighted)
                $lab_lec_grade = DB::table('lab_lec_grades')
                    ->where('schedule_id', '=', $this->detail['schedule_id'])
                    ->where('student_id', '=', $student->id)
                    ->first();

                $laboratory_component = null;
                if ($lab_lec_grade != null && floatval($lab_lec_grade->grade)) {
                    $current_term = collect($this->terms)->firstWhere('id', $this->detail['term_id']);
                    $term_weight_percent = $current_term ? $current_term->weight : 100;
                    $actual_lab_grade_percent = ($lab_lec_grade->grade / $lab_lec_grade->sub_weight) * 100;
                    $scaled_laboratory_grade = ($actual_lab_grade_percent / $term_weight_percent) * 10000;
                    
                    // Apply laboratory weight
                    $weighted_laboratory_grade = $scaled_laboratory_grade * ($lab_weight_percent / 100);
                    $laboratory_component = $scaled_laboratory_grade;
                    
                    $row[] = number_format($weighted_laboratory_grade, 2, '.', '');
                } else {
                    $row[] = $lab_lec_grade ? $lab_lec_grade->other : '0.00';
                }

                $weighted_average = $laboratory_component ?? 0;

                // Weighted Grade
                $weighted_grade = '';
                if ($weighted_average > 0) {
                    foreach ($this->point_grade_equivalent as $p_value) {
                        if ($weighted_average >= $p_value->minimum && $weighted_average < $p_value->maximum + 1) {
                            $weighted_grade = $p_value->grade;
                            break;
                        }
                    }
                }
                $row[] = $weighted_grade;

                // Remarks
                $term_grade_record = DB::table('term_grades')
                    ->where('schedule_id', '=', $this->detail['schedule_id'])
                    ->where('student_id', '=', $student->id)
                    ->where('term_id', '=', $this->detail['term_id'])
                    ->first();

                $row[] = $term_grade_record && $term_grade_record->remarks ? $term_grade_record->remarks : 'N/A';

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportExcelLabType()
    {
        // Get all students for this schedule
        $students = DB::table('enrolled_students as es')
            ->select(
                's.id',
                's.code',
                DB::raw('CONCAT_WS(" ", s.first_name, s.middle_name, s.last_name, s.suffix) AS fullname'),
                'c.name as college',
                'd.name as department',
                'yl.year_level'
            )
            ->leftJoin('students as s', 's.id', 'es.student_id')
            ->leftJoin('colleges as c', 'c.id', 's.college_id')
            ->leftJoin('departments as d', 'd.id', 's.department_id')
            ->leftJoin('year_levels as yl', 'yl.id', 's.year_level_id')
            ->where('es.schedule_id', '=', $this->detail['schedule_id'])
            ->orderBy('s.is_active', 'desc')
            ->orderBy('s.id', 'desc')
            ->get();

        // Apply filters
        if (!empty($this->filters['search'])) {
            $students = $students->filter(function ($student) {
                return stripos($student->code, $this->filters['search']) !== false ||
                    stripos($student->fullname, $this->filters['search']) !== false;
            });
        }

        if (!empty($this->filters['remarks'])) {
            $student_ids = DB::table('term_grades')
                ->where('schedule_id', '=', $this->detail['schedule_id'])
                ->where('term_id', '=', $this->detail['term_id'])
                ->where('remarks', '=', $this->filters['remarks'])
                ->pluck('student_id')
                ->toArray();
            $students = $students->whereIn('id', $student_ids);
        }

        // Get current term info
        $current_term = collect($this->terms)->firstWhere('id', $this->detail['term_id']);
        $term_name = $current_term ? $current_term->term_name : 'Term';
        
        // Get lab weight
        $lab_lec_weight_lab = DB::table('lab_lec')
            ->where('schedule_id', '=', $this->detail['schedule_id'])
            ->where('term_id', '=', $this->detail['term_id'])
            ->first();
        
        $lab_weight_percent = $lab_lec_weight_lab ? floatval($lab_lec_weight_lab->sub_weight) : 100;

        // Create Excel content
        $filename = 'evaluation_lab_' . $term_name . '_' . $this->school_year . '_' . $this->semester . '_' . now()->timezone('Asia/Manila')->format('Y-m-d_His') . '.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($students, $term_name, $lab_weight_percent) {
            echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
            echo '<head>';
            echo '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
            echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>';
            echo '<x:Name>Evaluation</x:Name>';
            echo '<x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet>';
            echo '</x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
            echo '<style>';
            echo 'table { border-collapse: collapse; width: 100%; }';
            echo 'th, td { border: 1px solid black; padding: 8px; text-align: left; }';
            echo 'th { background-color: #952323; color: white; font-weight: bold; }';
            echo '.text-center { text-align: center; }';
            echo '</style>';
            echo '</head><body>';

            // Add title and schedule info
            echo '<h2>' . htmlspecialchars($term_name) . ' Evaluation Report (Laboratory)</h2>';
            echo '<p><strong>School Year:</strong> ' . htmlspecialchars($this->school_year) . '</p>';
            echo '<p><strong>Semester:</strong> ' . htmlspecialchars($this->semester) . '</p>';
            if ($this->schedule) {
                echo '<p><strong>Subject:</strong> ' . htmlspecialchars($this->schedule->subject) . '</p>';
                echo '<p><strong>Faculty:</strong> ' . htmlspecialchars($this->schedule->faculty_fullname) . '</p>';
            }
            echo '<p><strong>Laboratory Weight:</strong> ' . number_format($lab_weight_percent, 2) . '%</p>';
            echo '<br>';

            echo '<table>';

            // Header row
            echo '<thead><tr>';
            echo '<th>#</th>';
            echo '<th>Student Code</th>';
            echo '<th>Student Name</th>';
            echo '<th>College</th>';
            echo '<th>Department</th>';
            echo '<th>Year Level</th>';

            // Add school work type columns dynamically
            foreach ($this->school_work_types as $swt) {
                if ($swt->weight > 0 && $swt->id != $this->current_school_work_type->id) {
                    echo '<th>' . htmlspecialchars($swt->school_work_type) . ' (%)</th>';
                }
            }

            echo '<th>Total</th>';
            echo '<th>Laboratory (Weighted ' . number_format($lab_weight_percent, 2) . '%)</th>';
            echo '<th>Weighted Grade</th>';
            echo '<th>Remarks</th>';
            echo '</tr></thead><tbody>';

            // Get weight totals
            $weight = DB::table('school_works_types')
                ->select(DB::raw('sum(weight) as total_weight'))
                ->where('schedule_id', '=', $this->detail['schedule_id'])
                ->where('term_id', '=', $this->detail['term_id'])
                ->first();

            // Data rows
            $counter = 1;
            foreach ($students as $student) {
                echo '<tr>';
                echo '<td class="text-center">' . $counter++ . '</td>';
                echo '<td>' . htmlspecialchars($student->code) . '</td>';
                echo '<td>' . htmlspecialchars($student->fullname) . '</td>';
                echo '<td>' . htmlspecialchars($student->college ?? 'N/A') . '</td>';
                echo '<td>' . htmlspecialchars($student->department ?? 'N/A') . '</td>';
                echo '<td>' . htmlspecialchars($student->year_level ?? 'N/A') . '</td>';

                // Calculate school work scores
                $total_grade = 0;
                foreach ($this->school_work_types as $key => $swt) {
                    if ($swt->weight > 0 && $swt->id != $this->current_school_work_type->id) {
                        $school_works = DB::table('school_works as sw')
                            ->select('sw.id', 'sw.max_score', 'sws.score')
                            ->leftJoin('school_work_scores as sws', 'sws.school_work_id', 'sw.id')
                            ->where('sw.school_work_type_id', '=', $swt->id)
                            ->where('sw.schedule_id', '=', $this->detail['schedule_id'])
                            ->where('sw.term_id', '=', $this->detail['term_id'])
                            ->where(function ($query) use ($student) {
                                $query->whereNull('sws.student_id')
                                    ->orWhere('sws.student_id', $student->id);
                            })
                            ->get();

                        $school_work_count = 0;
                        $school_work_average = 0;
                        foreach ($school_works as $sw) {
                            if ($sw->score !== null && $sw->max_score > 0) {
                                $school_work_average += ($sw->score / $sw->max_score);
                                $school_work_count++;
                            }
                        }

                        if ($school_work_count > 0) {
                            $sub_total = $school_work_average / $school_work_count;
                            echo '<td class="text-center">' . number_format($sub_total * 100, 2, '.', '') . '</td>';
                            $school_work_type_weight = $weight->total_weight ? ($swt->weight / $weight->total_weight * 100) : 0;
                            $total_grade += ($sub_total * $school_work_type_weight / 100);
                        } else {
                            echo '<td class="text-center"></td>';
                        }
                    }
                }

                // Total column
                echo '<td class="text-center">' . ($total_grade > 0 ? number_format($total_grade * 100, 2, '.', '') : '') . '</td>';

                // Laboratory Grade (Weighted)
                $lab_lec_grade = DB::table('lab_lec_grades')
                    ->where('schedule_id', '=', $this->detail['schedule_id'])
                    ->where('student_id', '=', $student->id)
                    ->first();

                $laboratory_component = null;
                if ($lab_lec_grade != null && floatval($lab_lec_grade->grade)) {
                    $current_term = collect($this->terms)->firstWhere('id', $this->detail['term_id']);
                    $term_weight_percent = $current_term ? $current_term->weight : 100;
                    $actual_lab_grade_percent = ($lab_lec_grade->grade / $lab_lec_grade->sub_weight) * 100;
                    $scaled_laboratory_grade = ($actual_lab_grade_percent / $term_weight_percent) * 10000;
                    
                    // Apply laboratory weight
                    $weighted_laboratory_grade = $scaled_laboratory_grade * ($lab_weight_percent / 100);
                    $laboratory_component = $scaled_laboratory_grade;
                    
                    echo '<td class="text-center">' . number_format($weighted_laboratory_grade, 2, '.', '') . '</td>';
                } else {
                    echo '<td class="text-center">' . htmlspecialchars($lab_lec_grade ? $lab_lec_grade->other : '0.00') . '</td>';
                }

                $weighted_average = $laboratory_component ?? 0;

                // Weighted Grade (Point Grade)
                $weighted_grade = '';
                if ($weighted_average > 0) {
                    foreach ($this->point_grade_equivalent as $p_value) {
                        if ($weighted_average >= $p_value->minimum && $weighted_average < $p_value->maximum + 1) {
                            $weighted_grade = $p_value->grade;
                            break;
                        }
                    }
                }
                echo '<td class="text-center">' . htmlspecialchars($weighted_grade) . '</td>';

                // Remarks
                $term_grade_record = DB::table('term_grades')
                    ->where('schedule_id', '=', $this->detail['schedule_id'])
                    ->where('student_id', '=', $student->id)
                    ->where('term_id', '=', $this->detail['term_id'])
                    ->first();

                $remarks = $term_grade_record && $term_grade_record->remarks
                    ? $term_grade_record->remarks
                    : 'N/A';

                $bg_color = match ($remarks) {
                    'PASSED' => '#198754',
                    'FAILED' => '#dc3545',
                    'INC' => '#ffc107',
                    'DROP' => '#6c757d',
                    default => '#f8f9fa'
                };

                $text_color = match ($remarks) {
                    'PASSED' => '#ffffff',
                    'FAILED' => '#ffffff',
                    'INC' => '#000000',
                    'DROP' => '#ffffff',
                    default => '#000000'
                };

                echo '<td class="text-center" style="background-color: ' . $bg_color . '; color: ' . $text_color . '; font-weight: bold;">' .
                    htmlspecialchars($remarks) .
                    '</td>';

                echo '</tr>';
            }

            echo '</tbody></table>';
            echo '<br><p><em>Generated on: ' . now()->timezone('Asia/Manila')->format('F d, Y h:i A') . ' </em></p>';
            echo '</body></html>';
        };

        return response()->stream($callback, 200, $headers);
    }

    public function storeLaboratoryValues()
{
    // Create table if it doesn't exist
    DB::statement("
        CREATE TABLE IF NOT EXISTS lab_values (
            id INT AUTO_INCREMENT PRIMARY KEY,
            student_id INT NOT NULL,
            schedule_id INT NOT NULL,
            term_id INT NOT NULL,
            term_type VARCHAR(50) NOT NULL,
            value_lab DECIMAL(10, 2) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY unique_lab_value (student_id, schedule_id, term_id)
        )
    ");

    // Handle LABORATORY schedules (is_lec = 0)
    if ($this->schedule && $this->schedule->is_lec == 0) {
        // Get all enrolled students for this schedule
        $students = DB::table('enrolled_students as es')
            ->select('s.id')
            ->leftJoin('students as s', 's.id', 'es.student_id')
            ->where('es.schedule_id', '=', $this->detail['schedule_id'])
            ->get();

        // Get all terms for this schedule
        $all_terms = DB::table('terms')
            ->where('schedule_id', '=', $this->detail['schedule_id'])
            ->orderBy('term_order', 'asc')
            ->get();

        // Get weight totals for school work types
        $weight = DB::table('school_works_types')
            ->select(DB::raw('sum(weight) as total_weight'))
            ->where('schedule_id', '=', $this->detail['schedule_id'])
            ->where('term_id', '=', $this->detail['term_id'])
            ->first();

        foreach ($students as $student) {
            foreach ($all_terms as $term) {
                $term_type = $term->term_name; // Midterm, Finalterm, etc.
                
                // Calculate total grade from school work types for this term
                $total_grade = 0;
                $has_scores = false;
                $has_null_scores = false;
                
                // Get school work types for this term
                $school_work_types_for_term = DB::table('school_works_types')
                    ->where('schedule_id', '=', $this->detail['schedule_id'])
                    ->where('term_id', '=', $term->id)
                    ->orderBy('number_order', 'asc')
                    ->get();
                
                foreach ($school_work_types_for_term as $swt) {
                    if ($swt->weight > 0 && $swt->id != $this->current_school_work_type->id) {
                        $school_works = DB::table('school_works as sw')
                            ->select('sw.id', 'sw.max_score', 'sws.score')
                            ->leftJoin('school_work_scores as sws', 'sws.school_work_id', 'sw.id')
                            ->where('sw.school_work_type_id', '=', $swt->id)
                            ->where('sw.schedule_id', '=', $this->detail['schedule_id'])
                            ->where('sw.term_id', '=', $term->id)
                            ->where(function ($query) use ($student) {
                                $query->whereNull('sws.student_id')
                                    ->orWhere('sws.student_id', $student->id);
                            })
                            ->get();

                        $school_work_count = 0;
                        $school_work_average = 0;
                        
                        foreach ($school_works as $sw) {
                            if ($sw->score !== null && $sw->max_score > 0) {
                                $school_work_average += ($sw->score / $sw->max_score);
                                $school_work_count++;
                                $has_scores = true;
                            } elseif ($sw->score === null) {
                                $has_null_scores = true;
                            }
                        }

                        if ($school_work_count > 0) {
                            $sub_total = $school_work_average / $school_work_count;
                            $school_work_type_weight = $weight->total_weight ? ($swt->weight / $weight->total_weight * 100) : 0;
                            $total_grade += ($sub_total * $school_work_type_weight / 100);
                        }
                    }
                }

                // Convert to percentage and scale (total_grade is in decimal form)
                $value_lab = $total_grade > 0 ? ($total_grade * 100) : null;
                
                // Check for INC or DROP status
                $term_grade_status = DB::table('term_grades')
                    ->where('schedule_id', '=', $this->detail['schedule_id'])
                    ->where('student_id', '=', $student->id)
                    ->where('term_id', '=', $term->id)
                    ->first();
                
                // Determine status based on scores
                if ($has_null_scores && $has_scores) {
                    $term_type = 'INC';
                    $value_lab = null;
                } elseif (!$has_scores) {
                    $term_type = 'DROP';
                    $value_lab = null;
                } elseif ($term_grade_status && $term_grade_status->other) {
                    $term_type = $term_grade_status->other;
                    $value_lab = null;
                }

                // Insert or update the lab value
                DB::statement("
                    INSERT INTO lab_values (student_id, schedule_id, term_id, term_type, value_lab)
                    VALUES (?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                        term_type = VALUES(term_type),
                        value_lab = VALUES(value_lab),
                        updated_at = CURRENT_TIMESTAMP
                ", [
                    $student->id,
                    $this->detail['schedule_id'],
                    $term->id,
                    $term_type,
                    $value_lab
                ]);
            }
        }
        
        return;
    }

    // Handle LECTURE schedules with laboratory component (existing logic)
    if (!$this->schedule || $this->schedule->is_lec != 1 || $this->schedule->laboratory_unit <= 0) {
        return;
    }

    // Only proceed if we have laboratory schedules
    if (count($this->laboratory_schedules) <= 0) {
        return;
    }

    // Get all enrolled students for this schedule
    $students = DB::table('enrolled_students as es')
        ->select('s.id')
        ->leftJoin('students as s', 's.id', 'es.student_id')
        ->where('es.schedule_id', '=', $this->detail['schedule_id'])
        ->get();

    // Get all terms for this schedule
    $all_terms = DB::table('terms')
        ->where('schedule_id', '=', $this->detail['schedule_id'])
        ->orderBy('term_order', 'asc')
        ->get();

    // Get corresponding laboratory schedule terms
    $lab_schedule_id = $this->laboratory_schedules[0]->id;
    $lab_terms = DB::table('terms')
        ->where('schedule_id', '=', $lab_schedule_id)
        ->orderBy('term_order', 'asc')
        ->get();

    foreach ($students as $student) {
        foreach ($all_terms as $key => $term) {
            $term_type = $term->term_name;
            
            // Get corresponding laboratory term
            $lab_term = $lab_terms[$key] ?? null;
            
            if (!$lab_term) {
                continue;
            }

            // Get laboratory grade from the laboratory schedule for this term
            $lab_lec_grade = DB::table('lab_lec_grades')
                ->where('schedule_id', '=', $lab_schedule_id)
                ->where('student_id', '=', $student->id)
                ->first();

            // Get lab weight for the laboratory schedule
            $lab_lec_weight = DB::table('lab_lec')
                ->where('schedule_id', '=', $lab_schedule_id)
                ->where('term_id', '=', $lab_term->id)
                ->first();

            $value_lab = null;

            // Calculate the laboratory value
            if ($lab_lec_grade && floatval($lab_lec_grade->grade) && $lab_lec_weight) {
                $lab_term_grade = DB::table('term_grades')
                    ->where('schedule_id', '=', $lab_schedule_id)
                    ->where('student_id', '=', $student->id)
                    ->where('term_id', '=', $lab_term->id)
                    ->first();

                if ($lab_term_grade && floatval($lab_term_grade->grade)) {
                    $term_weight_percent = $term->weight;
                    $actual_lab_grade_percent = ($lab_term_grade->grade / $lab_lec_weight->sub_weight) * 100;
                    $value_lab = ($actual_lab_grade_percent / $term_weight_percent) * 10000;
                }
            }

            // Check for INC or DROP status
            $lab_term_grade_status = DB::table('term_grades')
                ->where('schedule_id', '=', $lab_schedule_id)
                ->where('student_id', '=', $student->id)
                ->where('term_id', '=', $lab_term->id)
                ->first();

            if ($lab_term_grade_status && $lab_term_grade_status->other) {
                $value_lab = null;
                $term_type = $lab_term_grade_status->other;
            }

            // Insert or update the lab value
            DB::statement("
                INSERT INTO lab_values (student_id, schedule_id, term_id, term_type, value_lab)
                VALUES (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE
                    term_type = VALUES(term_type),
                    value_lab = VALUES(value_lab),
                    updated_at = CURRENT_TIMESTAMP
            ", [
                $student->id,
                $this->detail['schedule_id'],
                $term->id,
                $term_type,
                $value_lab
            ]);
        }
    }
}

}