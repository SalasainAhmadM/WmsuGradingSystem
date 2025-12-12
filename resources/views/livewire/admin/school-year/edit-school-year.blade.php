<div>
    <div class="container-fluid d-flex justify-content-center shadow">
        <span class="fs-2 fw-bold h1 m-0 brand-color">  Edit {{ $title }} </span>
    </div>
    <div class="container-fluid">
        <div class="table-header">
            <livewire:admin.BreadCrumb.BreadCrumb/>
        </div>
        <div class="d-flex justify-content-between my-2 gap-2 row">
        </div>

        <label class="fs-3">{{$school_year['year_start'].' - '.$school_year['year_end']}}</label>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="startMonth" class="form-label">Start Month</label>
                <select id="startMonth" required wire:model.defer="school_year.date_start_month" class="form-select @error('school_year.date_start_month') is-invalid @enderror">
                    @foreach($months as $value)
                        <option value="{{ $value['month_number'] }}" {{ $detail['date_start_month'] == $value['month_number'] ? 'selected' : '' }}>
                            {{ $value['month_name'] }}
                        </option>
                    @endforeach
                </select>
                @error('school_year.date_start_month')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="startDate" class="form-label">Start Date</label>
                @if($detail['date_start_month'] > 0)
                    <select id="startDate" required wire:model.defer="school_year.date_start_date" class="form-control @error('school_year.date_start_date') is-invalid @enderror">
                        @for($i = 0; $i < $months[$detail['date_start_month'] - 1]['max_date']; $i++)
                            <option value="{{ $i + 1 }}">{{ $i + 1 }}</option>
                        @endfor
                    </select>
                    @error('school_year.date_start_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="endMonth" class="form-label">End Month</label>
                <select id="endMonth" required wire:model.defer="school_year.date_end_month" class="form-select @error('school_year.date_end_month') is-invalid @enderror">
                    @foreach($months as $value)
                        <option value="{{ $value['month_number'] }}" {{ $detail['date_end_month'] == $value['month_number'] ? 'selected' : '' }}>
                            {{ $value['month_name'] }}
                        </option>
                    @endforeach
                </select>
                @error('school_year.date_end_month')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="endDate" class="form-label">End Date</label>
                @if($detail['date_end_month'] > 0)
                    <select id="endDate" required wire:model.defer="school_year.date_end_date" class="form-control @error('school_year.date_end_date') is-invalid @enderror">
                        @for($i = 0; $i < $months[$detail['date_end_month'] - 1]['max_date']; $i++)
                            <option value="{{ $i + 1 }}">{{ $i + 1 }}</option>
                        @endfor
                    </select>
                    @error('school_year.date_end_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                @endif
            </div>
        </div>


        <div class="row my-4">
            <div class="col-12 d-flex justify-content-center">
                <div class="p-2">
                    <button class="btn btn-success" wire:click="saveEdit()">
                        Save
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>


