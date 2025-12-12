<div>
    <div class="container-fluid d-flex justify-content-center shadow text-dark">
        <span class="fs-2 fw-bold h1 m-0 brand-color"> Activate {{ $title }}</span>
    </div>
    <div class="container-fluid">
        <div class="table-header">
            <livewire:admin.BreadCrumb.BreadCrumb/>
        </div>
        <div class="d-flex justify-content-between my-2 gap-2 row">
        </div>

        <form wire:submit.prevent="save()">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" disabled wire:model.defer="detail.email" placeholder="Email" class="form-control @error('detail.email') is-invalid @enderror">
                    @error('detail.email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3 d-flex align-items-end">
                    <div class="form-check mt-4 ">
                        <input type="checkbox" disabled id="is_admin" wire:model.defer="detail.is_admin" class="form-check-input @error('detail.is_admin') is-invalid @enderror">
                        <label for="is_admin" class="form-check-label">Is Admin?</label>
                        @error('detail.is_admin')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="code" class="form-label">ID</label>
                    <input type="text" id="code" disabled wire:model.defer="detail.code" placeholder="ID" class="form-control @error('detail.code') is-invalid @enderror">
                    @error('detail.code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="hours_per_week" class="form-label">No. of hours per week</label>
                    <input type="number" min="1" disabled id="hours_per_week" wire:model.defer="detail.hours_per_week" placeholder="First name" class="form-control @error('detail.hours_per_week') is-invalid @enderror">
                    @error('detail.hours_per_week')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="college_id" class="form-label">College</label>
                    <select name="college_id"  disabled id="college_id" wire:model.live="detail.college_id" class="form-select @error('detail.college_id') is-invalid @enderror">  
                        <option value="">Select College</option>
                        @foreach ($colleges as $key => $value )
                            <option value="{{ $value->id }}" >{{ $value->code.' - '.$value->name }}</option>
                        @endforeach
                    </select>
                    @error('detail.college_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror  
                </div>
                <div class="col-md-6 mb-3">
                    <label for="department_id" class="form-label">Department</label>
                    <select name="department_id" disabled  id="department_id" @if(intval($detail['college_id']) == 0){{ 'disabled' }} @endif wire:model="detail.department_id" class="form-select @error('detail.department_id') is-invalid @enderror">  
                        <option value="">Select Department</option>
                        @foreach ($departments as $key => $value )
                            @if(intval($detail['college_id']) == $value->college_id)
                                <option value="{{ $value->id }}" >{{ $value->code.' - '.$value->name }}</option>
                            @endif
                        @endforeach
                    </select>
                    @error('detail.department_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror  
                </div>
                <div class="col-md-6 mb-3">
                    <label for="first_name" class="form-label">First name</label>
                    <input type="text" id="first_name" disabled wire:model.defer="detail.first_name" placeholder="First name" class="form-control @error('detail.first_name') is-invalid @enderror">
                    @error('detail.first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="middle_name" class="form-label">Middle name</label>
                    <input type="text" id="middle_name" disabled wire:model.defer="detail.middle_name" placeholder="Middle name" class="form-control @error('detail.middle_name') is-invalid @enderror">
                    @error('detail.middle_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="last_name" class="form-label">Last name</label>
                    <input type="text" id="last_name"  disabled wire:model.defer="detail.last_name" placeholder="Last name" class="form-control @error('detail.last_name') is-invalid @enderror">
                    @error('detail.last_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="suffix" class="form-label">Suffix </label>
                    <input type="text" id="suffix" disabled wire:model.defer="detail.suffix" placeholder="Suffix" class="form-control @error('detail.suffix') is-invalid @enderror">
                    @error('detail.suffix')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="faculty_type_id" class="form-label">Faculty Type</label>
                    <select name="faculty_type_id" disabled id="faculty_type_id" wire:model.live="detail.faculty_type_id" class="form-select @error('detail.faculty_type_id') is-invalid @enderror">  
                        <option value="">Select Faculty Type</option>
                        @foreach ($faculty_types as $key => $value )
                            <option value="{{ $value->id }}" >{{ $value->code.' - '.$value->name }}</option>
                        @endforeach
                    </select>
                    @error('detail.faculty_type_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror  
                </div>
                <div class="col-md-6 mb-3">
                    <label for="designation_id" class="form-label">Designation</label>
                    <select name="designation_id" disabled id="designation_id" wire:model="detail.designation_id" class="form-select @error('detail.department_id') is-invalid @enderror">  
                        <option value="">Select Designation</option>
                        @foreach ($designations as $key => $value )
                            <option value="{{ $value->id }}" >{{ $value->code.' - '.$value->name }}</option>
                        @endforeach
                    </select>
                    @error('detail.designation_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror  
                </div>
                <div class="col-md-6 mb-3">
                    <label for="academic_rank_id" class="form-label">Academic Rank</label>
                    <select name="academic_rank_id" disabled id="academic_rank_id" wire:model.live="detail.academic_rank_id" class="form-select @error('detail.academic_rank_id') is-invalid @enderror">  
                        <option value="">Select Academic Rank</option>
                        @foreach ($academic_ranks as $key => $value )
                            <option value="{{ $value->id }}" >{{ $value->code.' - '.$value->name }}</option>
                        @endforeach
                    </select>
                    @error('detail.academic_rank_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror  
                </div>
                 <div class="col-6 mb-3 d-flex align-items-end">
                    <div>
                        <div class="form-check">
                            <input class="form-check-input" disabled type="radio" name="radioDefault" id="radioDefault1" value="With Release Time" wire:model="detail.release_time">
                            <label class="form-check-label" for="radioDefault1">
                                With Release Time
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" disabled type="radio" name="radioDefault" id="radioDefault2" value="Without Release Time" wire:model="detail.release_time">
                            <label class="form-check-label" for="radioDefault2">
                                Without Release Time
                            </label>
                        </div>
                    </div>
                </div>
            </div>  
            <div class="row">
                <p class="text-warning d-flex justify-content-center">
                    Are you sure you want to activate this ?
                </p>
            </div>
            <div class="row my-4">
                <div class="col-12 d-flex justify-content-center">
                    <div class="p-2">
                        <button class="btn btn-warning" type="submit">
                            Activate
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>


