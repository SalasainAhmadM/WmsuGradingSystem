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
                <input type="search" name="" id="" placeholder="Search ... " class="form-control">
            </div>
            <div class="d-flex col-7 justify-content-end gap-2">
                <a class="btn btn-primary" wire:wire:navigate href="{{ route($route.'-add') }}">
                    <svg  viewBox="0 0 20 20" width="20px" xmlns="http://www.w3.org/2000/svg" fill="none"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path fill="currentColor" fill-rule="evenodd" d="M9 17a1 1 0 102 0v-6h6a1 1 0 100-2h-6V3a1 1 0 10-2 0v6H3a1 1 0 000 2h6v6z"></path> </g></svg>
                </a>
            </div>
        </div>
        <div class="row ">
            <div class="table-responsive">
                <table class="table table-striped table-bordered" >
                    <thead style="background:#952323;color:white;">
                        <tr class="align-middle">
                            <th scope="col" class="px-4 ">#</th>
                            <th scope="col" class="px-4 ">School Year</th>
                            <th scope="col" class="px-4 ">Start Date</th>
                            <th scope="col" class="px-4 ">End Date</th>
                            <th scope="col" class="text-center px-4 ">Actions</th> 
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($table_data as $key =>$value)
                            <tr class="align-middle">
                            <th scope="row" class="px-4 ">{{($table_data->currentPage()-1)*$table_data->perPage()+$key+1 }}</th>
                                <td class="px-4 ">{{$value->year_start.' - '.$value->year_end}}</td>
                                <td scope="col" class="px-4 ">{{date_format(date_create($value->date_start),"M d, Y")}}</td>
                                <td scope="col" class="px-4 ">{{date_format(date_create($value->date_end),"M d, Y")}}</td>
                                <td class="px-4 ">
                                    <div class="d-flex justify-content-center">
                                        <a href="{{ route('school-year-edit',$value->id) }}" type="button" wire:wire:navigate  class="btn btn-success">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="" width="15px" viewbox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                                <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
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
        <div class="row mx-5 d-flex justify-content-end">
            {{ $table_data->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
