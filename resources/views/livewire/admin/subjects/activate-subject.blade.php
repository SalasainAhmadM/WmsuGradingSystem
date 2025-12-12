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
                <label for="prerequisite_subject_id" class="form-label">Prerequisite Subject</label>
                <div class="col-md-12 mb-3" wire:ignore>
                    <select name="prerequisite_subject_id" disabled id="prerequisite_subject_id" wire:model.live="detail.prerequisite_subject_id"  multiple="multiple" wire:ignore class="form-select position-relative z-10 select2 @error('detail.prerequisite_subject_id') is-invalid @enderror">  
                        @foreach ($subjects as $key => $value )
                            <option value="{{ $value->id }}" >{{ $value->subject_id.' - '.$value->subject_code }}</option>
                        @endforeach
                    </select>
                </div>
                @error('detail.prerequisite_subject_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror  
                <div class="col-md-6 mb-3">
                    <label for="subject_id" class="form-label">Subject ID</label>
                    <input type="subject_id" id="subject_id" disabled wire:model.defer="detail.subject_id" placeholder="Subject ID" class="form-control @error('detail.subject_id') is-invalid @enderror">
                    @error('detail.subject_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="subject_code" class="form-label">Subject Code</label>
                    <input type="text" id="subject_code" disabled wire:model.defer="detail.subject_code" placeholder="Subject Code" class="form-control @error('detail.subject_code') is-invalid @enderror">
                    @error('detail.subject_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="college_id" class="form-label">College</label>
                    <select name="college_id" id="college_id" disabled wire:model="detail.college_id" class="form-select @error('detail.college_id') is-invalid @enderror">  
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
                    <select name="department_id" id="department_id" disabled wire:model="detail.department_id" class="form-select @error('detail.department_id') is-invalid @enderror">  
                        <option value="">Select Department</option>
                        @foreach ($departments as $key => $value )
                            <option value="{{ $value->id }}" >{{ $value->code.' - '.$value->name }}</option>
                        @endforeach
                    </select>
                    @error('detail.department_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror  
                </div>
            </div>          
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea type="text" rows="3" id="description" disabled wire:model.defer="detail.description" placeholder="Description" class="form-control @error('detail.description') is-invalid @enderror">
                    </textarea>
                    @error('detail.description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="lecture_unit" class="form-label">Lecture Units</label>
                    <input type="number" id="lecture_unit" min="1" disabled wire:model.defer="detail.lecture_unit" placeholder="Lecture Units"  class="form-control rounded-md @error('detail.lecture_unit') is-invalid @enderror">
                    @error('detail.lecture_unit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="laboratory_unit" class="form-label">Laboratory Units</label>
                    <input type="number" id="laboratory_unit" min="0" disabled wire:model.defer="detail.laboratory_unit"  placeholder="Laboratory Units" class="form-control @error('detail.laboratory_unit') is-invalid @enderror">
                    @error('detail.laboratory_unit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
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
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Select2 Access Selection
            $('#prerequisite_subject_id').select2();
            
            // Make sure Livewire receives the selected value
            $('#prerequisite_subject_id').on('change', function(e) {
                var data = $(this).val(); // Get selected value
                @this.set('detail.prerequisite_subject_id', data);
            });

            // $('#prerequisite_subject_id').trigger('change');

            });
    
    </script>
</div>


