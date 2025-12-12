<div>
    <div class="container-fluid d-flex justify-content-center shadow">
        <span class="fs-2 fw-bold h1 m-0 brand-color">  {{ $title }}s</span>
    </div>
    <div class="container-fluid">
        <div class="table-header">
            <livewire:admin.BreadCrumb.BreadCrumb/>
        </div>
        <div class="d-flex justify-content-between my-2 row">
            <div class="col-4">
                <input type="search" wire:model.live="filters.search" name="" id="" placeholder="Search ... " class="form-control">
            </div>
            <div class="d-flex col justify-content-end gap-2">
            </div>
        </div>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3 mb-3">
            @forelse($table_data as $key =>$value)
                <div class="col">
                    <a class="course d-flex align-items-center justify-content-start brand-bg-color fs-5 h-100 rounded position-relative" 
                        href="/admin/faculty-and-scheduling/{{ $school_year }}/{{ $value->code }}">
                        <div class="p-3 rounded " style="min-width:200px; height: 120px;">
                            <div class="d-flex justify-content-end">
                                <div class="">
                                    <p>{{ $value->name }}</p>
                                    <span class="fs-5"></span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
            @endforelse
        </div>
        <div class="row mx-5 d-flex justify-content-end">
            {{ $table_data->links('pagination::bootstrap-5') }}
        </div>  
    </div>
</div>
