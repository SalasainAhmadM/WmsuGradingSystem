<div>
    <div class="container-fluid position-relative shadow" style="min-height: 110px;">
        <!-- Centered Title -->
        <div class="d-flex justify-content-center align-items-center" style="height: 100%;">
            <span class="fs-2 fw-bold h1 m-0 brand-color">{{ $title }}s</span>
        </div>

        <!-- Top Right Filters -->
        <div class="position-absolute top-0 end-0 p-2 d-flex flex-column gap-2">
            <select wire:model.live="detail.year_level_id"  class="form-select">
                <option value="">Select Year Level</option>
                @foreach ($year_levels as $key => $value)
                    <option value="{{ $value->id }}">{{ $value->year_level }}</option>
                @endforeach
            </select>
            <select wire:model.live="detail.semester_id"  class="form-select">
                <option value="">Select Semester</option>
                @foreach ($semesters as $key => $value)
                    <option value="{{ $value->id }}">{{ $value->semester }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="container-fluid">
        <div class="table-header">
            <livewire:admin.BreadCrumb.BreadCrumb/>
        </div>
        @if(!$edit)
            <div class="d-flex justify-content-between my-2 gap-2 row">
                <div class="col-4 d-flex">
                    <input type="search" wire:model.live="filters.search" name="" id="" placeholder="Search ... " class="form-control">
                </div>
                <div class="d-flex col-7 justify-content-end gap-2">
                    <div class="form-check">
                        <input type="checkbox" id="is_admin" wire:model.live="is_table" class="form-check-input">
                        <label for="is_admin" class="form-check-label">Is Table?</label>
                    </div>
                    <a class="btn btn-primary" wire:click="view_prospectus()">
                        Details
                    </a>
                    @if($curriculum['is_editable'])
                        <a class="btn btn-primary" wire:click="add('AddSubjectModal')">
                            <svg  viewBox="0 0 20 20" width="20px" xmlns="http://www.w3.org/2000/svg" fill="none"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill="currentColor" fill-rule="evenodd" d="M9 17a1 1 0 102 0v-6h6a1 1 0 100-2h-6V3a1 1 0 10-2 0v6H3a1 1 0 000 2h6v6z"></path> </g></svg>
                        </a>
                    @endif
                </div>
            </div>
            @if($is_table)
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
                            <th scope="col" class="px-4 ">Prerequisite</th>
                            <th scope="col" class="px-4 ">Subject</th>
                            <th scope="col" class="px-4 ">Lecture Unit</th>
                            <th scope="col" class="px-4 ">Laboratory Unit</th>
                            <th scope="col" class="px-4 ">Year Level</th>
                            <th scope="col" class="px-4 ">Semester</th>
                            @if($curriculum['is_editable'])
                            <th scope="col" class="text-center px-4 ">Actions</th> 
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($table_data as $key =>$value)
                            <tr class="align-middle">
                                <th scope="row" class="px-4">{{($table_data->currentPage()-1)*$table_data->perPage()+$key+1 }}</th>
                                <td class="px-4">
                                        @if($value->prerequisite_subject_id)
                                            @php 
                                                $prerequisites = DB::table('subjects')
                                                    ->whereIn('id', json_decode($value->prerequisite_subject_id))
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
                                <td class="px-4">{{$value->subject_id.' - '.$value->subject_code}}</td>
                                <td class="px-4">{{$value->lecture_unit}}</td>
                                <td class="px-4">{{$value->laboratory_unit}}</td>
                                <td class="px-4">{{$value->year_level}}</td>
                                <td class="px-4">{{$value->semester}}</td>
                                @if($curriculum['is_editable'])
                                    <td class="px-4">   
                                        <div class="d-flex justify-content-center gap-2">
                                            <a wire:click="view({{ $value->id }},'deleteModal')" type="button" wire:navigate  class="btn btn-outline-danger d-flex justify-content-center items-center">
                                                <svg fill="currentColor" width="20px"  viewBox="0 0 64 64" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <rect id="Icons" x="-64" y="-64" width="1280" height="800" style="fill:none;"></rect> <g id="Icons1" serif:id="Icons"> <g id="Strike"> </g> <g id="H1"> </g> <g id="H2"> </g> <g id="H3"> </g> <g id="list-ul"> </g> <g id="hamburger-1"> </g> <g id="hamburger-2"> </g> <g id="list-ol"> </g> <g id="list-task"> </g> <g id="trash"> <path d="M19.186,16.493l0,-1.992c0.043,-3.346 2.865,-6.296 6.277,-6.427c3.072,-0.04 10.144,-0.04 13.216,0c3.346,0.129 6.233,3.012 6.277,6.427l0,1.992l9.106,0l0,4l-4.442,0l0,29.11c-0.043,3.348 -2.865,6.296 -6.278,6.428c-7.462,0.095 -14.926,0.002 -22.39,0.002c-3.396,-0.044 -6.385,-2.96 -6.429,-6.43l0,-29.11l-4.443,0l0,-4l9.106,0Zm26.434,4l-27.099,0c-0.014,9.72 -0.122,19.441 0.002,29.16c0.049,1.25 1.125,2.33 2.379,2.379c7.446,0.095 14.893,0.095 22.338,0c1.273,-0.049 2.363,-1.163 2.38,-2.455l0,-29.084Zm-4.701,-4c-0.014,-0.83 0,-1.973 0,-1.973c0,0 -0.059,-2.418 -2.343,-2.447c-3.003,-0.039 -10.007,-0.039 -13.01,0c-1.273,0.049 -2.363,1.162 -2.38,2.454l0,1.966l17.733,0Z" style="fill-rule:nonzero;"></path> <rect x="22.58" y="28.099" width="3" height="16.327" style="fill-rule:nonzero;"></rect> <rect x="30.571" y="28.099" width="3" height="16.327" style="fill-rule:nonzero;"></rect> <rect x="38.58" y="28.099" width="3" height="16.327" style="fill-rule:nonzero;"></rect> </g> <g id="vertical-menu"> </g> <g id="horizontal-menu"> </g> <g id="sidebar-2"> </g> <g id="Pen"> </g> <g id="Pen1" serif:id="Pen"> </g> <g id="clock"> </g> <g id="external-link"> </g> <g id="hr"> </g> <g id="info"> </g> <g id="warning"> </g> <g id="plus-circle"> </g> <g id="minus-circle"> </g> <g id="vue"> </g> <g id="cog"> </g> <g id="logo"> </g> <g id="radio-check"> </g> <g id="eye-slash"> </g> <g id="eye"> </g> <g id="toggle-off"> </g> <g id="shredder"> </g> <g id="spinner--loading--dots-" serif:id="spinner [loading, dots]"> </g> <g id="react"> </g> <g id="check-selected"> </g> <g id="turn-off"> </g> <g id="code-block"> </g> <g id="user"> </g> <g id="coffee-bean"> </g> <g id="coffee-beans"> <g id="coffee-bean1" serif:id="coffee-bean"> </g> </g> <g id="coffee-bean-filled"> </g> <g id="coffee-beans-filled"> <g id="coffee-bean2" serif:id="coffee-bean"> </g> </g> <g id="clipboard"> </g> <g id="clipboard-paste"> </g> <g id="clipboard-copy"> </g> <g id="Layer1"> </g> </g> </g></svg>
                                            </a>
                                        </div>
                                    </td>
                                @endif
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
                <div class="row d-flex justify-content-end">
                    {{ $table_data->links('pagination::bootstrap-5') }}
                </div>
            @else
            <div class="row d-flex justify-content-center">
                @foreach($year_levels as $key => $value)
                    <div class="row d-flex px-2 justify-content-center border border-black text-black fs-4 py-2 fw-bold">{{ $value->year_level }}</div>
                    <div class="row mb-3 px-2">
                        @foreach($semesters as $s_key => $s_value)
                            <div class="col-md-6">
                                <div class="row d-flex justify-content-center border border-black text-black fs-5 py-2 fw-bold">{{ $s_value->semester }}</div>
                                <div class="row">
                                    <table class="table table-striped table-bordered" >
                                        <thead style="background:#952323;color:white;">
                                            <tr class="align-middle">
                                                <th scope="col" class="">Code</th>
                                                <th scope="col"colspan="3" class="">Description</th>
                                                <th scope="col" class="">LEC</th>
                                                <th scope="col" class="">LAB</th>
                                                <th scope="col" class="">PREREQ</th>
                                                @if($curriculum['is_editable'])
                                                    <th scope="col" class="text-center px-4 ">Actions</th> 
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php 
                                                $has_row = false;
                                            @endphp
                                            @foreach($table_data as $tb_key =>$tb_value)
                                                @if($value->id == $tb_value->year_level_id && $s_value->id ==  $tb_value->semester_id)
                                                    @php 
                                                    $has_row = true;
                                                    @endphp
                                                    <tr class="align-middle">
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
                                                        @if($curriculum['is_editable'])
                                                            <td class="px-4">   
                                                                <div class="d-flex justify-content-center gap-2">
                                                                    <a wire:click="view({{ $tb_value->id }},'deleteModal')" type="button" wire:navigate  class="btn btn-outline-danger d-flex justify-content-center items-center">
                                                                        <svg fill="currentColor" width="20px"  viewBox="0 0 64 64" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:2;"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <rect id="Icons" x="-64" y="-64" width="1280" height="800" style="fill:none;"></rect> <g id="Icons1" serif:id="Icons"> <g id="Strike"> </g> <g id="H1"> </g> <g id="H2"> </g> <g id="H3"> </g> <g id="list-ul"> </g> <g id="hamburger-1"> </g> <g id="hamburger-2"> </g> <g id="list-ol"> </g> <g id="list-task"> </g> <g id="trash"> <path d="M19.186,16.493l0,-1.992c0.043,-3.346 2.865,-6.296 6.277,-6.427c3.072,-0.04 10.144,-0.04 13.216,0c3.346,0.129 6.233,3.012 6.277,6.427l0,1.992l9.106,0l0,4l-4.442,0l0,29.11c-0.043,3.348 -2.865,6.296 -6.278,6.428c-7.462,0.095 -14.926,0.002 -22.39,0.002c-3.396,-0.044 -6.385,-2.96 -6.429,-6.43l0,-29.11l-4.443,0l0,-4l9.106,0Zm26.434,4l-27.099,0c-0.014,9.72 -0.122,19.441 0.002,29.16c0.049,1.25 1.125,2.33 2.379,2.379c7.446,0.095 14.893,0.095 22.338,0c1.273,-0.049 2.363,-1.163 2.38,-2.455l0,-29.084Zm-4.701,-4c-0.014,-0.83 0,-1.973 0,-1.973c0,0 -0.059,-2.418 -2.343,-2.447c-3.003,-0.039 -10.007,-0.039 -13.01,0c-1.273,0.049 -2.363,1.162 -2.38,2.454l0,1.966l17.733,0Z" style="fill-rule:nonzero;"></path> <rect x="22.58" y="28.099" width="3" height="16.327" style="fill-rule:nonzero;"></rect> <rect x="30.571" y="28.099" width="3" height="16.327" style="fill-rule:nonzero;"></rect> <rect x="38.58" y="28.099" width="3" height="16.327" style="fill-rule:nonzero;"></rect> </g> <g id="vertical-menu"> </g> <g id="horizontal-menu"> </g> <g id="sidebar-2"> </g> <g id="Pen"> </g> <g id="Pen1" serif:id="Pen"> </g> <g id="clock"> </g> <g id="external-link"> </g> <g id="hr"> </g> <g id="info"> </g> <g id="warning"> </g> <g id="plus-circle"> </g> <g id="minus-circle"> </g> <g id="vue"> </g> <g id="cog"> </g> <g id="logo"> </g> <g id="radio-check"> </g> <g id="eye-slash"> </g> <g id="eye"> </g> <g id="toggle-off"> </g> <g id="shredder"> </g> <g id="spinner--loading--dots-" serif:id="spinner [loading, dots]"> </g> <g id="react"> </g> <g id="check-selected"> </g> <g id="turn-off"> </g> <g id="code-block"> </g> <g id="user"> </g> <g id="coffee-bean"> </g> <g id="coffee-beans"> <g id="coffee-bean1" serif:id="coffee-bean"> </g> </g> <g id="coffee-bean-filled"> </g> <g id="coffee-beans-filled"> <g id="coffee-bean2" serif:id="coffee-bean"> </g> </g> <g id="clipboard"> </g> <g id="clipboard-paste"> </g> <g id="clipboard-copy"> </g> <g id="Layer1"> </g> </g> </g></svg>
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        @endif
                                                    </tr>
                                                @endif
                                            @endforeach
                                            @if(!$has_row)
                                                <tr class="align-middle">
                                                    <td colspan="42">
                                                        <div class="alert alert-danger d-flex justify-content-center">No records found!</div>
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
            @endif
        @else
            <form wire:submit.prevent="save_prospectus()">
                <div class="row p-5">
                    <div class="col-12 mb-3">
                        <label for="schedule_id" class="form-label">Prospectus</label>
                        <textarea name="" id="" cols="6" rows="10" class="form-control" @if(!$curriculum['is_editable'] ) disabled @endif wire:model="curriculum.prospectus"></textarea>
                        @error('curriculum.prospectus')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror  
                    </div>
                    @if($curriculum['is_editable'])
                        @if(intval($curriculum['id']))
                        <div class="col-12 mb-3">
                            <input type="checkbox" name="" id="" wire:model="permanent">
                            Mark as permanent ?
                        </div>
                        <div class="col-12">
                            <p class="text-danger">
                                Warning marking this as permanent is irreversible!
                            </p>
                        </div>
                        @endif
                        <div class="col-12 d-flex justify-content-center mb-3">
                            <button class="btn-success btn">
                                Save
                            </button>
                        </div>
                    @endif
                </div>
            </form>
        @endif
    </div>

    <div class="modal fade" id="AddSubjectModal" wire:ignore.self data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable d-flex justify-content-center">
            <form wire:submit.prevent="addSubject('AddSubjectModal')">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="AddSubjectModalLabel">Add Subject</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" id="AddSubjectModalclose" aria-label="Close"></button>
                    </div>
                    <div class="modal-body row">
                        <div class="col-md-12 mb-3">
                            <label for="subject_id" class="form-label">Select Subject</label>
                            <select name="subject_id" id="subject_id" wire:model.defer="detail.subject_id" class="form-select select2 @error('detail.subject_id') is-invalid @enderror">  
                                <option value="">Select Subject</option>
                                @foreach ($subjects as $key => $value )
                                     <option value="{{ $value->id }}" >{{ $value->subject_code.' - '.$value->description.' '}}</option>
                                @endforeach
                            </select>
                            @error('detail.subject_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror  
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="year_level_id" class="form-label">Year Level</label>
                            <select name="year_level_id" id="year_level_id" wire:model.defer="detail.year_level_id" class="form-select select2 @error('detail.year_level_id') is-invalid @enderror">  
                                <option value="">Select Year Level</option>
                                @foreach ($year_levels as $key => $value )
                                     <option value="{{ $value->id }}" >{{ $value->year_level}}</option>
                                @endforeach
                            </select>
                            @error('detail.year_level_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror  
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="semester_id" class="form-label">Semester</label>
                            <select name="semester_id" id="semester_id" wire:model.defer="detail.semester_id" class="form-select select2 @error('detail.semester_id') is-invalid @enderror">  
                                <option value="">Select Semester</option>
                                @foreach ($semesters as $key => $value )
                                    <option value="{{ $value->id }}" >{{ $value->semester}}</option>
                                @endforeach
                            </select>
                            @error('detail.year_level_id')
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
                <form wire:submit.prevent="saveDelete({{ $detail['id'] }},'deleteModal')">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="deleteschedulingModalTitle">Delete Subject</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" id="deleteModalclose" aria-label="Close"></button>
                        </div>
                        <div class="modal-body row">
                            <p class="text-danger">
                                Are you sure you want to delete this?
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

</div>
