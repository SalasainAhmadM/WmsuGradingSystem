<div>
    <div class="container-fluid d-flex justify-content-center shadow text-dark">
        <span class="fs-2 fw-bold h1 m-0 brand-color"> Add {{ $title }}</span>
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
                    <input type="email" id="email" wire:model.defer="detail.email" placeholder="Email" class="form-control @error('detail.email') is-invalid @enderror">
                    @error('detail.email')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3 d-flex align-items-end">
                    <div class="form-check mt-4 ">
                        <input type="checkbox" id="is_admin" wire:model.defer="detail.is_admin" class="form-check-input @error('detail.is_admin') is-invalid @enderror">
                        <label for="is_admin" class="form-check-label">Is Admin?</label>
                        @error('detail.is_admin')
                            <div class="text-danger d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="code" class="form-label">ID</label>
                    <input type="text" id="code" wire:model.defer="detail.code" placeholder="ID" class="form-control @error('detail.code') is-invalid @enderror">
                    @error('detail.code')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="hours_per_week" class="form-label">No. of hours per week</label>
                    <input type="number" min="1" id="hours_per_week" wire:model.defer="detail.hours_per_week" placeholder="Number of hours per week" class="form-control @error('detail.hours_per_week') is-invalid @enderror">
                    @error('detail.hours_per_week')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="college_id" class="form-label">College</label>
                    <select name="college_id" id="college_id" wire:model.live="detail.college_id" class="form-select @error('detail.college_id') is-invalid @enderror">  
                        <option value="">Select College</option>
                        @foreach ($colleges as $key => $value )
                            <option value="{{ $value->id }}" >{{ $value->code.' - '.$value->name }}</option>
                        @endforeach
                    </select>
                    @error('detail.college_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror  
                </div>
                <div class="col-md-6 mb-3">
                    <label for="department_id" class="form-label">Department</label>
                    <select name="department_id" id="department_id" @if(intval($detail['college_id']) == 0){{ 'disabled' }} @endif wire:model="detail.department_id" class="form-select @error('detail.department_id') is-invalid @enderror">  
                        <option value="">Select Department</option>
                        @foreach ($departments as $key => $value )
                            @if(intval($detail['college_id']) == $value->college_id)
                                <option value="{{ $value->id }}" >{{ $value->code.' - '.$value->name }}</option>
                            @endif
                        @endforeach
                    </select>
                    @error('detail.department_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror  
                </div>
                <div class="col-md-6 mb-3">
                    <label for="first_name" class="form-label">First name</label>
                    <input type="text" id="first_name" wire:model.defer="detail.first_name" placeholder="First name" class="form-control @error('detail.first_name') is-invalid @enderror">
                    @error('detail.first_name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="middle_name" class="form-label">Middle name</label>
                    <input type="text" id="middle_name" wire:model.defer="detail.middle_name" placeholder="Middle name" class="form-control @error('detail.middle_name') is-invalid @enderror">
                    @error('detail.middle_name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="last_name" class="form-label">Last name</label>
                    <input type="text" id="last_name" wire:model.defer="detail.last_name" placeholder="Last name" class="form-control @error('detail.last_name') is-invalid @enderror">
                    @error('detail.last_name')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="suffix" class="form-label">Suffix </label>
                    <input type="text" id="suffix" wire:model.defer="detail.suffix" placeholder="Suffix" class="form-control @error('detail.suffix') is-invalid @enderror">
                    @error('detail.suffix')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="faculty_type_id" class="form-label">Faculty Type</label>
                    <select name="faculty_type_id" id="faculty_type_id" wire:model.live="detail.faculty_type_id" class="form-select @error('detail.faculty_type_id') is-invalid @enderror">  
                        <option value="">Select Faculty Type</option>
                        @foreach ($faculty_types as $key => $value )
                            <option value="{{ $value->id }}" >{{ $value->code.' - '.$value->name }}</option>
                        @endforeach
                    </select>
                    @error('detail.faculty_type_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror  
                </div>
                <div class="col-md-6 mb-3">
                    <label for="designation_id" class="form-label">Designation</label>
                    <select name="designation_id" id="designation_id" wire:model="detail.designation_id" class="form-select @error('detail.designation_id') is-invalid @enderror">  
                        <option value="">Select Designation</option>
                        @foreach ($designations as $key => $value )
                            <option value="{{ $value->id }}" >{{ $value->code.' - '.$value->name }}</option>
                        @endforeach
                    </select>
                    @error('detail.designation_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror  
                </div>
                <div class="col-md-6 mb-3">
                    <label for="academic_rank_id" class="form-label">Academic Rank</label>
                    <select name="academic_rank_id" id="academic_rank_id" wire:model.live="detail.academic_rank_id" class="form-select @error('detail.academic_rank_id') is-invalid @enderror">  
                        <option value="">Select Academic Rank</option>
                        @foreach ($academic_ranks as $key => $value )
                            <option value="{{ $value->id }}" >{{ $value->code.' - '.$value->name }}</option>
                        @endforeach
                    </select>
                    @error('detail.academic_rank_id')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror  
                </div>
                <div class="col-6 mb-3 d-flex align-items-end">
                    <div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="radioDefault" id="radioDefault1" value="With Release Time" wire:model="detail.release_time">
                            <label class="form-check-label" for="radioDefault1">
                                With Release Time
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="radioDefault" id="radioDefault2" value="Without Release Time" wire:model="detail.release_time">
                            <label class="form-check-label" for="radioDefault2">
                                Without Release Time
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Password </label>
                    <div class="input-group d-flex">
                        <input 
                            type="password" 
                            wire:model="detail.password" 
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
                        <div class="text-danger">{{ $message }}</div>
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
            <div class="row my-4">
                <div class="col-12 d-flex justify-content-center">
                    <div class="p-2">
                        <button class="btn btn-primary" type="submit">
                            Add
                        </button>
                    </div>
                </div>
            </div>
        </form>
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


