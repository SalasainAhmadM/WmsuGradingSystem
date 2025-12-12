<div>
    <div class="container-fluid d-flex justify-content-center shadow">
        <span class="fs-2 fw-bold h1 m-0 brand-color">  {{ $title }}s</span>
    </div>
    <div class="container-fluid">
        <div class="table-header">
            <livewire:admin.BreadCrumb.BreadCrumb/>
        </div>
        <div class="d-flex justify-content-between my-2 gap-2 row">
            <div class="d-flex col-7 justify-content-end gap-2">
            </div>
        </div>
        <div class="row ">
            <div class="mx-2">  
                <label for="student" class="fs-3 fw-bold">Information</label>
                <ul class="list-group mb-3" id="student">
                    <li class="list-group-item"><strong>Student ID: </strong>{{ $student->code }}</li>
                    <li class="list-group-item"><strong>Name: </strong>{{ $student->fullname }}</li>
                    <li class="list-group-item"><strong>Email: </strong>{{ $student->email }}</li>
                </ul>

                <label for="student" class="fs-3 fw-bold">Attendance</label>
                <ul class="list-group mb-3" id="student">
                    @php
                        $attendance_present = 0;
                        $attendance_total = 0;
                    @endphp 
                    @foreach ($terms as $t_key =>$t_value )
                        @foreach($student_scores[$t_value->id][0] as $key =>$value)
                            @if($value['school_work_type'] =='Attendance')
                                    @php
                                        if(isset($value['score'])){
                                            $attendance_total++;
                                            $attendance_present+=$value['score'];
                                        }
                                    @endphp
                            @endif
                        @endforeach
                    @endforeach
                    <li class="list-group-item"><strong>Total Attendance: </strong>{{ $attendance_total }}</li>
                    <li class="list-group-item"><strong>Total Present: </strong>{{ $attendance_present }}</li>
                    <li class="list-group-item"><strong>Total Absent: </strong>{{ $attendance_total-$attendance_present }}</li>
                </ul>
                @php
                    $total_grade = 0;
                @endphp
                <label for="scores" class="fs-3 fw-bold">Scores </label>
                <ul class="list-group" id="scores"></ul>
                @foreach ($terms as $t_key =>$t_value )
                    <li class="list-group-item">
                        <label for="term-{{$t_value->term_name }}" class="fs-2">{{ $t_value->term_name }}</label>
                        <ul class="list-group mb-3" id="term-{{$t_value->term_name }}">
                            @foreach ($school_work_types as $swt_key => $swt_value )
                                @if($swt_value->term_id == $t_value->id && $swt_value->school_work_type != "Attendance")
                                <li class="list-group-item">
                                    <label for="school-work-{{$swt_value->school_work_type }}" class="fs-3">{{ $swt_value->school_work_type }}</label>
                                    <ul class="list-group mb-3" id="school-work-{{$swt_value->school_work_type }}">
                                        @foreach($student_scores[$t_value->id][0] as $key =>$value)
                                            @if($value['school_work_type'] != "Attendance" && $swt_value->id == $value['school_work_type_id'])
                                                <li class="list-group-item"><strong>{{ $value['school_work_name'] }} taken on {{ $value['schedule_date'] }} : </strong>
                                                    @if($value['score'] != NULL)
                                                        {{ $value['score'] }} / {{ $value['max_score'] }}</li>
                                                    @else   
                                                        No Data
                                                    @endif
                                            @endif
                                        @endforeach
                                    </ul>
                                </li>
                                @endif
                            @endforeach
                            @php
                            $term_grade = DB::table('term_grades')
                                ->where('term_id','=',$t_value->id)
                                ->where('schedule_id','=',$schedule_id)
                                ->where('student_id','=',$student_id)
                                ->first();
                            @endphp
                            <label for="" class="fs-3">{{ $t_value->term_name }} Grade :
                                @if($term_grade->grade)
                                    {{ number_format($term_grade->grade*100, 2, '.', '') }}
                                    @php
                                        if($total_grade != 'INC'){
                                            $total_grade += ($term_grade->grade) ;
                                        }
                                    @endphp
                                @else
                                    {{ $term_grade->other }} 
                                    @php
                                        $total_grade = 'INC';
                                    @endphp
                                @endif
                            </label>
                        </ul>
                    </li>
                @endforeach
                <label for="" class="fs-1 fw-bold">Total Grade : 
                    @if(floatval($total_grade))
                        {{ number_format($total_grade*100, 2, '.', '') }}
                    @else
                        @php
                        $other = DB::table('lab_lec_grades')
                            ->where('student_id','=',$student_id)
                            ->where('schedule_id','=',$schedule_id)
                            ->first();
                        @endphp
                        @if($other)
                        {{ $other->other }}
                        @endif
                    @endif    
                </label>
            </div>
        </div>
    </div>
</div>
