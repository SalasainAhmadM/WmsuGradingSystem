<div>
    <div class="container-fluid d-flex justify-content-center shadow">
        <span class="fs-2 fw-bold h1 m-0 brand-color">  {{ $title }}s</span>
    </div>
    <div class="container-fluid">
        <div class="table-header">
            <livewire:admin.BreadCrumb.BreadCrumb/>
        </div>
        <div class="d-flex justify-content-between my-2 gap-2 row">
            <div class="col-4">
                <input type="search" wire:model.live="filters.search" name="" id="" placeholder="Search ... " class="form-control">
            </div>
            <div class="d-flex col-7 justify-content-end gap-2">
            </div>
        </div>
        <div class="row ">
            <div class="table-responsive">
                <table class="table table-striped table-bordered text-center align-middle position-relative" >
                    <thead style="background:#952323;color:white;">
                        <tr class="">
                            <th scope="col" class="px-4">#</th>
                            <th scope="col" class="px-4 text-start">Subject</th>
                            <th scope="col" class="px-4 ">School Year</th>
                            <th scope="col" class="px-4 ">Semester</th>
                            <th scope="col" class="text-center px-4 ">Components</th> 
                            <th scope="col" class="text-center px-4 ">Grade</th> 
                            <th scope="col" class="text-center px-4 ">Grade Equivalent</th> 
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($table_data as $key =>$value)
                            <tr class="">
                                <th scope="row" class="px-4">{{ intval($key)+1 }}</th>
                                <td class="px-4 text-start">
                                        {{$value->subject_id}} - {{$value->subject_code}}
                                </td>
                                <th scope="row" class="px-4">{{ $value->school_year}}</th>
                                <th scope="row" class="px-4">{{ $value->semester}}</th>
                                <th scope="row" class="px-4">
                                    <a href="/student/my-grades/view-{{ $student_id }}/schedule-{{ $value->schedule_id }}/" class="btn btn-outline-secondary" target="_blank">
                                        <span>
                                            <svg viewBox="0 0 1024 1024" width="20px" class="icon" version="1.1" xmlns="http://www.w3.org/2000/svg" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M981.333333 960h-21.333333V576a21.333333 21.333333 0 0 0-42.666667 0v384h-128V213.333333a42.666667 42.666667 0 0 1 42.666667-42.666666h42.666667a42.666667 42.666667 0 0 1 42.666666 42.666666v21.333334a21.333333 21.333333 0 0 0 42.666667 0v-21.333334a85.333333 85.333333 0 0 0-85.333333-85.333333h-42.666667a85.333333 85.333333 0 0 0-85.333333 85.333333v746.666667h-85.333334V426.666667a85.333333 85.333333 0 0 0-85.333333-85.333334h-42.666667a85.333333 85.333333 0 0 0-85.333333 85.333334v533.333333h-85.333333V640a85.333333 85.333333 0 0 0-85.333334-85.333333h-42.666666a85.333333 85.333333 0 0 0-85.333334 85.333333v320H64V42.666667a21.333333 21.333333 0 0 0-42.666667 0v938.666666a21.333333 21.333333 0 0 0 21.333334 21.333334h938.666666a21.333333 21.333333 0 0 0 0-42.666667z m-661.333333 0H192V640a42.666667 42.666667 0 0 1 42.666667-42.666667h42.666666a42.666667 42.666667 0 0 1 42.666667 42.666667z m298.666667 0h-128V426.666667a42.666667 42.666667 0 0 1 42.666666-42.666667h42.666667a42.666667 42.666667 0 0 1 42.666667 42.666667z" fill="currentColor"></path><path d="M938.666667 384a21.333333 21.333333 0 0 0-21.333334 21.333333v85.333334a21.333333 21.333333 0 0 0 42.666667 0v-85.333334a21.333333 21.333333 0 0 0-21.333333-21.333333zM958.293333 311.893333a24.533333 24.533333 0 0 0-4.48-7.04l-3.2-2.56a16.213333 16.213333 0 0 0-3.84-1.92L942.933333 298.666667a21.333333 21.333333 0 0 0-12.373333 1.28 19.2 19.2 0 0 0-11.52 11.52 21.333333 21.333333 0 0 0-1.706667 8.533333 21.333333 21.333333 0 0 0 6.186667 15.146667 21.333333 21.333333 0 0 0 7.04 4.48A21.333333 21.333333 0 0 0 938.666667 341.333333a21.333333 21.333333 0 0 0 15.146666-6.186666A22.4 22.4 0 0 0 960 320a21.333333 21.333333 0 0 0-1.706667-8.106667z" fill="currentColor"></path></g></svg>
                                        </span>
                                    </a>
                                </th>
                                <td class="px-4">
                                    @php
                                        $lab = floatval($value->lab_calculated_grade);
                                        $lec = floatval($value->lec_calculated_grade);
                                        $weight = 0;

                                        if ($lab > 0) $weight += 1;
                                        if ($lec > 0) $weight += 1;

                                        $grade = $weight > 0
                                            ? (($lab * 0.5) + ($lec * 0.5)) 
                                            : NULL;
                                    @endphp
                                    @if($grade)
                                    {{ number_format($grade, 2, '.', '') }}
                                    @endif
                                </td>
                                <td class="px-4">
                                    @if($grade)
                                        @php
                                            $set = false;
                                        @endphp
                                        @foreach ($equivalent_grade as $eg_key =>$eg_value)
                                            @if(floatval($grade) >= floatval($eg_value->minimum) && floatval($grade) < floatval($eg_value->maximum + 1))
                                                {{ $eg_value->grade }}
                                                @php
                                                    $set = true;
                                                @endphp
                                            @endif
                                        @endforeach
                                        @if(!$set)
                                            No grade equivalent
                                        @endif
                                    @else 
                                        
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr class="align-middle">
                                <td colspan="42">
                                    <div class="alert alert-danger d-flex justify-content-center">No records found!</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row d-flex justify-content-end">
            {{ $table_data->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
