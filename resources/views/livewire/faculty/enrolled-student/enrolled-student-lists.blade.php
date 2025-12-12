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
            <div class="col d-flex justify-items-start gap-1">
             
            </div>
            <div class="d-flex col justify-content-end gap-2">
                <a href="{{ route('my-evaluation-lists',[
                        'school_year' => $school_year,
                        'semester' => $semester,
                        'schedule_id' => $detail['schedule_id']]) }}" class="btn btn-outline-warning d-flex justify-content-center items-center" wire:wire:navigate>
                    <svg fill="currentColor" height="20px" width="20px" version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 204 204" xml:space="preserve"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M139.185,157.339h25.175l-27.223,29.022v-26.974C137.137,158.257,138.056,157.339,139.185,157.339z M108.805,44.654 l7.761-1.004l-4.845-6.953L108.805,44.654z M179.518,5v142.339h-40.333c-6.644,0-12.048,5.405-12.048,12.048V204H29.482 c-2.762,0-5-2.239-5-5V5c0-2.761,2.238-5,5-5h145.035C177.279,0,179.518,2.239,179.518,5z M95.746,65.76 c0.568,0.208,1.148,0.307,1.721,0.307c2.038,0,3.953-1.256,4.694-3.281l2.765-7.546l18.084-2.34l4.595,6.594 c0.973,1.395,2.526,2.142,4.106,2.142c0.987,0,1.984-0.292,2.854-0.898c2.266-1.579,2.822-4.695,1.244-6.96l-21.377-30.677 c-0.008-0.011-0.019-0.02-0.027-0.031c-0.138-0.196-0.303-0.372-0.47-0.547c-0.061-0.064-0.113-0.141-0.177-0.201 c-0.132-0.124-0.286-0.226-0.432-0.336c-0.117-0.088-0.225-0.189-0.348-0.266c-0.094-0.059-0.202-0.098-0.301-0.151 c-0.193-0.103-0.384-0.209-0.588-0.285c-0.014-0.005-0.025-0.014-0.039-0.019c-0.117-0.043-0.237-0.057-0.354-0.091 c-0.183-0.052-0.364-0.11-0.552-0.141c-0.166-0.028-0.33-0.03-0.496-0.04c-0.157-0.01-0.313-0.028-0.471-0.024 c-0.169,0.005-0.333,0.034-0.5,0.056c-0.155,0.02-0.31,0.033-0.464,0.068c-0.166,0.038-0.324,0.099-0.486,0.154 c-0.145,0.049-0.292,0.089-0.434,0.153c-0.192,0.086-0.369,0.197-0.549,0.306c-0.09,0.055-0.185,0.091-0.273,0.152 c-0.01,0.007-0.018,0.017-0.028,0.024c-0.201,0.142-0.382,0.31-0.561,0.481c-0.061,0.058-0.132,0.107-0.19,0.167 c-0.121,0.128-0.219,0.278-0.326,0.419c-0.092,0.121-0.197,0.234-0.276,0.362c-0.052,0.083-0.086,0.18-0.134,0.267 c-0.111,0.205-0.222,0.409-0.303,0.626c-0.005,0.013-0.013,0.023-0.017,0.036L92.772,59.345 C91.822,61.938,93.153,64.81,95.746,65.76z M108.899,152.339c0-2.761-2.238-5-5-5H53.25c-2.762,0-5,2.239-5,5c0,2.761,2.238,5,5,5 h50.65C106.661,157.339,108.899,155.1,108.899,152.339z M130.157,121.839c0-2.761-2.238-5-5-5H53.25c-2.762,0-5,2.239-5,5 c0,2.761,2.238,5,5,5h71.907C127.919,126.839,130.157,124.6,130.157,121.839z M145.75,91.339c0-2.761-2.238-5-5-5h-87.5 c-2.762,0-5,2.239-5,5c0,2.761,2.238,5,5,5h87.5C143.512,96.339,145.75,94.1,145.75,91.339z M164.797,32.019 c-0.354-2.737-2.852-4.672-5.601-4.317l-6.681,0.865l-0.865-6.681c-0.354-2.738-2.851-4.673-5.601-4.317 c-2.738,0.354-4.672,2.862-4.317,5.6l0.865,6.681l-6.681,0.865c-2.738,0.354-4.672,2.861-4.317,5.6 c0.326,2.521,2.477,4.359,4.953,4.359c0.213,0,0.43-0.014,0.648-0.042l6.681-0.865l0.865,6.681c0.326,2.521,2.477,4.359,4.953,4.359 c0.213,0,0.43-0.014,0.647-0.042c2.738-0.354,4.672-2.862,4.317-5.6l-0.865-6.681l6.681-0.865 C163.218,37.265,165.151,34.758,164.797,32.019z"></path> </g></svg>
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
                            <th scope="col" class="px-4 ">ID</th>
                            <th scope="col" class="px-4 ">FullName</th>
                            <th scope="col" class="px-4 ">College</th>
                            <th scope="col" class="px-4 ">Department</th>
                            <th scope="col" class="px-4 ">Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($table_data as $key =>$value)
                            <tr class="align-middle">
                                <th scope="row" class="px-4">{{($table_data->currentPage()-1)*$table_data->perPage()+$key+1 }}</th>
                                    <td class="px-4">
                                        <a href="/faculty/students/view-{{ $value->id }}" target="_blank">
                                            {{$value->code}}
                                        </a>
                                    </td>
                                    <td class="px-4">
                                        {{$value->fullname}}
                                    </td>
                                    <td class="px-4">
                                        <a href="/faculty/colleges/view-{{ $value->college_id }}" target="_blank">
                                            {{$value->college_code}}
                                        </a>
                                    </td>
                                    <td class="px-4">
                                        <a href="/faculty/departments/view-{{ $value->department_id }}"  target="_blank">
                                            {{$value->department_code}}
                                        </a>
                                    </td>
                                    <td class="px-4">{{$value->email}}</td>
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
        
        <div class="modal fade" id="AddModal" wire:ignore.self data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable d-flex justify-content-center">
                <form wire:submit.prevent="saveAdd('AddModal')">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="AddModalLabel">Add Student</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" id="AddModalclose" aria-label="Close"></button>
                        </div>
                        <div class="modal-body row">
                            <div class="col-md-6 mb-3">
                                <label for="search" class="form-label">Search student</label>
                                <input type="text" id="search" wire:model="studentFilter.search" wire:change="studentList()" placeholder="Search subject ..." class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="college_id" class="form-label">Filter College</label>
                                <select name="college_id" id="college_id" wire:model.defer="studentFilter.college_id" wire:change="studentList()" class="form-select ">  
                                    <option value="">Select College</option>
                                    @forelse ($colleges as $key => $value )
                                        <option value="{{ $value->id }}" >{{ $value->code.' - '.$value->name }}</option>
                                    @empty
                                        <option value="">No Data</option>
                                    @endforelse
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="student_id" class="form-label">Select Student</label>
                                <select name="student_id" id="student_id" wire:model.defer="detail.student_id" class="form-select @error('detail.student_id') is-invalid @enderror">  
                                    <option value="">Select Student</option>
                                    @forelse ($students as $key => $value )
                                         <option value="{{ $value->id }}" >{{ $value->code.' '.$value->fullname }}</option>
                                    @empty
                                        <option value="">No Data</option>
                                    @endforelse
                                </select>
                                @error('detail.student_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror  
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary" >Add</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    
        <div class="modal fade" id="deleteModal" wire:ignore.self
            data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable d-flex justify-content-center">
                <form wire:submit.prevent="saveDelete({{ $detail['student_id'] }},'deleteModal')">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="deleteCurriculumModalTitle">Delete Enrolled Student</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" id="deleteModalclose" aria-label="Close"></button>
                        </div>
                        <div class="modal-body row">
                            <p class="text-danger">
                                Warning!!! deleting this will also delete the students grades!.
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
