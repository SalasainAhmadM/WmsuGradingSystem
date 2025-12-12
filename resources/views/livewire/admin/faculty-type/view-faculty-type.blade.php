<div>
    <div class="container-fluid d-flex justify-content-center shadow">
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
                    <label for="code" class="form-label"> Code</label>
                    <input type="text" id="code" wire:model.defer="detail.code" class="form-control @error('detail.code') is-invalid @enderror" disabled>
                    @error('detail.code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label"> Name</label>
                    <input type="text" id="name" wire:model.defer="detail.name" class="form-control @error('detail.name') is-invalid @enderror" disabled>
                    @error('detail.name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="row my-4">
                <div class="col-12 d-flex justify-content-center">
                    <div class="p-2">
                        <a href="{{ route($route."-lists") }}" wire:navigate class="btn btn-secondary" type="submit">
                            Back
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>


