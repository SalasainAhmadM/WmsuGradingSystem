<div>
    <div class="container-fluid position-relative shadow" style="min-height: 110px;">
        <!-- Centered Title -->
        <div class="d-flex justify-content-center align-items-center" style="height: 100%;">
            <span class="fs-2 fw-bold h1 m-0 brand-color">{{ $title }}s</span>
        </div>

        <!-- Top Right Filters -->
        <div class="position-absolute top-0 end-0 p-2 d-flex flex-column gap-2">
            <button class="btn btn-info" wire:click="viewDetails('detailModal')">
                View Details
            </button>
            <button class="btn btn-info" wire:click="viewAttendance('attendanceModal')">
                Attendance
            </button>
        </div>
    </div>
    <style>
    /* Remarks badge styling (non-dropdown version) */
    .badge-remarks {
        padding: 0.35rem 0.65rem;
        font-weight: 500;
        border-radius: 0.25rem;
        display: inline-block;
        min-width: 80px;
        text-align: center;
    }
    
    .badge-remarks.bg-success {
        background-color: #198754 !important;
        color: white !important;
    }
    
    .badge-remarks.bg-danger {
        background-color: #dc3545 !important;
        color: white !important;
    }
    
    .badge-remarks.bg-warning {
        background-color: #ffc107 !important;
        color: #000 !important;
    }
    
    .badge-remarks.bg-secondary {
        background-color: #6c757d !important;
        color: white !important;
    }
    
    .badge-remarks.bg-light {
        background-color: #f8f9fa !important;
        color: #000 !important;
        border: 1px solid #dee2e6 !important;
    }
</style>
    <div class="container-fluid">
        <div class="table-header">
            <livewire:admin.BreadCrumb.BreadCrumb/>
        </div>
        <div class="d-flex justify-content-between my-2 gap-2 row">
            <div class="col-3">
                <input type="search" wire:model.live="filters.search" name="" id="" placeholder="Search ... " class="form-control">
            </div>
            <div class="col-2 d-flex justify-items-start gap-1">
                <label for="" class="mt-2">Remarks</label>
                <select name="" id="" class="form-control" wire:model.live="filters.remarks">
                    <option value="">All</option>
                    <option value="PASSED">PASSED</option>
                    <option value="FAILED">FAILED</option>
                    <option value="INC">INC</option>
                    <option value="DROP">DROP</option>
                </select>
            </div>
            <div class="d-flex col justify-content-end gap-2">
                @if ($schedule->is_lec && $schedule->laboratory_unit >0)
                    <button class="btn btn-outline-secondary" wire:click="open_lablect_weight('lablecweightModal')">
                        <svg viewBox="0 0 24 24" height="20px" width="20px" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M3.99923 21H19.9992M11.9992 21V7M11.9992 7C13.1038 7 13.9992 6.10457 13.9992 5M11.9992 7C10.8947 7 9.99923 6.10457 9.99923 5M13.9992 5C13.9992 3.89543 13.1038 3 11.9992 3C10.8947 3 9.99923 3.89543 9.99923 5M13.9992 5H19.9992M9.99923 5H3.99923M5.99923 17C7.51177 17 8.76287 16.1584 8.96934 14.7513C8.98242 14.6621 8.98897 14.6175 8.98385 14.5186C8.98031 14.4503 8.95717 14.3256 8.93599 14.2605C8.90531 14.1664 8.86812 14.1003 8.79375 13.968L5.99923 9L3.2047 13.968C3.13575 14.0906 3.10128 14.1519 3.06939 14.2584C3.04977 14.3239 3.02706 14.4811 3.02735 14.5494C3.02781 14.6606 3.03453 14.6899 3.04799 14.7486C3.30295 15.86 4.5273 17 5.99923 17ZM17.9992 17C19.5118 17 20.7629 16.1584 20.9693 14.7513C20.9824 14.6621 20.989 14.6175 20.9838 14.5186C20.9803 14.4503 20.9572 14.3256 20.936 14.2605C20.9053 14.1664 20.8681 14.1003 20.7937 13.968L17.9992 9L15.2047 13.968C15.1358 14.0906 15.1013 14.1519 15.0694 14.2584C15.0498 14.3239 15.0271 14.4811 15.0273 14.5494C15.0278 14.6606 15.0345 14.6899 15.048 14.7486C15.303 15.86 16.5273 17 17.9992 17Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                    </button>
                @endif
                <button class="btn btn-outline-primary" wire:click="open_term_weight('weightModal')">
                    <svg viewBox="0 0 24 24" height="20px" width="20px" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M3.99923 21H19.9992M11.9992 21V7M11.9992 7C13.1038 7 13.9992 6.10457 13.9992 5M11.9992 7C10.8947 7 9.99923 6.10457 9.99923 5M13.9992 5C13.9992 3.89543 13.1038 3 11.9992 3C10.8947 3 9.99923 3.89543 9.99923 5M13.9992 5H19.9992M9.99923 5H3.99923M5.99923 17C7.51177 17 8.76287 16.1584 8.96934 14.7513C8.98242 14.6621 8.98897 14.6175 8.98385 14.5186C8.98031 14.4503 8.95717 14.3256 8.93599 14.2605C8.90531 14.1664 8.86812 14.1003 8.79375 13.968L5.99923 9L3.2047 13.968C3.13575 14.0906 3.10128 14.1519 3.06939 14.2584C3.04977 14.3239 3.02706 14.4811 3.02735 14.5494C3.02781 14.6606 3.03453 14.6899 3.04799 14.7486C3.30295 15.86 4.5273 17 5.99923 17ZM17.9992 17C19.5118 17 20.7629 16.1584 20.9693 14.7513C20.9824 14.6621 20.989 14.6175 20.9838 14.5186C20.9803 14.4503 20.9572 14.3256 20.936 14.2605C20.9053 14.1664 20.8681 14.1003 20.7937 13.968L17.9992 9L15.2047 13.968C15.1358 14.0906 15.1013 14.1519 15.0694 14.2584C15.0498 14.3239 15.0271 14.4811 15.0273 14.5494C15.0278 14.6606 15.0345 14.6899 15.048 14.7486C15.303 15.86 16.5273 17 17.9992 17Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                </button>
                <a href="{{ route('enrolled-student-lists',[
                        'school_year' => $school_year,
                        'semester' => $semester,
                        'schedule_id' => $detail['schedule_id']]) }}" class="btn btn-outline-secondary d-flex justify-content-center items-center" wire:wire:navigate>
                    <svg fill="currentColor" viewBox="0 -64 640 640"  width="20px"  xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M96 224c35.3 0 64-28.7 64-64s-28.7-64-64-64-64 28.7-64 64 28.7 64 64 64zm448 0c35.3 0 64-28.7 64-64s-28.7-64-64-64-64 28.7-64 64 28.7 64 64 64zm32 32h-64c-17.6 0-33.5 7.1-45.1 18.6 40.3 22.1 68.9 62 75.1 109.4h66c17.7 0 32-14.3 32-32v-32c0-35.3-28.7-64-64-64zm-256 0c61.9 0 112-50.1 112-112S381.9 32 320 32 208 82.1 208 144s50.1 112 112 112zm76.8 32h-8.3c-20.8 10-43.9 16-68.5 16s-47.6-6-68.5-16h-8.3C179.6 288 128 339.6 128 403.2V432c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48v-28.8c0-63.6-51.6-115.2-115.2-115.2zm-223.7-13.4C161.5 263.1 145.6 256 128 256H64c-35.3 0-64 28.7-64 64v32c0 17.7 14.3 32 32 32h65.9c6.3-47.4 34.9-87.3 75.2-109.4z"></path></g></svg>
                </a>
            </div>
        </div>
        <div class="row " style="min-width:800px;">
            <div class="table-responsive">
                <table class="table table-striped table-bordered text-center align-middle " style="overflow-x: auto;" >
                    <div wire:target="filters.search perPage, nextPage, previousPage, gotoPage"
                            wire:loading.flex>
                            <div class="form-loader">
                                Loading...
                                <div class="sk-wave sk-primary mt-4">
                                    <div class="sk-wave-rect"></div>
                                    <div class="sk-wave-rect"></div>
                                    <div class="sk-wave-rect"></div>
                                    <div class="sk-wave-rect"></div>
                                    <div class="sk-wave-rect"></div>
                                </div>
                            </div>
        
                        </div>
                    <thead style="background:#952323;color:white;">
                        <tr class="align-middle">
                            <th class="sticky-col left-0"></th>
                            <th class="sticky-col left-1"></th>
                            @php
                                $weight = DB::table('school_works_types')
                                    ->select(DB::raw('sum(weight) as total_weight'))
                                    ->where('schedule_id','=',$this->detail['schedule_id'])
                                    ->where('term_id','=',$this->detail['term_id'])
                                    ->first();
                            @endphp
                            <th colspan="5">
                                Final Grade
                            </th>
                        </tr>
                        <tr class="align-middle">
                            <th scope="col" class="sticky-col left-0">#</th>
                            <th scope="col" class="sticky-col left-1">Student</th>
                            @forelse ($school_work_types as $key => $value )
                                @php
                                    $school_works_var = DB::table('school_works')
                                        ->where('school_work_type_id','=',$value->id)
                                        ->get()
                                        ->toArray();
                                @endphp
                            @empty
                            @endforelse
                            @php
                                $term_total = DB::table('terms')
                                    ->select(DB::raw('sum(weight) as total'))
                                    ->where('schedule_id','=',$detail['schedule_id'])
                                    ->first();
                            @endphp
                            @foreach ($terms as $key =>$value )
                                @if($value->id != $detail['term_id'])
                                @php 
                                    $other_term_weight = $value->weight;
                                @endphp 
                                @endif
                            @endforeach
                            @if($schedule->is_lec)
                            <th scope="col" class="">Lecture</th>
                            @endif
                            @if($schedule->laboratory_unit>0)
                                <th scope="col" class="">Laboratory</th>
                            @endif
                                <th scope="col" class="">Total</th>
                                <th scope="col" class="">Weighted Grade</th>
                                <th scope="col" class="">Remarks</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($table_data as $key => $value)
                            <tr class="align-middle">
                                <th scope="row" class="px-4">
                                    {{ ($table_data->currentPage()-1) * $table_data->perPage() + $key + 1 }}
                                </th>
                                <td class="text-start">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="/faculty/students/view-{{ $value->id }}" target="_blank">
                                            <span>{{ $value->code.' - '.$value->fullname }}</span>
                                        </a>
                                        <a href="/faculty/students/view-{{ $value->id }}/schedule-{{ $detail['schedule_id'] }}/" 
                                        class="btn btn-outline-secondary" target="_blank">
                                            <span>
                                                <svg viewBox="0 0 1024 1024" width="20px" class="icon" version="1.1" xmlns="http://www.w3.org/2000/svg" fill="#000000">
                                                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                                                    <g id="SVGRepo_iconCarrier">
                                                        <path d="M981.333333 960h-21.333333V576a21.333333 21.333333 0 0 0-42.666667 0v384h-128V213.333333a42.666667 42.666667 0 0 1 42.666667-42.666666h42.666667a42.666667 42.666667 0 0 1 42.666666 42.666666v21.333334a21.333333 21.333333 0 0 0 42.666667 0v-21.333334a85.333333 85.333333 0 0 0-85.333333-85.333333h-42.666667a85.333333 85.333333 0 0 0-85.333333 85.333333v746.666667h-85.333334V426.666667a85.333333 85.333333 0 0 0-85.333333-85.333334h-42.666667a85.333333 85.333333 0 0 0-85.333333 85.333334v533.333333h-85.333333V640a85.333333 85.333333 0 0 0-85.333334-85.333333h-42.666666a85.333333 85.333333 0 0 0-85.333334 85.333333v320H64V42.666667a21.333333 21.333333 0 0 0-42.666667 0v938.666666a21.333333 21.333333 0 0 0 21.333334 21.333334h938.666666a21.333333 21.333333 0 0 0 0-42.666667z m-661.333333 0H192V640a42.666667 42.666667 0 0 1 42.666667-42.666667h42.666666a42.666667 42.666667 0 0 1 42.666667 42.666667z m298.666667 0h-128V426.666667a42.666667 42.666667 0 0 1 42.666666-42.666667h42.666667a42.666667 42.666667 0 0 1 42.666667 42.666667z" fill="currentColor"></path>
                                                        <path d="M938.666667 384a21.333333 21.333333 0 0 0-21.333334 21.333333v85.333334a21.333333 21.333333 0 0 0 42.666667 0v-85.333334a21.333333 21.333333 0 0 0-21.333333-21.333333zM958.293333 311.893333a24.533333 24.533333 0 0 0-4.48-7.04l-3.2-2.56a16.213333 16.213333 0 0 0-3.84-1.92L942.933333 298.666667a21.333333 21.333333 0 0 0-12.373333 1.28 19.2 19.2 0 0 0-11.52 11.52 21.333333 21.333333 0 0 0-1.706667 8.533333 21.333333 21.333333 0 0 0 6.186667 15.146667 21.333333 21.333333 0 0 0 7.04 4.48A21.333333 21.333333 0 0 0 938.666667 341.333333a21.333333 21.333333 0 0 0 15.146666-6.186666A22.4 22.4 0 0 0 960 320a21.333333 21.333333 0 0 0-1.706667-8.106667z" fill="currentColor"></path>
                                                    </g>
                                                </svg>
                                            </span>
                                        </a>
                                    </div>
                                </td>
                                
                                {{-- Get final grades data from the final_grades array --}}
                                @php
                                    $student_final_grade = isset($final_grades[$value->id]) ? $final_grades[$value->id] : null;
                                @endphp
                                
                                {{-- Lecture Grade --}}
                                @if($schedule->is_lec)
                                    <td class="text-center">
                                        @if($student_final_grade && $student_final_grade['lecture_grade'] !== null)
                                            {{ number_format($student_final_grade['lecture_grade'], 2, '.', '') }}
                                        @else
                                            <span class="text-muted">--</span>
                                        @endif
                                    </td>
                                @endif
                                
                                {{-- Laboratory Grade --}}
                                @if($schedule->laboratory_unit > 0 || $schedule->is_lec == 0)
                                    <td class="text-center">
                                        @if($student_final_grade && $student_final_grade['laboratory_grade'] !== null)
                                            {{ number_format($student_final_grade['laboratory_grade'], 2, '.', '') }}
                                        @else
                                            <span class="text-muted">--</span>
                                        @endif
                                    </td>
                                @endif
                                
                                {{-- Total Grade --}}
                                <td class="text-center">
                                    @if($student_final_grade && $student_final_grade['total_grade'] !== null)
                                        {{ number_format($student_final_grade['total_grade'], 2, '.', '') }}
                                    @else
                                        <span class="text-muted">--</span>
                                    @endif
                                </td>
                                
                                {{-- Weighted Grade --}}
                                <td class="text-center">
                                    @if($student_final_grade && $student_final_grade['weighted_grade'] !== null)
                                        {{ number_format($student_final_grade['weighted_grade'], 2, '.', '') }}
                                    @else
                                        <span class="text-muted">--</span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    @if($student_final_grade && $student_final_grade['remarks'])
                                        @php
                                            $badge_class = match($student_final_grade['remarks']) {
                                                'PASSED' => 'bg-success',
                                                'FAILED' => 'bg-danger',
                                                'INC' => 'bg-warning',
                                                'DROP' => 'bg-secondary',
                                                default => 'bg-light'
                                            };
                                        @endphp
                                        <span class="badge-remarks {{ $badge_class }}">
                                            {{ $student_final_grade['remarks'] }}
                                        </span>
                                    @else
                                        <span class="badge-remarks bg-light">N/A</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr class="align-middle">
                                <td colspan="20">
                                    <div class="alert alert-danger d-flex justify-content-center">
                                        No records found!
                                    </div>
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
        
        <div class="modal fade" id="addSchoolWorkTypeModal" wire:ignore.self data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="addSchoolWorkTypeModalTitle">Add School Work Types</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" id="addSchoolWorkTypeModalclose" aria-label="Close"></button>
                    </div>
                    <div class="modal-body row">
                        <div class="col-md-6 mb-3">
                            <label for="school_work_type" class="form-label">School Work Type</label>
                            <input type="text" id="school_work_type" wire:model.defer="school_work_type.school_work_type" placeholder="School work type" class="form-control @error('school_work_type.school_work_type') is-invalid @enderror">
                            @error('school_work_type.school_work_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-5 mb-3">
                            <label for="weight" class="form-label">School Work Weight</label>
                            <input type="weight" min="0.0" step="0.1" id="weight" wire:model.defer="school_work_type.weight" class="form-control @error('school_work_type.weight') is-invalid @enderror">
                            @error('school_work_type.weight')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col align-bottom">
                            <label for="weight" class="form-label text-white" >asd</label>
                            <button class="btn btn-primary " wire:click="add_school_work_type({{ $detail['schedule_id'] }},'addSchoolWorkTypeModal')">
                                Add
                            </button>
                        </div>
                        <div class="col-12">
                            <table class="table table-striped table-bordered text-center align-middle position-relative" >
                                <thead style="background:#952323;color:white;">
                                    <tr class="align-middle">
                                        <th scope="col" class="px-4">#</th>
                                        <th scope="col" class="px-4 ">School Work Type</th>
                                        <th scope="col" class="text-center px-4 ">Weight</th> 
                                        <th scope="col" class="text-center px-4 ">Percent</th> 
                                        <th scope="col" class="text-center px-4 ">Action</th> 
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $total_weight = 0;
                                    @endphp
                                    @forelse($school_work_types as $key =>$value)
                                        <tr class="align-middle">
                                            <th scope="row" class="px-4">{{ intval($key)+1 }}</th>
                                            <td class="px-4">
                                                {{ $value->school_work_type }}
                                            </td>
                                            <td class="px-4">
                                                <input type="number" name="" id="" value="{{ $value->weight }}" wire:model="school_work_type_value.{{ $key }}.val" 
                                                    wire:change="updateSchoolWorktype('{{$value->id }}', $event.target.value)" class="form-control" placeholder="weight" min="0.0" step="0.1" >
                                            </td>
                                            <td class="px-4">
                                                @if($weight->total_weight)
                                                    {{number_format($value->weight / $weight->total_weight *100 , 2, '.', '') }}%
                                                @endif
                                            </td>
                                            @if($value->school_work_type != 'Attendance')
                                                <td class="d-flex justify-content-center text-center">
                                                    <button wire:click="deleteSchoolWorkType({{$value->id }})" type="button" wire:wire:navigate  class="btn btn-outline-danger d-flex justify-content-center items-center">
                                                        <svg fill="currentColor" width="20px"  viewBox="0 0 64 64" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <rect id="Icons" x="-64" y="-64" width="1280" height="800" style="fill:none;"></rect> <g id="Icons1" serif:id="Icons"> <g id="Strike"> </g> <g id="H1"> </g> <g id="H2"> </g> <g id="H3"> </g> <g id="list-ul"> </g> <g id="hamburger-1"> </g> <g id="hamburger-2"> </g> <g id="list-ol"> </g> <g id="list-task"> </g> <g id="trash"> <path d="M19.186,16.493l0,-1.992c0.043,-3.346 2.865,-6.296 6.277,-6.427c3.072,-0.04 10.144,-0.04 13.216,0c3.346,0.129 6.233,3.012 6.277,6.427l0,1.992l9.106,0l0,4l-4.442,0l0,29.11c-0.043,3.348 -2.865,6.296 -6.278,6.428c-7.462,0.095 -14.926,0.002 -22.39,0.002c-3.396,-0.044 -6.385,-2.96 -6.429,-6.43l0,-29.11l-4.443,0l0,-4l9.106,0Zm26.434,4l-27.099,0c-0.014,9.72 -0.122,19.441 0.002,29.16c0.049,1.25 1.125,2.33 2.379,2.379c7.446,0.095 14.893,0.095 22.338,0c1.273,-0.049 2.363,-1.163 2.38,-2.455l0,-29.084Zm-4.701,-4c-0.014,-0.83 0,-1.973 0,-1.973c0,0 -0.059,-2.418 -2.343,-2.447c-3.003,-0.039 -10.007,-0.039 -13.01,0c-1.273,0.049 -2.363,1.162 -2.38,2.454l0,1.966l17.733,0Z" style="fill-rule:nonzero;"></path> <rect x="22.58" y="28.099" width="3" height="16.327" style="fill-rule:nonzero;"></rect> <rect x="30.571" y="28.099" width="3" height="16.327" style="fill-rule:nonzero;"></rect> <rect x="38.58" y="28.099" width="3" height="16.327" style="fill-rule:nonzero;"></rect> </g> <g id="vertical-menu"> </g> <g id="horizontal-menu"> </g> <g id="sidebar-2"> </g> <g id="Pen"> </g> <g id="Pen1" serif:id="Pen"> </g> <g id="clock"> </g> <g id="external-link"> </g> <g id="hr"> </g> <g id="info"> </g> <g id="warning"> </g> <g id="plus-circle"> </g> <g id="minus-circle"> </g> <g id="vue"> </g> <g id="cog"> </g> <g id="logo"> </g> <g id="radio-check"> </g> <g id="eye-slash"> </g> <g id="eye"> </g> <g id="toggle-off"> </g> <g id="shredder"> </g> <g id="spinner--loading--dots-" serif:id="spinner [loading, dots]"> </g> <g id="react"> </g> <g id="check-selected"> </g> <g id="turn-off"> </g> <g id="code-block"> </g> <g id="user"> </g> <g id="coffee-bean"> </g> <g id="coffee-beans"> <g id="coffee-bean1" serif:id="coffee-bean"> </g> </g> <g id="coffee-bean-filled"> </g> <g id="coffee-beans-filled"> <g id="coffee-bean2" serif:id="coffee-bean"> </g> </g> <g id="clipboard"> </g> <g id="clipboard-paste"> </g> <g id="clipboard-copy"> </g> <g id="Layer1"> </g> </g> </g></svg>
                                                    </button>
                                                </td>
                                            @endif
                                        </tr>
                                        @php
                                            $total_weight +=$value->weight;
                                        @endphp
                                    @empty
                                        <tr class="align-middle">
                                            <td colspan="42">
                                                <div class="alert alert-danger d-flex justify-content-center">No records found!</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                    <tr class="align-middle">
                                        <td colspan="2" class="text-end">Total :</td>
                                        <td colspan="">
                                            <div class="">{{ $total_weight }}</div>
                                        </td>
                                        <td colspan="">
                                            <div class="">100%</div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="addSchoolWorkModal" wire:ignore.self data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <form wire:submit.prevent="add_school_work('addSchoolWorkModal')" class="w-100">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="addSchoolWorkModalTitle">Add School Work</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" id="addSchoolWorkModalclose" aria-label="Close"></button>
                        </div>
                        <div class="modal-body row">
                            <div class="col-6 mb-3">
                                <label for="school_work_type_id" class="form-label">School work type</label>
                                <select name="school_work_type_id" id="school_work_type_id" class="form-control @error('school_work.school_work_type_id') is-invalid @enderror" wire:model.live="school_work.school_work_type_id">
                                    <option value="">Select school work type</option>
                                    @forelse($school_work_types as $key =>$value)
                                        @if($value->school_work_type != 'Attendance')
                                            <option value="{{ $value->id }}">{{ $value->school_work_type }}</option>
                                        @endif
                                    @empty
                                        <option value="">Please add school work type </option>
                                    @endforelse
                                </select>
                                @error('school_work.school_work_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-6 mb-4">
                                <label for="schedule_date" class="form-label">School work date</label>
                                <input type="date" name="schedule_date" id="schedule_date"  wire:model="school_work.schedule_date" class="form-control @error('school_work.schedule_date') is-invalid @enderror">
                                @error('school_work.schedule_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="school_work" class="form-label">School work name</label>
                                <input type="text" id="school_work" wire:model.defer="school_work.school_work_name" placeholder="School work type" class="form-control @error('school_work.school_work_name') is-invalid @enderror">
                                @error('school_work.school_work_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-5 mb-3">
                                <label for="max_score" class="form-label">School work total score</label>
                                <input type="max_score" min="0.0" step="1" id="max_score" wire:model="school_work.max_score" class="form-control @error('school_work.max_score') is-invalid @enderror">
                                @error('school_work.max_score')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col align-bottom">
                                <label for="weight" class="form-label text-white">asd</label>
                                <button class="btn btn-primary">
                                    Add
                                </button>
                            </div>
                            <div class="col-12">
                                <table class="table table-striped table-bordered text-center align-middle position-relative" >
                                    <thead style="background:#952323;color:white;">
                                        <tr class="align-middle">
                                            <th scope="col" class="px-4">#</th>
                                            <th scope="col" class="px-4 ">School work name</th>
                                            <th scope="col" class="text-center px-4 ">Date</th> 
                                            <th scope="col" class="text-center px-4 ">Score</th> 
                                            <th scope="col" class="text-center px-4 ">Action</th> 
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $total_score = 0;
                                        @endphp
                                        @forelse($school_works as $key =>$value)
                                            <tr class="align-middle">
                                                <th scope="row" class="px-4">{{ intval($key)+1 }}</th>
                                                <td class="px-4">
                                                    <input type="text" class="form-control" value={{ $value->school_work_name }}
                                                        wire:change="updateSchoolWorkName({{ $value->id }}, $event.target.value)"
                                                    >
                                                </td>
                                                <td class="px-4">
                                                    <input type="date" class="form-control" name="" value="{{ \Carbon\Carbon::parse($value->schedule_date)->format('Y-m-d')}}" id=""
                                                        wire:change="updateSchoolWorkDate({{ $value->id }}, $event.target.value)">
                                                </td>
                                                <td class="px-4">
                                                     <input type="number" min="1" class="form-control" value={{ $value->max_score }}
                                                        wire:change="updateSchoolWorkName({{ $value->id }}, $event.target.value)"
                                                    >
                                                </td>
                                                <td class="d-flex justify-content-center gap-1 text-center">
                                                    <button wire:click="deleteSchoolWork({{$value->id }})" type="button" wire:wire:navigate  class="btn btn-outline-danger d-flex justify-content-center items-center">
                                                        <svg fill="currentColor" width="20px"  viewBox="0 0 64 64" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <rect id="Icons" x="-64" y="-64" width="1280" height="800" style="fill:none;"></rect> <g id="Icons1" serif:id="Icons"> <g id="Strike"> </g> <g id="H1"> </g> <g id="H2"> </g> <g id="H3"> </g> <g id="list-ul"> </g> <g id="hamburger-1"> </g> <g id="hamburger-2"> </g> <g id="list-ol"> </g> <g id="list-task"> </g> <g id="trash"> <path d="M19.186,16.493l0,-1.992c0.043,-3.346 2.865,-6.296 6.277,-6.427c3.072,-0.04 10.144,-0.04 13.216,0c3.346,0.129 6.233,3.012 6.277,6.427l0,1.992l9.106,0l0,4l-4.442,0l0,29.11c-0.043,3.348 -2.865,6.296 -6.278,6.428c-7.462,0.095 -14.926,0.002 -22.39,0.002c-3.396,-0.044 -6.385,-2.96 -6.429,-6.43l0,-29.11l-4.443,0l0,-4l9.106,0Zm26.434,4l-27.099,0c-0.014,9.72 -0.122,19.441 0.002,29.16c0.049,1.25 1.125,2.33 2.379,2.379c7.446,0.095 14.893,0.095 22.338,0c1.273,-0.049 2.363,-1.163 2.38,-2.455l0,-29.084Zm-4.701,-4c-0.014,-0.83 0,-1.973 0,-1.973c0,0 -0.059,-2.418 -2.343,-2.447c-3.003,-0.039 -10.007,-0.039 -13.01,0c-1.273,0.049 -2.363,1.162 -2.38,2.454l0,1.966l17.733,0Z" style="fill-rule:nonzero;"></path> <rect x="22.58" y="28.099" width="3" height="16.327" style="fill-rule:nonzero;"></rect> <rect x="30.571" y="28.099" width="3" height="16.327" style="fill-rule:nonzero;"></rect> <rect x="38.58" y="28.099" width="3" height="16.327" style="fill-rule:nonzero;"></rect> </g> <g id="vertical-menu"> </g> <g id="horizontal-menu"> </g> <g id="sidebar-2"> </g> <g id="Pen"> </g> <g id="Pen1" serif:id="Pen"> </g> <g id="clock"> </g> <g id="external-link"> </g> <g id="hr"> </g> <g id="info"> </g> <g id="warning"> </g> <g id="plus-circle"> </g> <g id="minus-circle"> </g> <g id="vue"> </g> <g id="cog"> </g> <g id="logo"> </g> <g id="radio-check"> </g> <g id="eye-slash"> </g> <g id="eye"> </g> <g id="toggle-off"> </g> <g id="shredder"> </g> <g id="spinner--loading--dots-" serif:id="spinner [loading, dots]"> </g> <g id="react"> </g> <g id="check-selected"> </g> <g id="turn-off"> </g> <g id="code-block"> </g> <g id="user"> </g> <g id="coffee-bean"> </g> <g id="coffee-beans"> <g id="coffee-bean1" serif:id="coffee-bean"> </g> </g> <g id="coffee-bean-filled"> </g> <g id="coffee-beans-filled"> <g id="coffee-bean2" serif:id="coffee-bean"> </g> </g> <g id="clipboard"> </g> <g id="clipboard-paste"> </g> <g id="clipboard-copy"> </g> <g id="Layer1"> </g> </g> </g></svg>
                                                    </button>
                                                </td>
                                            </tr>
                                            @php
                                                $total_score +=$value->max_score;
                                            @endphp
                                        @empty
                                            <tr class="align-middle">
                                                <td colspan="42">
                                                    <div class="alert alert-danger d-flex justify-content-center">No records found!</div>
                                                </td>
                                            </tr>
                                        @endforelse
                                        <tr class="align-middle">
                                            <td colspan="3" class="text-end">Total :</td>
                                            <td colspan="">
                                                <div class="">{{ $total_score }}</div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="modal fade" id="detailModal" wire:ignore.self data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <form class="w-100">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="detailModalTitle">Details</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" id="detailModalclose" aria-label="Close"></button>
                        </div>
                        <div class="modal-body row">
                            @if($schedule)
                                <ul class="list-group">
                                    <li class="list-group-item"><span><strong> School year:</strong> {{ $schedule->school_year }}</span></li>
                                    <li class="list-group-item"><span><strong> College:</strong> {{ $schedule->college }}</span></li>
                                    <li class="list-group-item"><span><strong> Department:</strong> {{ $schedule->department }}</span></li>
                                    <li class="list-group-item"><span><strong> Faculty name:</strong> {{ $schedule->faculty_fullname }}</span></li>
                                    <li class="list-group-item"><span><strong> Semester:</strong> {{ $schedule->semester }}</span></li>
                                    <li class="list-group-item"><span><strong> Year Level:</strong> {{ $schedule->year_level }}</span></li>
                                    <li class="list-group-item"><span><strong> Subject:</strong> {{ $schedule->subject }}</span></li>
                                    <li class="list-group-item"><span><strong> Schedule:</strong> {{ $schedule->schedule }}</span></li>
                                    <li class="list-group-item"><span><strong> Lecture Unit:</strong> {{ $schedule->lecture_unit }}</span></li>
                                    <li class="list-group-item"><span><strong> Laboratory Unit:</strong> {{ $schedule->laboratory_unit }}</span></li>
                                    <li class="list-group-item"><span><strong> Room :</strong> {{ $schedule->room }}</span></li>
                                </ul>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="weightModal" wire:ignore.self data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <form class="w-100" wire:submit.prevent="updateWeight('weightModal')">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="weightModalTitle">Term Weight Percentage</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" id="weightModalclose" aria-label="Close"></button>
                        </div>
                        <div class="modal-body row">
                            @foreach ($temp_terms as $key =>$value )
                                <div class="col-md-6 mb-3">
                                    <label for="weight" class="form-label">{{ $value['term_name'] }} weight percentage</label>
                                    <input type="number" min="1" step="0.1" id="weight" wire:model="temp_terms.{{ $key }}.weight" placeholder="Term Weight" 
                                        class="form-control @error('term_weight.weight') is-invalid @enderror">
                                    @error('temp_terms.weight')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endforeach
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-success">
                                Save
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="lablecweightModal" wire:ignore.self data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <form class="w-100" wire:submit.prevent="updateLabWeight('lablecweightModal')">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="weightModalTitle">Lab Lec Weight</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" id="weightModalclose" aria-label="Close"></button>
                        </div>
                        <div class="modal-body row">
                             <div class="col-md-12 mb-3">
                                    <label for="weight" class="form-label">Lecture Weight</label>
                                    <input type="number" min="1" step="0.1" id="weight" wire:model="lecture_weight" placeholder="Lecture Weight" 
                                        class="form-control @error('term_weight.weight') is-invalid @enderror">
                                    @error('temp_terms.weight')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @foreach ($laboratory_schedules as $key => $value )
                                <div class="col-md-12 mb-3">
                                    <label for="weight" class="form-label">{{ $value->fullname }}  : {{ $value->schedule_from  }} - {{ $value->schedule_to }}</label>
                                    <input type="number" min="1" step="0.1" id="weight" wire:model="laboratory_schedules_weight.{{ $key }}.weight" placeholder="Laboratory Weight" 
                                        class="form-control @error('term_weight.weight') is-invalid @enderror">
                                    @error('temp_terms.weight')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endforeach
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-success">
                                Save
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="attendanceModal" wire:ignore.self data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <form class="w-100">
                    <div class="modal-content" style="min-height:500px;">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="attendanceModalTitle">Attendance Date</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" id="weightModalclose" aria-label="Close"></button>
                        </div>
                        <div class="modal-body row">
                            <div id="flatpickr-calendar" wire:ignore></div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-success">
                                Save
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>