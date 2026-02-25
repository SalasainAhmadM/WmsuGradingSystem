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
    /**
     * Shared helper: compute one student's row data exactly as the blade does.
     * Returns an array with keys:
     *   swt_cols       => [ ['label'=>..., 'value'=>...], ... ]
     *   total_grade    => float (0-100 scale, i.e. $total_grade*100)
     *   lecture_value  => float|null
     *   lab_component  => float|null   (unweighted, 0-100 scale)
     *   lab_value      => float|null   (weighted)
     *   weighted_avg   => float
     *   weighted_grade => string
     *   remark         => string
     */
    private function calcStudentRow(
        $student,
        array $school_work_types,
        $total_weight_obj,
        $lecture_weight_pct,
        $laboratory_weight_pct,
        $lab_weight_pct_lab   // used only when is_lec == 0
    ): array {
        // ── 1. School-work-type sub-totals (same loop as blade) ─────────────
        $swt_cols       = [];
        $total_grade    = 0.0;
        $sub_average    = 0.0;
        $null_count     = 0;
        $scored_count   = 0;

        foreach ($school_work_types as $swt) {
            if ($swt->weight <= 0 || $swt->id == $this->current_school_work_type->id) {
                continue;
            }

            $school_works = DB::table('school_works as sw')
                ->select('sw.id', 'sw.max_score', 'sws.score')
                ->leftJoin('school_work_scores as sws', 'sws.school_work_id', 'sw.id')
                ->where('sw.school_work_type_id', $swt->id)
                ->where('sw.schedule_id', $this->detail['schedule_id'])
                ->where('sw.term_id', $this->detail['term_id'])
                ->where(function ($q) use ($student) {
                    $q->whereNull('sws.student_id')->orWhere('sws.student_id', $student->id);
                })
                ->get();

            // Mirrors blade exactly:
            // $school_work_type_count  → total items (denominator)
            // $sub_average             → sum of (score/max) only for intval(score) truthy
            // $temp_sub_total_score    → sum of raw scores (used for @if check)
            $sw_total_count = 0;
            $sw_score_sum   = 0.0;
            $sw_raw_sum     = 0.0;

            foreach ($school_works as $sw) {
                $sw_total_count++;
                if ($sw->score !== null) {
                    $sw_raw_sum += floatval($sw->score);
                    // blade: if(intval($v_value['score'])) — skips null AND zero
                    if (intval($sw->score) && $sw->max_score > 0) {
                        $sw_score_sum += $sw->score / $sw->max_score;
                    }
                    $scored_count++;
                } else {
                    $null_count++;
                }
            }

            $swt_weight_pct = $total_weight_obj->total_weight
                ? ($swt->weight / $total_weight_obj->total_weight * 100)
                : 0;

            // blade: @if($sub_total_score) — only render if raw sum > 0
            // blade avg = $sub_average / $school_work_type_count_prev
            //           = score_sum / TOTAL item count (not just scored count)
            if ($sw_raw_sum > 0 && $sw_total_count > 0) {
                $sub_total   = $sw_score_sum / $sw_total_count;
                $col_display = number_format($sub_total * 100, 2, '.', '');
                $total_grade += $sub_total * $swt_weight_pct / 100;
            } else {
                $sub_total   = 0;
                $col_display = '';
            }

            $swt_cols[] = [
                'label' => $swt->school_work_type,
                'value' => $col_display,
            ];
        }

        $total_grade_pct = $total_grade * 100; // now on 0-100 scale (matches blade)

        // ── 2. Lecture / Lab values (mirrors blade @php blocks) ──────────────
        $lecture_value      = null;
        $lab_component      = null; // unweighted, 0-100 scale
        $lab_value_weighted = null;

        if ($this->schedule->is_lec) {
            // Lecture = Total × (lecture_weight / 100)
            if ($total_grade_pct > 0) {
                $lecture_value = $total_grade_pct * ($lecture_weight_pct / 100);
            }

            // Laboratory from lab_values table
            if ($this->schedule->laboratory_unit > 0) {
                $current_term = collect($this->terms)->firstWhere('id', $this->detail['term_id']);
                $term_type    = $current_term ? $current_term->term_name : 'Midterm';

                $lv = DB::table('lab_values')
                    ->where('student_id', $student->id)
                    ->where('term_type', $term_type)
                    ->first();

                if ($lv && floatval($lv->value_lab)) {
                    $lab_component      = floatval($lv->value_lab);          // unweighted
                    $lab_value_weighted = $lab_component * ($laboratory_weight_pct / 100);
                }
            }
        } else {
            // Pure lab schedule: Lab = Total × (lab_weight / 100)
            if ($total_grade_pct > 0) {
                $lab_component      = $total_grade_pct;                        // unweighted = Total
                $lab_value_weighted = $total_grade_pct * ($lab_weight_pct_lab / 100);
            }
        }

        // ── 3. Weighted average (mirrors blade) ──────────────────────────────
        $weighted_avg = 0.0;

        if ($this->schedule->is_lec) {
            if ($lecture_value !== null && $lab_component !== null) {
                $weighted_avg = $lecture_value + ($lab_component * ($laboratory_weight_pct / 100));
            } elseif ($lecture_value !== null) {
                $weighted_avg = $lecture_value;
            }
        } else {
            // For pure lab: weighted_avg = Total (same as blade)
            $weighted_avg = $total_grade_pct;
        }

        // ── 4. Incomplete / Drop check (mirrors blade remark logic) ──────────
        $total_sw_count = $scored_count + $null_count;

        if ($total_sw_count > 0 && $null_count === $total_sw_count) {
            $remark = 'DROP';
        } elseif ($null_count > 0 && $null_count < $total_sw_count) {
            $remark = 'INC';
        } elseif ($weighted_avg === 0.0) {
            $remark = 'INC';
        } elseif ($weighted_avg >= 100 && $weighted_avg <= 300) {
            $remark = 'PASSED';
        } elseif ($weighted_avg == 500 || $weighted_avg < 60) {
            $remark = 'FAILED';
        } else {
            $remark = 'PASSED';
        }

        // ── 5. Point-grade equivalent ─────────────────────────────────────────
        $weighted_grade = '';
        if ($weighted_avg > 0) {
            foreach ($this->point_grade_equivalent as $p) {
                if ($weighted_avg >= $p->minimum && $weighted_avg < $p->maximum + 1) {
                    $weighted_grade = $p->grade;
                    break;
                }
            }
        }

        // Blank out Total / Weighted Grade for INC and DROP (matches blade `$is_incomplete` check)
        $is_incomplete = in_array($remark, ['INC', 'DROP']);
        if ($is_incomplete) {
            $weighted_avg   = 0.0;
            $weighted_grade = '';
        }

        return compact(
            'swt_cols', 'total_grade_pct',
            'lecture_value', 'lab_component', 'lab_value_weighted',
            'weighted_avg', 'weighted_grade', 'remark', 'is_incomplete'
        );
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // EXPORT CSV  (Lecture schedule)
    // ─────────────────────────────────────────────────────────────────────────────
    public function exportCSV()
    {
        $students = $this->getExportStudents();

        $current_term = collect($this->terms)->firstWhere('id', $this->detail['term_id']);
        $term_name    = $current_term ? $current_term->term_name : 'Term';

        [$lec_pct, $lab_pct] = $this->getLabLecPcts();

        $filename = 'evaluation_' . $term_name . '_' . $this->school_year . '_' . $this->semester
            . '_' . now()->timezone('Asia/Manila')->format('Y-m-d_His') . '.csv';

        $weight = $this->getTotalWeight();

        return response()->stream(function () use ($students, $term_name, $lec_pct, $lab_pct, $weight) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header
            $header = ['#', 'Student Code', 'Student Name', 'College', 'Department', 'Year Level'];
            foreach ($this->school_work_types as $swt) {
                if ($swt->weight > 0 && $swt->id != $this->current_school_work_type->id) {
                    $header[] = $swt->school_work_type . ' (%)';
                }
            }
            $header[] = 'Total';
            if ($this->schedule->is_lec)                                         $header[] = 'Lecture (Weighted ' . number_format($lec_pct, 2) . '%)';
            if ($this->schedule->laboratory_unit > 0 || !$this->schedule->is_lec) $header[] = 'Laboratory (Weighted ' . number_format($lab_pct, 2) . '%)';
            if ($this->schedule->is_lec)                                         $header[] = 'Total (Weighted Avg)';
            $header[] = 'Weighted Grade';
            $header[] = 'Remarks';
            fputcsv($file, $header);

            // Rows
            $lab_weight_pct_lab = $this->getLabOnlyPct();
            $i = 1;
            foreach ($students as $student) {
                $row = $this->calcStudentRow($student, $this->school_work_types, $weight, $lec_pct, $lab_pct, $lab_weight_pct_lab);

                $line = [
                    $i++,
                    $student->code,
                    $student->fullname,
                    $student->college   ?? 'N/A',
                    $student->department ?? 'N/A',
                    $student->year_level ?? 'N/A',
                ];
                foreach ($row['swt_cols'] as $col) $line[] = $col['value'];
                $line[] = $row['total_grade_pct'] > 0 ? number_format($row['total_grade_pct'], 2, '.', '') : '';

                if ($this->schedule->is_lec) {
                    $line[] = $row['lecture_value'] !== null ? number_format($row['lecture_value'], 2, '.', '') : '';
                }
                if ($this->schedule->laboratory_unit > 0 || !$this->schedule->is_lec) {
                    $line[] = $row['lab_value_weighted'] !== null ? number_format($row['lab_value_weighted'], 2, '.', '') : '0.00';
                }
                if ($this->schedule->is_lec) {
                    // Blank for INC / DROP
                    $line[] = $row['is_incomplete'] ? '' : ($row['weighted_avg'] > 0 ? number_format($row['weighted_avg'], 2, '.', '') : '0.00');
                }
                $line[] = $row['is_incomplete'] ? '' : $row['weighted_grade']; // blank for INC/DROP
                $line[] = $row['remark'];

                fputcsv($file, $line);
            }
            fclose($file);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // EXPORT EXCEL (Lecture schedule)
    // ─────────────────────────────────────────────────────────────────────────────
    public function exportExcel()
    {
        $students = $this->getExportStudents();

        $current_term = collect($this->terms)->firstWhere('id', $this->detail['term_id']);
        $term_name    = $current_term ? $current_term->term_name : 'Term';

        [$lec_pct, $lab_pct] = $this->getLabLecPcts();

        $filename = 'evaluation_' . $term_name . '_' . $this->school_year . '_' . $this->semester
            . '_' . now()->timezone('Asia/Manila')->format('Y-m-d_His') . '.xls';

        $weight             = $this->getTotalWeight();
        $lab_weight_pct_lab = $this->getLabOnlyPct();

        return response()->stream(function () use ($students, $term_name, $lec_pct, $lab_pct, $weight, $lab_weight_pct_lab) {
            echo $this->excelHead($term_name);
            echo '<h2>' . e($term_name) . ' Evaluation Report</h2>';
            echo '<p><strong>School Year:</strong> ' . e($this->school_year) . '</p>';
            echo '<p><strong>Semester:</strong> ' . e($this->semester) . '</p>';
            if ($this->schedule) {
                echo '<p><strong>Subject:</strong> ' . e($this->schedule->subject) . '</p>';
                echo '<p><strong>Faculty:</strong> ' . e($this->schedule->faculty_fullname) . '</p>';
            }
            echo '<p><strong>Lab/Lecture Weight:</strong> Lecture ' . number_format($lec_pct, 2) . '% / Laboratory ' . number_format($lab_pct, 2) . '%</p><br>';
            echo '<table><thead><tr>';
            echo '<th>#</th><th>Student Code</th><th>Student Name</th><th>College</th><th>Department</th><th>Year Level</th>';
            foreach ($this->school_work_types as $swt) {
                if ($swt->weight > 0 && $swt->id != $this->current_school_work_type->id)
                    echo '<th>' . e($swt->school_work_type) . ' (%)</th>';
            }
            echo '<th>Total</th>';
            if ($this->schedule->is_lec)                                          echo '<th>Lecture (Weighted ' . number_format($lec_pct, 2) . '%)</th>';
            if ($this->schedule->laboratory_unit > 0 || !$this->schedule->is_lec) echo '<th>Laboratory (Weighted ' . number_format($lab_pct, 2) . '%)</th>';
            if ($this->schedule->is_lec)                                          echo '<th>Total (Weighted Avg)</th>';
            echo '<th>Weighted Grade</th><th>Remarks</th></tr></thead><tbody>';

            $i = 1;
            foreach ($students as $student) {
                $row = $this->calcStudentRow($student, $this->school_work_types, $weight, $lec_pct, $lab_pct, $lab_weight_pct_lab);
                echo '<tr>';
                echo '<td>' . $i++ . '</td>';
                echo '<td>' . e($student->code) . '</td>';
                echo '<td>' . e($student->fullname) . '</td>';
                echo '<td>' . e($student->college   ?? 'N/A') . '</td>';
                echo '<td>' . e($student->department ?? 'N/A') . '</td>';
                echo '<td>' . e($student->year_level ?? 'N/A') . '</td>';

                foreach ($row['swt_cols'] as $col)
                    echo '<td class="text-center">' . e($col['value']) . '</td>';

                echo '<td class="text-center">' . ($row['total_grade_pct'] > 0 ? number_format($row['total_grade_pct'], 2, '.', '') : '') . '</td>';

                if ($this->schedule->is_lec) {
                    echo '<td class="text-center">' . ($row['lecture_value'] !== null ? number_format($row['lecture_value'], 2, '.', '') : '') . '</td>';
                }
                if ($this->schedule->laboratory_unit > 0 || !$this->schedule->is_lec) {
                    echo '<td class="text-center">' . ($row['lab_value_weighted'] !== null ? number_format($row['lab_value_weighted'], 2, '.', '') : '0.00') . '</td>';
                }
                if ($this->schedule->is_lec) {
                    // Blank for INC / DROP
                    $total_display = $row['is_incomplete'] ? '' : ($row['weighted_avg'] > 0 ? number_format($row['weighted_avg'], 2, '.', '') : '0.00');
                    echo '<td class="text-center" style="background-color:#FFF3CD;font-weight:bold;">' . $total_display . '</td>';
                }
                echo '<td class="text-center">' . e($row['is_incomplete'] ? '' : $row['weighted_grade']) . '</td>';
                echo '<td class="text-center" style="' . $this->remarkStyle($row['remark']) . '">' . e($row['remark']) . '</td>';
                echo '</tr>';
            }

            echo '</tbody></table>';
            echo '<br><p><em>Generated on: ' . now()->timezone('Asia/Manila')->format('F d, Y h:i A') . '</em></p>';
            echo '</body></html>';
        }, 200, [
            'Content-Type'        => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // EXPORT CSV  (Laboratory schedule)
    // ─────────────────────────────────────────────────────────────────────────────
    public function exportCSVLabType()
    {
        $students = $this->getExportStudents();

        $current_term = collect($this->terms)->firstWhere('id', $this->detail['term_id']);
        $term_name    = $current_term ? $current_term->term_name : 'Term';
        $lab_pct      = $this->getLabOnlyPct();

        $filename = 'evaluation_lab_' . $term_name . '_' . $this->school_year . '_' . $this->semester
            . '_' . now()->timezone('Asia/Manila')->format('Y-m-d_His') . '.csv';

        $weight = $this->getTotalWeight();

        return response()->stream(function () use ($students, $term_name, $lab_pct, $weight) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            $header = ['#', 'Student Code', 'Student Name', 'College', 'Department', 'Year Level'];
            foreach ($this->school_work_types as $swt) {
                if ($swt->weight > 0 && $swt->id != $this->current_school_work_type->id)
                    $header[] = $swt->school_work_type . ' (%)';
            }
            $header[] = 'Total';
            $header[] = 'Laboratory (Weighted ' . number_format($lab_pct, 2) . '%)';
            $header[] = 'Weighted Grade';
            $header[] = 'Remarks';
            fputcsv($file, $header);

            $i = 1;
            foreach ($students as $student) {
                // For lab-only: lec_pct unused; lab_weight_pct_lab = lab_pct
                $row = $this->calcStudentRow($student, $this->school_work_types, $weight, 0, 0, $lab_pct);

                $line = [
                    $i++,
                    $student->code,
                    $student->fullname,
                    $student->college   ?? 'N/A',
                    $student->department ?? 'N/A',
                    $student->year_level ?? 'N/A',
                ];
                foreach ($row['swt_cols'] as $col) $line[] = $col['value'];
                $line[] = $row['total_grade_pct'] > 0 ? number_format($row['total_grade_pct'], 2, '.', '') : '';
                $line[] = $row['lab_value_weighted'] !== null ? number_format($row['lab_value_weighted'], 2, '.', '') : '0.00';
                $line[] = $row['is_incomplete'] ? '' : $row['weighted_grade']; // blank for INC/DROP
                $line[] = $row['remark'];

                fputcsv($file, $line);
            }
            fclose($file);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // EXPORT EXCEL (Laboratory schedule)
    // ─────────────────────────────────────────────────────────────────────────────
    public function exportExcelLabType()
    {
        $students = $this->getExportStudents();

        $current_term = collect($this->terms)->firstWhere('id', $this->detail['term_id']);
        $term_name    = $current_term ? $current_term->term_name : 'Term';
        $lab_pct      = $this->getLabOnlyPct();

        $filename = 'evaluation_lab_' . $term_name . '_' . $this->school_year . '_' . $this->semester
            . '_' . now()->timezone('Asia/Manila')->format('Y-m-d_His') . '.xls';

        $weight = $this->getTotalWeight();

        return response()->stream(function () use ($students, $term_name, $lab_pct, $weight) {
            echo $this->excelHead($term_name);
            echo '<h2>' . e($term_name) . ' Evaluation Report (Laboratory)</h2>';
            echo '<p><strong>School Year:</strong> ' . e($this->school_year) . '</p>';
            echo '<p><strong>Semester:</strong> ' . e($this->semester) . '</p>';
            if ($this->schedule) {
                echo '<p><strong>Subject:</strong> ' . e($this->schedule->subject) . '</p>';
                echo '<p><strong>Faculty:</strong> ' . e($this->schedule->faculty_fullname) . '</p>';
            }
            echo '<p><strong>Laboratory Weight:</strong> ' . number_format($lab_pct, 2) . '%</p><br>';
            echo '<table><thead><tr>';
            echo '<th>#</th><th>Student Code</th><th>Student Name</th><th>College</th><th>Department</th><th>Year Level</th>';
            foreach ($this->school_work_types as $swt) {
                if ($swt->weight > 0 && $swt->id != $this->current_school_work_type->id)
                    echo '<th>' . e($swt->school_work_type) . ' (%)</th>';
            }
            echo '<th>Total</th>';
            echo '<th>Laboratory (Weighted ' . number_format($lab_pct, 2) . '%)</th>';
            echo '<th>Weighted Grade</th><th>Remarks</th></tr></thead><tbody>';

            $i = 1;
            foreach ($students as $student) {
                $row = $this->calcStudentRow($student, $this->school_work_types, $weight, 0, 0, $lab_pct);
                echo '<tr>';
                echo '<td>' . $i++ . '</td>';
                echo '<td>' . e($student->code) . '</td>';
                echo '<td>' . e($student->fullname) . '</td>';
                echo '<td>' . e($student->college   ?? 'N/A') . '</td>';
                echo '<td>' . e($student->department ?? 'N/A') . '</td>';
                echo '<td>' . e($student->year_level ?? 'N/A') . '</td>';

                foreach ($row['swt_cols'] as $col)
                    echo '<td class="text-center">' . e($col['value']) . '</td>';

                echo '<td class="text-center">' . ($row['total_grade_pct'] > 0 ? number_format($row['total_grade_pct'], 2, '.', '') : '') . '</td>';
                echo '<td class="text-center">' . ($row['lab_value_weighted'] !== null ? number_format($row['lab_value_weighted'], 2, '.', '') : '0.00') . '</td>';
                echo '<td class="text-center">' . e($row['is_incomplete'] ? '' : $row['weighted_grade']) . '</td>'; // blank for INC/DROP
                echo '<td class="text-center" style="' . $this->remarkStyle($row['remark']) . '">' . e($row['remark']) . '</td>';
                echo '</tr>';
            }

            echo '</tbody></table>';
            echo '<br><p><em>Generated on: ' . now()->timezone('Asia/Manila')->format('F d, Y h:i A') . '</em></p>';
            echo '</body></html>';
        }, 200, [
            'Content-Type'        => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────────
    // Small private helpers to reduce repetition
    // ─────────────────────────────────────────────────────────────────────────────

    /** Fetch & filter students for export */
    private function getExportStudents()
    {
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
            ->where('es.schedule_id', $this->detail['schedule_id'])
            ->orderBy('s.is_active', 'desc')
            ->orderBy('s.id', 'desc')
            ->get();

        if (!empty($this->filters['search'])) {
            $search   = $this->filters['search'];
            $students = $students->filter(fn($s) =>
                stripos($s->code, $search) !== false || stripos($s->fullname, $search) !== false
            );
        }

        if (!empty($this->filters['remarks'])) {
            $ids      = DB::table('term_grades')
                ->where('schedule_id', $this->detail['schedule_id'])
                ->where('term_id', $this->detail['term_id'])
                ->where('remarks', $this->filters['remarks'])
                ->pluck('student_id')->toArray();
            $students = $students->whereIn('id', $ids);
        }

        return $students;
    }

    /** Lecture weight % and laboratory weight % for the current term */
    private function getLabLecPcts(): array
    {
        $ll  = DB::table('lab_lec')
            ->where('schedule_id', $this->detail['schedule_id'])
            ->where('term_id', $this->detail['term_id'])
            ->first();
        $lec = $ll ? floatval($ll->sub_weight) : 80.0;
        return [$lec, 100 - $lec];
    }

    /** Lab-only weight % (for is_lec == 0 schedules) */
    private function getLabOnlyPct(): float
    {
        $ll = DB::table('lab_lec')
            ->where('schedule_id', $this->detail['schedule_id'])
            ->where('term_id', $this->detail['term_id'])
            ->first();
        return $ll ? floatval($ll->sub_weight) : 100.0;
    }

    /** Total weight of school work types for current term */
    private function getTotalWeight()
    {
        return DB::table('school_works_types')
            ->select(DB::raw('sum(weight) as total_weight'))
            ->where('schedule_id', $this->detail['schedule_id'])
            ->where('term_id', $this->detail['term_id'])
            ->first();
    }

    /** Excel HTML boilerplate head */
    private function excelHead(string $sheetName): string
    {
        return <<<HTML
    <html xmlns:o="urn:schemas-microsoft-com:office:office"
        xmlns:x="urn:schemas-microsoft-com:office:excel"
        xmlns="http://www.w3.org/TR/REC-html40">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>
    <x:Name>{$sheetName}</x:Name>
    <x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions>
    </x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->
    <style>
    table{border-collapse:collapse;width:100%}
    th,td{border:1px solid black;padding:8px;text-align:left}
    th{background-color:#952323;color:white;font-weight:bold}
    .text-center{text-align:center}
    </style>
    </head><body>
    HTML;
    }

    /** Inline style for remark cell */
    private function remarkStyle(string $remark): string
    {
        $bg = match($remark) {
            'PASSED' => '#198754',
            'FAILED' => '#dc3545',
            'INC'    => '#ffc107',
            'DROP'   => '#6c757d',
            default  => '#f8f9fa',
        };
        $fg = match($remark) {
            'INC', 'N/A' => '#000000',
            default      => '#ffffff',
        };
        return "background-color:{$bg};color:{$fg};font-weight:bold;";
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