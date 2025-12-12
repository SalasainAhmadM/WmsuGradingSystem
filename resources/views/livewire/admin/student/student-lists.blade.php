<div>
    <div class="container-fluid d-flex justify-content-center shadow">
        <span class="fs-2 fw-bold h1 m-0 brand-color">  {{ $title }}s</span>
    </div>
    <div class="container-fluid">
        <div class="table-header">
            <livewire:admin.BreadCrumb.BreadCrumb/>
        </div>
        <div class="d-flex justify-content-between my-2 gap-1 row">
            <div class="col-4">
                <input type="search" wire:model.live="filters.search" name="" id="" placeholder="Search ... " class="form-control">
            </div>
            <div class="col d-flex justify-items-start gap-1">
                <select name="" id="" wire:model.live="filters.college_id" class="form-select"> 
                    <option value="">Select College</option>
                    @foreach ($colleges as $key => $value )
                        <option value="{{ $value->id }}" >{{ $value->code }}</option>
                    @endforeach
                </select>
                <select name="" id="" wire:model.live="filters.department_id" class="form-select"> 
                    <option value="">Select Department</option>
                    @foreach ($departments as $key => $value )
                        <option value="{{ $value->id }}" >{{ $value->code }}</option>
                    @endforeach
                </select>
                <select name="" id="" wire:model.live="filters.year_level_id" class="form-select"> 
                    <option value="">Select Year Level</option>
                    @foreach ($year_levels as $key => $value )
                        <option value="{{ $value->id }}" >{{ $value->year_level }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col">
                <div class="d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-sm btn-outline-primary waves-effect waves-light"
                        wire:click="openImportModal('importModal')">
                        <svg viewBox="0 0 24 24" width="15px" class="mx-1" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 14L11.6464 14.3536L12 14.7071L12.3536 14.3536L12 14ZM12.5 5C12.5 4.72386 12.2761 4.5 12 4.5C11.7239 4.5 11.5 4.72386 11.5 5L12.5 5ZM6.64645 9.35355L11.6464 14.3536L12.3536 13.6464L7.35355 8.64645L6.64645 9.35355ZM12.3536 14.3536L17.3536 9.35355L16.6464 8.64645L11.6464 13.6464L12.3536 14.3536ZM12.5 14L12.5 5L11.5 5L11.5 14L12.5 14Z" fill="#222222"></path>
                            <path d="M5 16L5 17C5 18.1046 5.89543 19 7 19L17 19C18.1046 19 19 18.1046 19 17V16" stroke="#222222"></path>
                        </svg>
                        Import
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary waves-effect waves-light"
                        wire:click="downloadTemplate()">
                        Template
                    </button>
                    <a class="btn btn-primary" wire:wire:navigate href="{{ route($route.'-add') }}">
                        <svg  viewBox="0 0 20 20" width="20px" xmlns="http://www.w3.org/2000/svg" fill="none"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill="currentColor" fill-rule="evenodd" d="M9 17a1 1 0 102 0v-6h6a1 1 0 100-2h-6V3a1 1 0 10-2 0v6H3a1 1 0 000 2h6v6z"></path> </g></svg>
                    </a>
                </div>
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
                            <th scope="col" class="px-4 ">Is Active?</th>
                            <th scope="col" class="px-4 ">Year Level</th>
                            <th scope="col" class="text-center px-4 ">Actions</th> 
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($table_data as $key =>$value)
                            <tr class="align-middle">
                                <th scope="row" class="px-4">{{($table_data->currentPage()-1)*$table_data->perPage()+$key+1 }}</th>
                                    <td class="px-4">
                                        {{$value->code}}
                                    </td>
                                    <td class="px-4">
                                        {{$value->fullname}}
                                    </td>
                                    <td class="px-4">
                                        <a href="/admin/colleges/view-{{ $value->college_id }}" target="_blank">
                                            {{$value->college_code}}
                                        </a>
                                    </td>
                                    <td class="px-4">
                                        <a href="/admin/departments/view-{{ $value->department_id }}"  target="_blank">
                                            {{$value->department_code}}
                                        </a>
                                    </td>
                                    <td class="px-4">{{$value->email}}</td>
                                    <td class="px-4">
                                        @if($value->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-4">{{$value->year_level}}</td>

                                    <td class="px-4">
                                        <div class="d-flex justify-content-center gap-2">
                                            <!-- <a href="{{ route($route.'-view',$value->id) }}" type="button" wire:wire:navigate  class="btn btn-outline-secondary d-flex justify-content-center items-center">
                                                <svg viewBox="0 0 24 24"  width="20px" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9ZM11 12C11 11.4477 11.4477 11 12 11C12.5523 11 13 11.4477 13 12C13 12.5523 12.5523 13 12 13C11.4477 13 11 12.5523 11 12Z" fill="currentColor"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M21.83 11.2807C19.542 7.15186 15.8122 5 12 5C8.18777 5 4.45796 7.15186 2.17003 11.2807C1.94637 11.6844 1.94361 12.1821 2.16029 12.5876C4.41183 16.8013 8.1628 19 12 19C15.8372 19 19.5882 16.8013 21.8397 12.5876C22.0564 12.1821 22.0536 11.6844 21.83 11.2807ZM12 17C9.06097 17 6.04052 15.3724 4.09173 11.9487C6.06862 8.59614 9.07319 7 12 7C14.9268 7 17.9314 8.59614 19.9083 11.9487C17.9595 15.3724 14.939 17 12 17Z" fill="currentColor"></path> </g></svg>
                                            </a> -->
                                            <!-- <button class="btn btn-outline-secondary" wire:click="change_password({{ $value->user_id }},'changePasswordModal')">
                                                <svg fill="currentColor" width="20px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M17,9V7c0-2.8-2.2-5-5-5S7,4.2,7,7v2c-1.7,0-3,1.3-3,3v7c0,1.7,1.3,3,3,3h10c1.7,0,3-1.3,3-3v-7C20,10.3,18.7,9,17,9z M9,7c0-1.7,1.3-3,3-3s3,1.3,3,3v2H9V7z M13.1,15.5c0,0-0.1,0.1-0.1,0.1V17c0,0.6-0.4,1-1,1s-1-0.4-1-1v-1.4c-0.6-0.6-0.7-1.5-0.1-2.1c0.6-0.6,1.5-0.7,2.1-0.1C13.6,13.9,13.7,14.9,13.1,15.5z"></path></g></svg>
                                            </button> -->
                                            <!-- <button class="btn btn-outline-success" wire:click="gradeLists({{$value->id }},'gradesModal')">
                                                <svg viewBox="0 0 1024 1024" width="20px" class="icon" version="1.1" xmlns="http://www.w3.org/2000/svg" fill="currentColor"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M981.333333 960h-21.333333V576a21.333333 21.333333 0 0 0-42.666667 0v384h-128V213.333333a42.666667 42.666667 0 0 1 42.666667-42.666666h42.666667a42.666667 42.666667 0 0 1 42.666666 42.666666v21.333334a21.333333 21.333333 0 0 0 42.666667 0v-21.333334a85.333333 85.333333 0 0 0-85.333333-85.333333h-42.666667a85.333333 85.333333 0 0 0-85.333333 85.333333v746.666667h-85.333334V426.666667a85.333333 85.333333 0 0 0-85.333333-85.333334h-42.666667a85.333333 85.333333 0 0 0-85.333333 85.333334v533.333333h-85.333333V640a85.333333 85.333333 0 0 0-85.333334-85.333333h-42.666666a85.333333 85.333333 0 0 0-85.333334 85.333333v320H64V42.666667a21.333333 21.333333 0 0 0-42.666667 0v938.666666a21.333333 21.333333 0 0 0 21.333334 21.333334h938.666666a21.333333 21.333333 0 0 0 0-42.666667z m-661.333333 0H192V640a42.666667 42.666667 0 0 1 42.666667-42.666667h42.666666a42.666667 42.666667 0 0 1 42.666667 42.666667z m298.666667 0h-128V426.666667a42.666667 42.666667 0 0 1 42.666666-42.666667h42.666667a42.666667 42.666667 0 0 1 42.666667 42.666667z" fill="currentColor"></path><path d="M938.666667 384a21.333333 21.333333 0 0 0-21.333334 21.333333v85.333334a21.333333 21.333333 0 0 0 42.666667 0v-85.333334a21.333333 21.333333 0 0 0-21.333333-21.333333zM958.293333 311.893333a24.533333 24.533333 0 0 0-4.48-7.04l-3.2-2.56a16.213333 16.213333 0 0 0-3.84-1.92L942.933333 298.666667a21.333333 21.333333 0 0 0-12.373333 1.28 19.2 19.2 0 0 0-11.52 11.52 21.333333 21.333333 0 0 0-1.706667 8.533333 21.333333 21.333333 0 0 0 6.186667 15.146667 21.333333 21.333333 0 0 0 7.04 4.48A21.333333 21.333333 0 0 0 938.666667 341.333333a21.333333 21.333333 0 0 0 15.146666-6.186666A22.4 22.4 0 0 0 960 320a21.333333 21.333333 0 0 0-1.706667-8.106667z" fill="currentColor"></path></g></svg>
                                            </button> -->
                                            <button class="btn btn-outline-primary" wire:click="gradeLists_v2({{$value->id }},'gradesVersionTwoModal')">
                                                <svg viewBox="0 0 1024 1024" width="20px" class="icon" version="1.1" xmlns="http://www.w3.org/2000/svg" fill="currentColor"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M981.333333 960h-21.333333V576a21.333333 21.333333 0 0 0-42.666667 0v384h-128V213.333333a42.666667 42.666667 0 0 1 42.666667-42.666666h42.666667a42.666667 42.666667 0 0 1 42.666666 42.666666v21.333334a21.333333 21.333333 0 0 0 42.666667 0v-21.333334a85.333333 85.333333 0 0 0-85.333333-85.333333h-42.666667a85.333333 85.333333 0 0 0-85.333333 85.333333v746.666667h-85.333334V426.666667a85.333333 85.333333 0 0 0-85.333333-85.333334h-42.666667a85.333333 85.333333 0 0 0-85.333333 85.333334v533.333333h-85.333333V640a85.333333 85.333333 0 0 0-85.333334-85.333333h-42.666666a85.333333 85.333333 0 0 0-85.333334 85.333333v320H64V42.666667a21.333333 21.333333 0 0 0-42.666667 0v938.666666a21.333333 21.333333 0 0 0 21.333334 21.333334h938.666666a21.333333 21.333333 0 0 0 0-42.666667z m-661.333333 0H192V640a42.666667 42.666667 0 0 1 42.666667-42.666667h42.666666a42.666667 42.666667 0 0 1 42.666667 42.666667z m298.666667 0h-128V426.666667a42.666667 42.666667 0 0 1 42.666666-42.666667h42.666667a42.666667 42.666667 0 0 1 42.666667 42.666667z" fill="currentColor"></path><path d="M938.666667 384a21.333333 21.333333 0 0 0-21.333334 21.333333v85.333334a21.333333 21.333333 0 0 0 42.666667 0v-85.333334a21.333333 21.333333 0 0 0-21.333333-21.333333zM958.293333 311.893333a24.533333 24.533333 0 0 0-4.48-7.04l-3.2-2.56a16.213333 16.213333 0 0 0-3.84-1.92L942.933333 298.666667a21.333333 21.333333 0 0 0-12.373333 1.28 19.2 19.2 0 0 0-11.52 11.52 21.333333 21.333333 0 0 0-1.706667 8.533333 21.333333 21.333333 0 0 0 6.186667 15.146667 21.333333 21.333333 0 0 0 7.04 4.48A21.333333 21.333333 0 0 0 938.666667 341.333333a21.333333 21.333333 0 0 0 15.146666-6.186666A22.4 22.4 0 0 0 960 320a21.333333 21.333333 0 0 0-1.706667-8.106667z" fill="currentColor"></path></g></svg>
                                            </button>
                                            <a href="{{ route($route.'-edit',$value->id) }}" type="button" wire:wire:navigate  class="btn btn-outline-success d-flex justify-content-center items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="" width="20px" viewbox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                                    <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                            @if($value->is_active)
                                                <a href="{{ route($route.'-delete',$value->id) }}" type="button" wire:wire:navigate  class="btn btn-outline-danger d-flex justify-content-center items-center">
                                                    <svg fill="currentColor" width="20px"  viewBox="0 0 64 64" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <rect id="Icons" x="-64" y="-64" width="1280" height="800" style="fill:none;"></rect> <g id="Icons1" serif:id="Icons"> <g id="Strike"> </g> <g id="H1"> </g> <g id="H2"> </g> <g id="H3"> </g> <g id="list-ul"> </g> <g id="hamburger-1"> </g> <g id="hamburger-2"> </g> <g id="list-ol"> </g> <g id="list-task"> </g> <g id="trash"> <path d="M19.186,16.493l0,-1.992c0.043,-3.346 2.865,-6.296 6.277,-6.427c3.072,-0.04 10.144,-0.04 13.216,0c3.346,0.129 6.233,3.012 6.277,6.427l0,1.992l9.106,0l0,4l-4.442,0l0,29.11c-0.043,3.348 -2.865,6.296 -6.278,6.428c-7.462,0.095 -14.926,0.002 -22.39,0.002c-3.396,-0.044 -6.385,-2.96 -6.429,-6.43l0,-29.11l-4.443,0l0,-4l9.106,0Zm26.434,4l-27.099,0c-0.014,9.72 -0.122,19.441 0.002,29.16c0.049,1.25 1.125,2.33 2.379,2.379c7.446,0.095 14.893,0.095 22.338,0c1.273,-0.049 2.363,-1.163 2.38,-2.455l0,-29.084Zm-4.701,-4c-0.014,-0.83 0,-1.973 0,-1.973c0,0 -0.059,-2.418 -2.343,-2.447c-3.003,-0.039 -10.007,-0.039 -13.01,0c-1.273,0.049 -2.363,1.162 -2.38,2.454l0,1.966l17.733,0Z" style="fill-rule:nonzero;"></path> <rect x="22.58" y="28.099" width="3" height="16.327" style="fill-rule:nonzero;"></rect> <rect x="30.571" y="28.099" width="3" height="16.327" style="fill-rule:nonzero;"></rect> <rect x="38.58" y="28.099" width="3" height="16.327" style="fill-rule:nonzero;"></rect> </g> <g id="vertical-menu"> </g> <g id="horizontal-menu"> </g> <g id="sidebar-2"> </g> <g id="Pen"> </g> <g id="Pen1" serif:id="Pen"> </g> <g id="clock"> </g> <g id="external-link"> </g> <g id="hr"> </g> <g id="info"> </g> <g id="warning"> </g> <g id="plus-circle"> </g> <g id="minus-circle"> </g> <g id="vue"> </g> <g id="cog"> </g> <g id="logo"> </g> <g id="radio-check"> </g> <g id="eye-slash"> </g> <g id="eye"> </g> <g id="toggle-off"> </g> <g id="shredder"> </g> <g id="spinner--loading--dots-" serif:id="spinner [loading, dots]"> </g> <g id="react"> </g> <g id="check-selected"> </g> <g id="turn-off"> </g> <g id="code-block"> </g> <g id="user"> </g> <g id="coffee-bean"> </g> <g id="coffee-beans"> <g id="coffee-bean1" serif:id="coffee-bean"> </g> </g> <g id="coffee-bean-filled"> </g> <g id="coffee-beans-filled"> <g id="coffee-bean2" serif:id="coffee-bean"> </g> </g> <g id="clipboard"> </g> <g id="clipboard-paste"> </g> <g id="clipboard-copy"> </g> <g id="Layer1"> </g> </g> </g></svg>
                                                </a>
                                            @else
                                                <a href="{{ route($route.'-activate',$value->id) }}" type="button" wire:wire:navigate  class="btn btn-outline-warning d-flex justify-content-center items-center">
                                                    <svg viewBox="0 0 24 24"  width="20px" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M4 12.6111L8.92308 17.5L20 6.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                                                </a>
                                            @endif
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

        <div class="modal fade" id="gradesVersionTwoModal" wire:ignore.self data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <form wire:submit.prevent="add_school_work('gradesModal')" class="w-100">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="gradesModalTitle">Subject Grades</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" id="gradesModalclose" aria-label="Close"></button>
                        </div>
                        <div class="modal-body row">
                            <div class="col-md-12 mb-3">
                                <label for="department_id" class="form-label">Curriculum</label>
                                <select name="curriculum_id" id="curriculum_id" wire:model.live="curriculum_id" class="form-select">  
                                    <option value="">Select Curriculum</option>
                                    @foreach ($curriculums as $key => $value )
                                        <option value="{{ $value->id }}" >{{ $value->year_start.' - '.$value->year_end }}</option>
                                    @endforeach
                                </select>
                                @error('detail.department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror  
                            </div>
                            <div class="col-12 overflow-scroll" style="max-height:700px;">
                                <div class="row">
                                    @foreach($year_levels as $key => $value)
                                        <div class="row d-flex px-2 justify-content-center border border-black text-black fs-4 py-2 fw-bold">{{ $value->year_level }}</div>
                                        <div class="row mb-3 px-2">
                                            @foreach($semesters as $s_key => $s_value)
                                                @php 
                                                $grade_average = 0.0;
                                                $grade_count = 0.0;
                                                @endphp
                                                <div class="col-md-6">
                                                    <div class="row d-flex justify-content-center border border-black text-black fs-5 py-2 fw-bold">{{ $s_value->semester }}</div>
                                                    <div class="row">
                                                        <table class="table table-striped table-bordered" >
                                                            <thead style="background:#952323;color:white;">
                                                                <tr class="align-middle">
                                                                    <th scope="col" class="">Grade</th>
                                                                    <th scope="col" class="">Code</th>
                                                                    <th scope="col"colspan="3" class="">Description</th>
                                                                    <th scope="col" class="">LEC</th>
                                                                    <th scope="col" class="">LAB</th>
                                                                    <th scope="col" class="">PREREQ</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @php 
                                                                    $has_row = false;
                                                                @endphp
                                                                @foreach($curriculum as $tb_key =>$tb_value)
                                                                    @if($value->id == $tb_value->year_level_id && $s_value->id ==  $tb_value->semester_id)
                                                                        @php 
                                                                        $has_row = true;
                                                                        @endphp
                                                                        <tr class="align-middle">
                                                                            <td class="">
                                                                                @foreach($grades_v2 as $g_key =>$g_value)
                                                                                    @if($g_value->subject_id == $tb_value->subject_id)
                                                                                        @php
                                                                                            $lab = floatval($g_value->lab_calculated_grade);
                                                                                            $lec = floatval($g_value->lec_calculated_grade);
                                                                                            $weight = 0;

                                                                                            if ($lab > 0) $weight += 1;
                                                                                            if ($lec > 0) $weight += 1;

                                                                                            $grade = $weight > 0
                                                                                                ? (($lab * 0.5) + ($lec * 0.5)) 
                                                                                                : NULL;
                                                                                        @endphp
                                                                                        @if($grade)
                                                                                            {{ number_format($grade, 2, '.', '') }}
                                                                                            @php
                                                                                                $grade_count++;
                                                                                                $grade_average+=$grade;
                                                                                            @endphp
                                                                                        @else
                                                                                        
                                                                                        @endif
                                                                                        -
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
                                                                                        @endif
                                                                                    @endif
                                                                                @endforeach
                                                                            </td>
                                                                            <td class="">{{$tb_value->subject_code}}</td>
                                                                            <td class="" colspan="3">{{$tb_value->description}}</td>
                                                                            <td class="">{{$tb_value->lecture_unit}}</td>
                                                                            <td class="">{{$tb_value->laboratory_unit}}</td>
                                                                            <td class="">
                                                                                @if($tb_value->prerequisite_subject_id)
                                                                                    @php 
                                                                                        $prerequisites = DB::table('subjects')
                                                                                            ->whereIn('id', json_decode($tb_value->prerequisite_subject_id))
                                                                                            ->get()
                                                                                            ->toArray();
                                                                                        foreach($prerequisites as $v_key => $v_value){
                                                                                            echo $v_value->subject_id.' - '.$v_value->subject_code; 
                                                                                            echo '<br>';  
                                                                                            
                                                                                        }
                                                                                    @endphp
                                                                                @else 
                                                                                    N/A
                                                                                @endif    
                                                                            </td>
                                                                        </tr>
                                                                    @endif
                                                                @endforeach
                                                                @if(!$has_row)
                                                                    <tr class="align-middle">
                                                                        <td colspan="42">
                                                                            <div class="alert alert-danger d-flex justify-content-center">No records found!</div>
                                                                        </td>
                                                                    </tr>
                                                                @else
                                                                    <tr>
                                                                        <td colspan="43">
                                                                            <div class="d-flex justify-content-start">
                                                                                Average: 
                                                                                @if($grade_average>0)
                                                                                    {{ number_format($grade_average/$grade_count, 2, '.', '') }}
                                                                                @endif
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="modal fade" id="gradesModal" wire:ignore.self data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <form wire:submit.prevent="add_school_work('gradesModal')" class="w-100">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="gradesModalTitle">Grades</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" id="gradesModalclose" aria-label="Close"></button>
                        </div>
                        <div class="modal-body row">
                            <div class="col-12">
                                <table class="table table-striped table-bordered text-center align-middle position-relative" >
                                    <thead style="background:#952323;color:white;">
                                        <tr class="">
                                            <th scope="col" class="px-4">#</th>
                                            <th scope="col" class="px-4 text-start">Subject</th>
                                            <th scope="col" class="px-4 ">School Year</th>
                                            <th scope="col" class="px-4 ">Semester</th>
                                            <th scope="col" class="text-center px-4 ">Grade</th> 
                                            <th scope="col" class="text-center px-4 ">Grade Equivalent</th> 
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($grades as $key =>$value)
                                            <tr class="">
                                                <th scope="row" class="px-4">{{ intval($key)+1 }}</th>
                                                <td class="px-4 text-start">
                                                     {{$value->subject_id}} -{{$value->subject_code.' '.($value->is_lec ? "Lecture" : "Laboratory
                                                     ")}}
                                                </td>
                                                <th scope="row" class="px-4">{{ $value->school_year}}</th>
                                                <th scope="row" class="px-4">{{ $value->semester}}</th>
                                                <td class="px-4">
                                                    @if(1)
                                                        @if($value->calculated_grade)
                                                            {{ number_format($value->calculated_grade, 2, '.', '') }}
                                                        @else 
                                                            {{ $value->other }}
                                                        @endif
                                                    @endif
                                                </td>
                                                <td class="px-4">
                                                    @if($value->calculated_grade)
                                                        @php
                                                            $set = false;
                                                        @endphp
                                                        @foreach ($equivalent_grade as $eg_key =>$eg_value)
                                                            @if(floatval($value->calculated_grade) >= floatval($eg_value->minimum) && floatval($value->calculated_grade) < floatval($eg_value->maximum + 1))
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
                                                        {{ $value->other }}
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
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

         <div class="modal fade" id="changePasswordModal" wire:ignore.self data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <form wire:submit.prevent="save_password('changePasswordModal')" class="w-100">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="changePasswordModalTitle">Change password</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" id="changePasswordModalclose" aria-label="Close"></button>
                        </div>
                        <div class="modal-body row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password </label>
                                <div class="input-group d-flex">
                                    <input 
                                        type="password" 
                                        wire:model.defer="detail.new_password"
                                        id="password" 
                                        class="form-control" 
                                        placeholder="Password"
                                    >
                                    <button 
                                        class="" 
                                        type="button" 
                                        id="togglePassword"
                                    >
                                    <svg viewBox="0 0 24 24" width="20px" class="m-2" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M15.0007 12C15.0007 13.6569 13.6576 15 12.0007 15C10.3439 15 9.00073 13.6569 9.00073 12C9.00073 10.3431 10.3439 9 12.0007 9C13.6576 9 15.0007 10.3431 15.0007 12Z" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M12.0012 5C7.52354 5 3.73326 7.94288 2.45898 12C3.73324 16.0571 7.52354 19 12.0012 19C16.4788 19 20.2691 16.0571 21.5434 12C20.2691 7.94291 16.4788 5 12.0012 5Z" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                                    </button>
                                </div>
                                @error('detail.password') 
                                    <span class="text-danger">{{ $message }}</span> 
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">Confirm password </label>
                                <div class="input-group d-flex">
                                    <input 
                                        type="password" 
                                        wire:model.defer="detail.confirm_password"
                                        id="confirmpassword" 
                                        class="form-control" 
                                        placeholder="Password"
                                    >
                                    <button 
                                        class="" 
                                        type="button" 
                                        id="confirmtogglePassword"
                                    >
                                    <svg viewBox="0 0 24 24" width="20px" class="m-2" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M15.0007 12C15.0007 13.6569 13.6576 15 12.0007 15C10.3439 15 9.00073 13.6569 9.00073 12C9.00073 10.3431 10.3439 9 12.0007 9C13.6576 9 15.0007 10.3431 15.0007 12Z" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M12.0012 5C7.52354 5 3.73326 7.94288 2.45898 12C3.73324 16.0571 7.52354 19 12.0012 19C16.4788 19 20.2691 16.0571 21.5434 12C20.2691 7.94291 16.4788 5 12.0012 5Z" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>
                                    </button>
                                </div>
                                @error('detail.confirm_password')
                                    <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-success" type="submit">Save</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="modal fade" id="importModal"  wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalTitle">Import Students</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" id="importModalclose" aria-label="Close"></button>
                    </div>
                    <form wire:submit.prevent="importSheet('importModal')" enctype="multipart/form-data">
                        <div class="modal-body row">
                            <div class="row">
                                <div class="row z-3 py-2">
                                    <div class="d-flex justify-content-end">
                                        <div class="row">
                                            <ul class="mx-5">
                                                <li class="p-2">
                                                    Ensure to populate column that is required, with "<span class="text-danger">(*)</span> ".
                                                </li>
                                                <li class="p-2">
                                                    Ensure that the Columns <span class="text-danger"> ("Work Sheet Reference" ;*) </span> , the " <span class="text-danger">;*</span>  " means that it is required.
                                                </li>
                                                <li class="p-2">
                                                    For columns that are selection base, you can view the reference in the other working sheets in the template. Please prefer to use the Code.
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mb-4">
                                    <label for="status-filter" class="mb-1">Import File <span class="text-danger">*</span></label>
                                    <input type="file" name="file" id="file" wire:model.live="excel_file_input" class="form-control">
                                    @error('excel_file_input')
                                        <span class="text-danger mt-1 d-block">{{ $message }}</span>
                                    @enderror
                                    <div wire:loading wire:target="excel_file_input" class="text-info mt-2">
                                        <i class="spinner-border spinner-border-sm"></i> Uploading...
                                    </div>
                                </div>
                                <div class="col-12 mb-4">
                                    @if($import['total_valid_inserts'] < $import['total_inserts'])
                                        <div class="row">
                                            <p class="text-danger">
                                                There are {{$import['total_inserts'] - $import['total_valid_inserts']}} rows  have encountered validation errors, do you want to download and fix the errors?
                                            </p>
                                            <p>
                                                The errors are marked as <span class="text-warning">comments</span> in the excel cells once you download it.
                                            </p>
                                            @if($import['total_valid_inserts'] > 0)
                                                <p>
                                                    There are total of {{$import['total_valid_inserts']}} valid rows, if you click import it will only insert the valid rows.
                                                </p>
                                            @endif
                                        </div>
                                        <div class="row">
                                            <div>
                                                <button type="button" class="btn btn-primary" wire:click="downloadErrorSheet()">Download</button>
                                            </div>
                                        </div>
                                    @elseif($import['total_inserts'] > 0 && $import['total_valid_inserts'] == $import['total_inserts'])
                                        <p>
                                            There are total of {{$import['total_valid_inserts']}} valid rows.
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" id="add-button" 
                                @if( $import['total_valid_inserts'] <= 0 ) 
                                    disabled 
                                @endif
                                >Import
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInput = document.getElementById('password');
            const toggleButton = document.getElementById('togglePassword');
            const confirmpasswordInput = document.getElementById('confirmpassword');
            const confirmtoggleButton = document.getElementById('confirmtogglePassword');

            const hideText = `
                <svg viewBox="0 0 24 24" width="20px" class="m-2" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M2.99902 3L20.999 21M9.8433 9.91364C9.32066 10.4536 8.99902 11.1892 8.99902 12C8.99902 13.6569 10.3422 15 11.999 15C12.8215 15 13.5667 14.669 14.1086 14.133M6.49902 6.64715C4.59972 7.90034 3.15305 9.78394 2.45703 12C3.73128 16.0571 7.52159 19 11.9992 19C13.9881 19 15.8414 18.4194 17.3988 17.4184M10.999 5.04939C11.328 5.01673 11.6617 5 11.9992 5C16.4769 5 20.2672 7.94291 21.5414 12C21.2607 12.894 20.8577 13.7338 20.3522 14.5" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>`;

            const eyeIcon = `<svg viewBox="0 0 24 24" width="20px" class="m-2" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M15.0007 12C15.0007 13.6569 13.6576 15 12.0007 15C10.3439 15 9.00073 13.6569 9.00073 12C9.00073 10.3431 10.3439 9 12.0007 9C13.6576 9 15.0007 10.3431 15.0007 12Z" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> <path d="M12.0012 5C7.52354 5 3.73326 7.94288 2.45898 12C3.73324 16.0571 7.52354 19 12.0012 19C16.4788 19 20.2691 16.0571 21.5434 12C20.2691 7.94291 16.4788 5 12.0012 5Z" stroke="#000000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path> </g></svg>`;

            toggleButton.innerHTML = eyeIcon;

            toggleButton.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                // Toggle innerHTML
                toggleButton.innerHTML = (type === 'password') ? eyeIcon : hideText;
            });


            confirmtoggleButton.innerHTML = eyeIcon;

            confirmtoggleButton.addEventListener('click', function () {
                const type = confirmpasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                confirmpasswordInput.setAttribute('type', type);

                // Toggle innerHTML
                confirmtoggleButton.innerHTML = (type === 'password') ? eyeIcon : hideText;
            });
        });
    </script>
</div>
