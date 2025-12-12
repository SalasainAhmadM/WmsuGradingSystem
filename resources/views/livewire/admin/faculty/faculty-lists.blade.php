<div>
    <div class="container-fluid d-flex justify-content-center shadow">
        <span class="fs-2 fw-bold h1 m-0 brand-color">  {{ $title }}</span>
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
            </div>
            <div class="col">
                <div class="d-flex justify-content-end gap-2">
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
                            <th scope="col" class="px-4 ">Faculty ID</th>
                            <th scope="col" class="px-4 ">FullName</th>
                            <th scope="col" class="px-4 ">College</th>
                            <th scope="col" class="px-4 ">Department</th>
                            <th scope="col" class="px-4 ">Designations</th>
                            <th scope="col" class="px-4 ">Academic Rank</th>
                            <th scope="col" class="px-4 ">Faculty Type</th>
                            <th scope="col" class="px-4 ">Email</th>
                            <th scope="col" class="px-4 ">Is Active?</th>
                            <th scope="col" class="px-4 ">Is Admin?</th>
                            <th scope="col" class="text-center px-4 ">Actions</th> 
                        </tr>
                    </thead>
                    <tbody>
                         @forelse($table_data as $key =>$value)
                            <tr class="align-middle">
                                <th scope="row" class="px-4">{{($table_data->currentPage()-1)*$table_data->perPage()+$key+1 }}</th>
                                    <td class="px-4">{{$value->code}}</td>
                                    <td class="px-4">{{$value->fullname}}</td>
                                    <td class="px-4">
                                        <a href="/admin/colleges/view-{{ $value->college_id }}" target="_blank">
                                            {{ $value->college_code }}
                                        </a>
                                    </td>
                                    <td class="px-4">
                                        <a href="/admin/departments/view-{{ $value->department_id }}" target="_blank">
                                            {{ $value->department_code }}
                                        </a>
                                    </td>
                                    <td class="px-4">
                                        <a href="/admin/academic/designations/view-{{ $value->designation_id }}" target="_blank">
                                            {{ $value->designation_code }}
                                        </a>
                                    </td>
                                    <td class="px-4">
                                        <a href="/admin/academic/ranks/view-{{ $value->academic_rank_id }}" target="_blank">
                                            {{ $value->academic_rank_code }}
                                        </a>
                                    </td>
                                    <td class="px-4">
                                        <a href="/admin/academic/faculty-types/view-{{ $value->faculty_type_id }}" target="_blank">
                                            {{ $value->faculty_type_code }}
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
                                    <td class="px-4">
                                        @if($value->admin_type == 1)
                                            <span class="badge bg-success">Yes</span>
                                        @else
                                            <span class="badge bg-danger">No</span>
                                        @endif
                                    </td>
                                    <td class="px-4">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-outline-secondary" wire:click="change_password({{ $value->user_id }},'changePasswordModal')">
                                                <svg fill="currentColor" width="20px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" enable-background="new 0 0 24 24"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><path d="M17,9V7c0-2.8-2.2-5-5-5S7,4.2,7,7v2c-1.7,0-3,1.3-3,3v7c0,1.7,1.3,3,3,3h10c1.7,0,3-1.3,3-3v-7C20,10.3,18.7,9,17,9z M9,7c0-1.7,1.3-3,3-3s3,1.3,3,3v2H9V7z M13.1,15.5c0,0-0.1,0.1-0.1,0.1V17c0,0.6-0.4,1-1,1s-1-0.4-1-1v-1.4c-0.6-0.6-0.7-1.5-0.1-2.1c0.6-0.6,1.5-0.7,2.1-0.1C13.6,13.9,13.7,14.9,13.1,15.5z"></path></g></svg>
                                            </button>
                                            <a href="{{ route($route.'-view',$value->id) }}" type="button" wire:wire:navigate  class="btn btn-outline-secondary d-flex justify-content-center items-center">
                                                <svg viewBox="0 0 24 24"  width="20px" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill-rule="evenodd" clip-rule="evenodd" d="M12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9ZM11 12C11 11.4477 11.4477 11 12 11C12.5523 11 13 11.4477 13 12C13 12.5523 12.5523 13 12 13C11.4477 13 11 12.5523 11 12Z" fill="currentColor"></path> <path fill-rule="evenodd" clip-rule="evenodd" d="M21.83 11.2807C19.542 7.15186 15.8122 5 12 5C8.18777 5 4.45796 7.15186 2.17003 11.2807C1.94637 11.6844 1.94361 12.1821 2.16029 12.5876C4.41183 16.8013 8.1628 19 12 19C15.8372 19 19.5882 16.8013 21.8397 12.5876C22.0564 12.1821 22.0536 11.6844 21.83 11.2807ZM12 17C9.06097 17 6.04052 15.3724 4.09173 11.9487C6.06862 8.59614 9.07319 7 12 7C14.9268 7 17.9314 8.59614 19.9083 11.9487C17.9595 15.3724 14.939 17 12 17Z" fill="currentColor"></path> </g></svg>
                                            </a>
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
            <div class="row d-flex justify-content-end">
                {{ $table_data->links('pagination::bootstrap-5') }}
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
