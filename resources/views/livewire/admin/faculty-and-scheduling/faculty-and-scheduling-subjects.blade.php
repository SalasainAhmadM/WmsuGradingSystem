<div>
    <div class="container-fluid position-relative shadow" style="min-height: 110px;">
        <!-- Centered Title -->
        <div class="d-flex justify-content-center align-items-center" style="height: 100%;">
            <span class="fs-2 fw-bold h1 m-0 brand-color">{{ $detail['department'] }} {{ $title }}s</span>
        </div>

        <!-- Top Right Filters -->
        <div class="position-absolute top-0 end-0 p-2 d-flex flex-column gap-2">
            <select wire:model.defer="detail.year_level" wire:change="redirectPath('year_level', $event.target.value)" class="form-select">
                @foreach ($year_levels as $key => $value)
                    <option value="{{ $value->year_level }}">{{ $value->year_level }}</option>
                @endforeach
            </select>
            <select wire:model.defer="detail.semester"  wire:change="redirectPath('semester', $event.target.value)" class="form-select">
                @foreach ($semesters as $key => $value)
                    <option value="{{ $value->semester }}">{{ $value->semester }}</option>
                @endforeach
            </select>
        </div>
    </div>



    <div class="container-fluid">
        <div class="table-header">
            <livewire:admin.BreadCrumb.BreadCrumb/>
        </div>
        <div class="d-flex justify-content-between my-2 gap-2 row">
            <div class="col-4">
                <input type="search" wire:model.live="filters.search" name="" id="" placeholder="Search ... " class="form-control">
            </div>
            <div class="col-2 d-flex items-center">
            </div>
            <div class="d-flex col justify-content-end gap-2">
                <a class="btn btn-primary" wire:click="add('AddSubjectModal')">
                    <svg  viewBox="0 0 20 20" width="20px" xmlns="http://www.w3.org/2000/svg" fill="none"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill="currentColor" fill-rule="evenodd" d="M9 17a1 1 0 102 0v-6h6a1 1 0 100-2h-6V3a1 1 0 10-2 0v6H3a1 1 0 000 2h6v6z"></path> </g></svg>
                </a>
            </div>
        </div>
        <div class="row ">
            <div class="table-responsive">
                <table class="table table-striped table-bordered position-relative" >
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
                            <th scope="col" class="px-4">#</th>
                            <th scope="col" class="px-4 ">Subject ID</th>
                            <th scope="col" class="px-4 ">Description</th>
                            <th scope="col" class="px-4 ">Faculty name</th>
                            <th scope="col" class="px-4 ">Room</th>
                            <th scope="col" class="px-4 ">Start period</th>
                            <th scope="col" class="px-4 ">End period</th>
                            <th scope="col" class="px-4 ">Type</th>
                            <th scope="col" class="px-4 ">Days</th>
                            <th scope="col" class="text-center px-4 ">Actions</th> 
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($table_data as $key =>$value)
                            <tr class="align-middle">
                                <th scope="row" class="px-4">{{($table_data->currentPage()-1)*$table_data->perPage()+$key+1 }}</th>
                                    <td class="px-4">
                                        <a href="{{route('subject-view',$value->subject_id)}}" target="_blank">
                                            {{$value->subject}}
                                        </a>
                                    </td>
                                    <td class="px-4">
                                            {{$value->description}}
                                    </td>
                                    <td class="px-4">
                                        @if($value->faculty_fullname)
                                            <a href="{{route('faculty-view',$value->faculty_id)  }}" target="_blank">
                                                {{($value->faculty_fullname ? $value->faculty_fullname : "Unassigned")}}
                                            </a>
                                        @else
                                            Unassigned
                                        @endif
                                    </td>
                                    <td class="px-4">
                                        <a href="{{route('room-view',$value->room_id)  }}" target="_blank">
                                            {{$value->room_code}}
                                        </a>
                                    </td>
                                    <td class="px-4">{{$value->schedule_from}}</td>
                                    <td class="px-4">{{$value->schedule_to}}</td>
                                    <td class="px-4">
                                        {{ ($value->is_lec? "LECTURE": "LABORATORY") }}
                                    </td>
                                    <td class="px-4">{{implode(', ', json_decode($value->day, true))}}</td>
                                    <td class="px-4">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('enrolled-student-lists',$value->id) }}" class="btn btn-outline-secondary d-flex justify-content-center items-center" wire:wire:navigate>
                                                <svg fill="currentColor" viewBox="0 -64 640 640"  width="20px"  xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M96 224c35.3 0 64-28.7 64-64s-28.7-64-64-64-64 28.7-64 64 28.7 64 64 64zm448 0c35.3 0 64-28.7 64-64s-28.7-64-64-64-64 28.7-64 64 28.7 64 64 64zm32 32h-64c-17.6 0-33.5 7.1-45.1 18.6 40.3 22.1 68.9 62 75.1 109.4h66c17.7 0 32-14.3 32-32v-32c0-35.3-28.7-64-64-64zm-256 0c61.9 0 112-50.1 112-112S381.9 32 320 32 208 82.1 208 144s50.1 112 112 112zm76.8 32h-8.3c-20.8 10-43.9 16-68.5 16s-47.6-6-68.5-16h-8.3C179.6 288 128 339.6 128 403.2V432c0 26.5 21.5 48 48 48h288c26.5 0 48-21.5 48-48v-28.8c0-63.6-51.6-115.2-115.2-115.2zm-223.7-13.4C161.5 263.1 145.6 256 128 256H64c-35.3 0-64 28.7-64 64v32c0 17.7 14.3 32 32 32h65.9c6.3-47.4 34.9-87.3 75.2-109.4z"></path></g></svg>
                                            </a>
                                            <a href="{{ route('evaluation-lists',$value->id) }}" class="btn btn-outline-warning d-flex justify-content-center items-center" wire:wire:navigate>
                                                <svg fill="currentColor" height="20px" width="20px" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 204 204" xml:space="preserve"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M139.185,157.339h25.175l-27.223,29.022v-26.974C137.137,158.257,138.056,157.339,139.185,157.339z M108.805,44.654 l7.761-1.004l-4.845-6.953L108.805,44.654z M179.518,5v142.339h-40.333c-6.644,0-12.048,5.405-12.048,12.048V204H29.482 c-2.762,0-5-2.239-5-5V5c0-2.761,2.238-5,5-5h145.035C177.279,0,179.518,2.239,179.518,5z M95.746,65.76 c0.568,0.208,1.148,0.307,1.721,0.307c2.038,0,3.953-1.256,4.694-3.281l2.765-7.546l18.084-2.34l4.595,6.594 c0.973,1.395,2.526,2.142,4.106,2.142c0.987,0,1.984-0.292,2.854-0.898c2.266-1.579,2.822-4.695,1.244-6.96l-21.377-30.677 c-0.008-0.011-0.019-0.02-0.027-0.031c-0.138-0.196-0.303-0.372-0.47-0.547c-0.061-0.064-0.113-0.141-0.177-0.201 c-0.132-0.124-0.286-0.226-0.432-0.336c-0.117-0.088-0.225-0.189-0.348-0.266c-0.094-0.059-0.202-0.098-0.301-0.151 c-0.193-0.103-0.384-0.209-0.588-0.285c-0.014-0.005-0.025-0.014-0.039-0.019c-0.117-0.043-0.237-0.057-0.354-0.091 c-0.183-0.052-0.364-0.11-0.552-0.141c-0.166-0.028-0.33-0.03-0.496-0.04c-0.157-0.01-0.313-0.028-0.471-0.024 c-0.169,0.005-0.333,0.034-0.5,0.056c-0.155,0.02-0.31,0.033-0.464,0.068c-0.166,0.038-0.324,0.099-0.486,0.154 c-0.145,0.049-0.292,0.089-0.434,0.153c-0.192,0.086-0.369,0.197-0.549,0.306c-0.09,0.055-0.185,0.091-0.273,0.152 c-0.01,0.007-0.018,0.017-0.028,0.024c-0.201,0.142-0.382,0.31-0.561,0.481c-0.061,0.058-0.132,0.107-0.19,0.167 c-0.121,0.128-0.219,0.278-0.326,0.419c-0.092,0.121-0.197,0.234-0.276,0.362c-0.052,0.083-0.086,0.18-0.134,0.267 c-0.111,0.205-0.222,0.409-0.303,0.626c-0.005,0.013-0.013,0.023-0.017,0.036L92.772,59.345 C91.822,61.938,93.153,64.81,95.746,65.76z M108.899,152.339c0-2.761-2.238-5-5-5H53.25c-2.762,0-5,2.239-5,5c0,2.761,2.238,5,5,5 h50.65C106.661,157.339,108.899,155.1,108.899,152.339z M130.157,121.839c0-2.761-2.238-5-5-5H53.25c-2.762,0-5,2.239-5,5 c0,2.761,2.238,5,5,5h71.907C127.919,126.839,130.157,124.6,130.157,121.839z M145.75,91.339c0-2.761-2.238-5-5-5h-87.5 c-2.762,0-5,2.239-5,5c0,2.761,2.238,5,5,5h87.5C143.512,96.339,145.75,94.1,145.75,91.339z M164.797,32.019 c-0.354-2.737-2.852-4.672-5.601-4.317l-6.681,0.865l-0.865-6.681c-0.354-2.738-2.851-4.673-5.601-4.317 c-2.738,0.354-4.672,2.862-4.317,5.6l0.865,6.681l-6.681,0.865c-2.738,0.354-4.672,2.861-4.317,5.6 c0.326,2.521,2.477,4.359,4.953,4.359c0.213,0,0.43-0.014,0.648-0.042l6.681-0.865l0.865,6.681c0.326,2.521,2.477,4.359,4.953,4.359 c0.213,0,0.43-0.014,0.647-0.042c2.738-0.354,4.672-2.862,4.317-5.6l-0.865-6.681l6.681-0.865 C163.218,37.265,165.151,34.758,164.797,32.019z"></path> </g></svg>
                                            </a>
                                            <button type="button" wire:click="view({{ $value->id}},'deleteCurriculumModal')"  class="btn btn-outline-danger d-flex justify-content-center items-center">
                                                <svg fill="currentColor" width="20px"  viewBox="0 0 64 64" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <rect id="Icons" x="-64" y="-64" width="1280" height="800" style="fill:none;"></rect> <g id="Icons1" serif:id="Icons"> <g id="Strike"> </g> <g id="H1"> </g> <g id="H2"> </g> <g id="H3"> </g> <g id="list-ul"> </g> <g id="hamburger-1"> </g> <g id="hamburger-2"> </g> <g id="list-ol"> </g> <g id="list-task"> </g> <g id="trash"> <path d="M19.186,16.493l0,-1.992c0.043,-3.346 2.865,-6.296 6.277,-6.427c3.072,-0.04 10.144,-0.04 13.216,0c3.346,0.129 6.233,3.012 6.277,6.427l0,1.992l9.106,0l0,4l-4.442,0l0,29.11c-0.043,3.348 -2.865,6.296 -6.278,6.428c-7.462,0.095 -14.926,0.002 -22.39,0.002c-3.396,-0.044 -6.385,-2.96 -6.429,-6.43l0,-29.11l-4.443,0l0,-4l9.106,0Zm26.434,4l-27.099,0c-0.014,9.72 -0.122,19.441 0.002,29.16c0.049,1.25 1.125,2.33 2.379,2.379c7.446,0.095 14.893,0.095 22.338,0c1.273,-0.049 2.363,-1.163 2.38,-2.455l0,-29.084Zm-4.701,-4c-0.014,-0.83 0,-1.973 0,-1.973c0,0 -0.059,-2.418 -2.343,-2.447c-3.003,-0.039 -10.007,-0.039 -13.01,0c-1.273,0.049 -2.363,1.162 -2.38,2.454l0,1.966l17.733,0Z" style="fill-rule:nonzero;"></path> <rect x="22.58" y="28.099" width="3" height="16.327" style="fill-rule:nonzero;"></rect> <rect x="30.571" y="28.099" width="3" height="16.327" style="fill-rule:nonzero;"></rect> <rect x="38.58" y="28.099" width="3" height="16.327" style="fill-rule:nonzero;"></rect> </g> <g id="vertical-menu"> </g> <g id="horizontal-menu"> </g> <g id="sidebar-2"> </g> <g id="Pen"> </g> <g id="Pen1" serif:id="Pen"> </g> <g id="clock"> </g> <g id="external-link"> </g> <g id="hr"> </g> <g id="info"> </g> <g id="warning"> </g> <g id="plus-circle"> </g> <g id="minus-circle"> </g> <g id="vue"> </g> <g id="cog"> </g> <g id="logo"> </g> <g id="radio-check"> </g> <g id="eye-slash"> </g> <g id="eye"> </g> <g id="toggle-off"> </g> <g id="shredder"> </g> <g id="spinner--loading--dots-" serif:id="spinner [loading, dots]"> </g> <g id="react"> </g> <g id="check-selected"> </g> <g id="turn-off"> </g> <g id="code-block"> </g> <g id="user"> </g> <g id="coffee-bean"> </g> <g id="coffee-beans"> <g id="coffee-bean1" serif:id="coffee-bean"> </g> </g> <g id="coffee-bean-filled"> </g> <g id="coffee-beans-filled"> <g id="coffee-bean2" serif:id="coffee-bean"> </g> </g> <g id="clipboard"> </g> <g id="clipboard-paste"> </g> <g id="clipboard-copy"> </g> <g id="Layer1"> </g> </g> </g></svg>
                                            </button>
                                        </div>
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

    <div class="modal fade" id="AddSubjectModal" wire:ignore.self data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable d-flex justify-content-center">
            <form wire:submit.prevent="addSchedule('AddSubjectModal')">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="AddSubjectModalLabel">Add Subject</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" id="AddSubjectModalclose" aria-label="Close"></button>
                    </div>
                    <div class="modal-body row">
                        <div class="col-md-12 mb-3">
                            <label for="faculty_id" class="form-label">Select Faculty</label>
                            <select name="faculty_id" id="faculty_id" wire:model.live="detail.faculty_id" class="form-select @error('detail.faculty_id') is-invalid @enderror">  
                                <option value="">Select Faculty</option>
                                @foreach ($faculty as $key => $value )
                                     <option value="{{ $value->id }}" >{{ $value->first_name.' - '.$value->middle_name.' '.$value->last_name.' '.$value->suffix }}</option>
                                @endforeach
                            </select>
                            @error('detail.faculty_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror  
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label for="schedule_id" class="form-label">Select Curriculum</label>
                            <select name="" id="" wire:model.live="subject_school_year" wire:change="filterSubject()" class="form-select"> 
                                <option value="">Select Curriculum</option>
                                @foreach ($curriculums as $key => $value )
                                    <option value="{{ $value->school_year_id }}" >{{ $value->year_start .' - '.$value->year_end }}</option>
                                @endforeach
                            </select> 
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="schedule_id" class="form-label">Select Subject Schedule</label>
                            <div class="d-flex">
                                <select name="schedule_id" id="schedule_id" @if(!intval($detail['faculty_id'])) disabled @endif wire:model.defer="detail.schedule_id" wire:change="selectSubject()" class="form-select select2 @error('detail.schedule_id') is-invalid @enderror">  
                                    <option value="">Select Subject</option>
                                    @forelse ($subjectschedules as $key => $value )
                                         <option value="{{ $value->id }}" >{{ $value->subject_id.' - '.$value->subject_code.' '.
                                            $value->schedule_from.' - '.
                                            $value->schedule_to.' ( '.
                                            implode(', ', json_decode($value->day, true)).' )'.' '.$value->room_code.($value->is_lec? "-LECTURE": "-LABORATORY")
                                         }}</option>
                                    @empty
                                         <option value="">No schedules added yet or curriculum is empty</option>
                                    @endforelse
                                </select>
                                <button class="btn btn-primary" type="submit" @if(!intval($detail['faculty_id'])) disabled @endif>
                                    Add
                                </button>
                            </div>
                        </div>
                        @error('detail.schedule_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror  
                        <div class="col-md-12 mb-3">
                            <ul class="list-group">
                                @foreach ($faculty_subjects as $key =>  $value )
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ $value->subject_id.' - '.$value->subject_code.' '.
                                            $value->schedule_from.' - '.
                                            $value->schedule_to.' ( '.
                                            implode(', ', json_decode($value->day, true)).' )'.' '.$value->room_code.($value->is_lec? "-LECTURE": "-LABORATORY")
                                        }}
                                        <button type="button" class="btn btn-sm btn-danger"
                                            onclick="confirmDelete({{ $value->id }})">
                                            Delete
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="EditSubjectModal" wire:ignore.self data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
         <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable d-flex justify-content-center">
            <form wire:submit.prevent="editSchedule('EditSubjectModal')">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="EditSubjectModalLabel">Edit Subject</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" id="EditSubjectModalclose" aria-label="Close"></button>
                    </div>
                    <div class="modal-body row">
                        <div class="col-md-12 mb-3">
                            <label for="schedule_id" class="form-label">Select Subject Schedule</label>
                            <select name="schedule_id" id="schedule_id" wire:model.defer="detail.schedule_id" class="form-select @error('detail.schedule_id') is-invalid @enderror">  
                                @foreach ($subjectschedules as $key => $value )
                                     <option value="{{ $value->id }}" >{{ $value->subject_id.' - '.$value->subject_code.' '.$value->room_code.' '.
                                        $value->schedule_from.' - '.
                                        $value->schedule_to.' ('.
                                        implode(', ', json_decode($value->day, true)).')'
                                     }}</option>
                                @endforeach
                            </select>
                            @error('detail.schedule_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror  
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="faculty_id" class="form-label">Select Faculty</label>
                            <select name="faculty_id" id="faculty_id" wire:model="detail.faculty_id" class="form-select @error('detail.faculty_id') is-invalid @enderror">  
                                <option value="">Select Faculty</option>
                                @foreach ($faculty as $key => $value )
                                     <option value="{{ $value->id }}" >{{ $value->first_name.' - '.$value->middle_name.' '.$value->last_name.' '.$value->suffix }}</option>
                                @endforeach
                            </select>
                            @error('detail.schedule_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror  
                        </div>  
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="room_id" class="form-label">Room</label>
                                <select name="room_id" id="room_id" wire:model="detail.room_id" disabled class="form-select @error('detail.room_id') is-invalid @enderror">  
                                    <option value="">Select Room</option>
                                    @foreach ($rooms as $key => $value )
                                        <option value="{{ $value->id }}" >{{ $value->code.' - '.$value->name }}</option>
                                    @endforeach
                                </select>
                                @error('detail.room_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror  
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="day" class="form-label">Days</label>
                                <input type="text" name="day" id="day" wire:model="detail.day" disabled class="form-select @error('detail.day') is-invalid @enderror">  
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="schedule_from" class="form-label">Start period</label>
                                <input type="time" name="schedule_from" id="schedule_from"  wire:model="detail.schedule_from" disabled class="form-select @error('detail.schedule_from') is-invalid @enderror">  
                                @error('detail.schedule_from')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror  
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="schedule_to" class="form-label">End period</label>
                                <input type="time" name="schedule_to" id="schedule_to" wire:model="detail.schedule_to" disabled class="form-select @error('detail.schedule_to') is-invalid @enderror">  
                                @error('detail.schedule_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror  
                            </div>
                            
                            <div class="col-6 mb-3 d-flex align-items-end">
                                <div>
                                    Is Lecture?
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="radioDefault" disabled id="radioDefault1" value="1" wire:model="detail.is_lec">
                                        <label class="form-check-label" for="radioDefault1">
                                            Lecture Subject
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="radioDefault" disabled id="radioDefault2" value="0" wire:model="detail.is_lec">
                                        <label class="form-check-label" for="radioDefault2">
                                            Laboratory Subject
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success" >Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="deleteCurriculumModal" wire:ignore.self
        data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable d-flex justify-content-center">
            <form wire:submit.prevent="deleteSchedule({{ $detail['id'] }},'deleteCurriculumModal')">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="deleteCurriculumModalTitle">Delete Subject Schedule</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" id="deleteCurriculumModalclose" aria-label="Close"></button>
                    </div>
                    <div class="modal-body row">
                        <p class="text-danger">
                            Warning!!! deleting this will also delete the enrolled students to this subject schedules and the grades generated by faculty.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger" >Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        function confirmDelete(id) {
            $.confirm({
                title: 'Confirm Deletion',
                content: 'Are you sure you want to delete this item? This action cannot be undone.',
                type: 'red',
                buttons: {
                    confirm: {
                        text: 'Yes, Delete',
                        btnClass: 'btn-red',
                        action: function () {
                            // Dispatch a Livewire event or do AJAX here
                            Livewire.dispatch('deleteSubject', { id: id });

                        }
                    },
                    cancel: {
                        text: 'Cancel',
                        action: function () {
                            // Optional: do something on cancel
                        }
                    }
                }
            });
        }
    </script>

</div>
