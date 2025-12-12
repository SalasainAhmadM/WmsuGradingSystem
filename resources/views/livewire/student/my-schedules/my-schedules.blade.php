<div>
    <div class="container-fluid position-relative shadow" style="min-height: 110px;">
        <!-- Centered Title -->
        <div class="d-flex justify-content-center align-items-center" style="height: 100%;">
            <span class="fs-2 fw-bold h1 m-0 brand-color"> {{ $title }}s</span>
        </div>

        <!-- Top Right Filters -->
        <div class="position-absolute top-0 end-0 p-2 d-flex flex-column gap-2">
            <select wire:model.live="detail.year_level_id" class="form-select">
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
        <div class="d-flex justify-content-between my-2 gap-2 row">
            <div class="col-4">
                <input type="search" wire:model.live="filters.search" name="" id="" placeholder="Search ... " class="form-control">
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
                            <th scope="col" class="px-4 ">Professor</th>
                            <th scope="col" class="px-4 ">School Year</th>
                            <th scope="col" class="px-4 ">Year Level</th>
                            <th scope="col" class="px-4 ">Semester</th>
                            <th scope="col" class="px-4 ">Room</th>
                            <th scope="col" class="px-4 ">Start period</th>
                            <th scope="col" class="px-4 ">End period</th>
                            <th scope="col" class="px-4 ">Type</th>
                            <th scope="col" class="px-4 ">Days</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($table_data as $key =>$value)
                            @if($value->subject_id)
                            <tr class="align-middle">
                                <th scope="row" class="px-4">{{($table_data->currentPage()-1)*$table_data->perPage()+$key+1 }}</th>
                                    <td class="px-4">
                                        <a href="{{route('student-subject-view',$value->subject_id)}}" target="_blank">
                                            {{$value->subject}}
                                        </a>
                                    </td>
                                    <td class="px-4">
                                        @if($value->faculty_fullname)
                                            <a href="{{route('student-faculty-view',$value->faculty_id)  }}" target="_blank">
                                                {{($value->faculty_fullname ? $value->faculty_fullname : "Unassigned")}}
                                            </a>
                                        @else
                                            Unassigned
                                        @endif
                                    </td>
                                    <th scope="row" class="px-4">{{ $value->school_year}}</th>
                                    <th class="px-4">{{$value->year_level}}</th>
                                    <th class="px-4">{{$value->semester}}</th>

                                    <td class="px-4">
                                        <a href="{{route('student-room-view',$value->room_id)  }}" target="_blank">
                                            {{$value->room_code}}
                                        </a>
                                    </td>
                                    <td class="px-4">{{$value->schedule_from}}</td>
                                    <td class="px-4">{{$value->schedule_to}}</td>
                                    <td class="px-4">
                                        {{ ($value->is_lec? "LECTURE": "LABORATORY") }}
                                    </td>
                                    <td class="px-4">{{implode(', ', json_decode($value->day, true))}}</td>
                                </tr>
                            @else
                            @endif#
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