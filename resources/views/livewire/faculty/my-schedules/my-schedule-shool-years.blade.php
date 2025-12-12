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
        <div class="row p-2">
                @forelse($table_data as $key =>$value)
                    <div class="col-3 p-0 mx-2">
                        <a href="/faculty/my-schedules/{{ $value->year_start.'-'.$value->year_end }}">
                            <div class="d-flex align-items-center justify-content-between brand-bg-color p-3 rounded " style="min-width:200px;">
                                <div class="d-flex justify-content-end">
                                    <div class="">
                                        <p>Curriculum</p>
                                        <span class="fs-5">{{$value->year_start.' - '.$value->year_end}}</span>
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
