<div>
    <div class="container-fluid d-flex justify-content-center shadow">
        <span class="fs-2 fw-bold h1 m-0 brand-color">  {{ $title }}s</span>
    </div>
    <div class="container-fluid">
        <div class="table-header">
            <livewire:admin.BreadCrumb.BreadCrumb/>
        </div>
        <div class="d-flex justify-content-between my-2 gap-2 row">
            <div class="col-4">
                <input type="search" wire:model.live="filters.search" name="" id="" placeholder="Search ... " class="form-control">
            </div>
            <div class="d-flex col-7 justify-content-end gap-2">
                <a class="btn btn-primary" wire:wire:navigate href="{{ route('enrolled-student-add') }}">
                    <svg  viewBox="0 0 20 20" width="20px" xmlns="http://www.w3.org/2000/svg" fill="none"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill="currentColor" fill-rule="evenodd" d="M9 17a1 1 0 102 0v-6h6a1 1 0 100-2h-6V3a1 1 0 10-2 0v6H3a1 1 0 000 2h6v6z"></path> </g></svg>
                </a>
            </div>
        </div>
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
                    <th scope="col" class="px-4 ">Code</th>
                    <th scope="col" class="px-4 ">College</th>
                    <th scope="col" class="px-4 ">Departments</th>
                    <th scope="col" class="text-center px-4 ">Actions</th> 
                </tr>
            </thead>
            <tbody>
                 @forelse($table_data as $key =>$value)
                    <tr class="align-middle">
                        <th scope="row" class="px-4">{{($table_data->currentPage()-1)*$table_data->perPage()+$key+1 }}</th>
                            <td class="px-4">{{$value->code}}</td>
                            <td class="px-4">{{$value->name}}</td>
                            <td class="px-4">
                                <a class="btn btn-outline-primary" wire:wire:navigate href="{{ route('department-lists-college',$value->id) }}">
                                    Departments
                                </a>
                            </td>
                            <td class="px-4">
                                <div class="d-flex justify-content-center gap-2">
                                   
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
        <div class="row d-flex justify-content-end">
            {{ $table_data->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
