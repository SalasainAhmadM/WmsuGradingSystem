<div>
    <div class="container-fluid d-flex justify-content-center shadow">
        <span class="fs-2 fw-bold h1 m-0 brand-color">  Delete {{ $title }}</span>
    </div>
    <div class="container-fluid">
        <div class="table-header">
            <livewire:admin.BreadCrumb.BreadCrumb/>
        </div>
        <div class="d-flex justify-content-between my-2 gap-2 row">
        </div>
        <form wire:submit.prevent="save()">
            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="year_level" class="form-label">Year Level</label>
                    <input type="text" id="year_level" disabled placeholder="Year level" wire:model.defer="detail.year_level" class="form-control @error('detail.year_level') is-invalid @enderror">
                    @error('detail.year_level')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row">
                <p class="text-warning d-flex justify-content-center">
                    Are you sure you want to delete this ?
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


