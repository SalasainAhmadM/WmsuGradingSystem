<div>
    <div class="container-fluid d-flex justify-content-center shadow">
        <span class="fs-2 fw-bold h1 m-0 brand-color"> Save {{ $title }}</span>
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
                    <label for="minimum" class="form-label"> Grade Mininum</label>
                    <input type="number" id="minimum" disabled wire:model.defer="detail.minimum" placeholder="Minimum" class="form-control @error('detail.minimum') is-invalid @enderror">
                    @error('detail.minimum')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label for="maximum" class="form-label"> Grade Maximum</label>
                    <input type="number" id="maximum" disabled wire:model.defer="detail.maximum" placeholder="Maximum" class="form-control @error('detail.maximum') is-invalid @enderror">
                    @error('detail.maximum')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-12 mb-3">
                    <label for="grade" class="form-label">Grade Equivalent</label>
                    <input type="text" id="grade" disabled wire:model.defer="detail.grade" placeholder="Grade Equivalent" class="form-control @error('detail.grade') is-invalid @enderror">
                    @error('detail.grade')
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


