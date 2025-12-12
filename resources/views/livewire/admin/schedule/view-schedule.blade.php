<div>
    <div class="container-fluid d-flex justify-content-center shadow text-dark">
        <span class="fs-2 fw-bold h1 m-0 brand-color"> Delete {{ $title }}</span>
    </div>
    <div class="container-fluid">
        <div class="table-header">
            <livewire:admin.BreadCrumb.BreadCrumb/>
        </div>
        <div class="d-flex justify-content-between my-2 gap-2 row">
        </div>

        <form wire:submit.prevent="save()" id="addform">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="subject_id" class="form-label">Subject</label>
                    <select name="subject_id" id="subject_id" wire:model="detail.subject_id" disabled class="form-select @error('detail.subject_id') is-invalid @enderror">  
                        <option value="">Select Subject</option>
                        @foreach ($subjects as $key => $value )
                            <option value="{{ $value->id }}" >{{ $value->subject_id.' - '.$value->subject_code }}</option>
                        @endforeach
                    </select>
                    @error('detail.subject_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror  
                </div>
                <div class="col-md-6 mb-3">
                    <label for="room_id" class="form-label">Room</label>
                    <select name="room_id" id="room_id" wire:model="detail.room_id" disabled class="form-select @error('detail.room_id') is-invalid @enderror">  
                        <option value="">Select Room</option>
                        @foreach ($rooms as $key => $value )
                            <option value="{{ $value->id }}" >{{ $value->code.' - '.$value->name }}</option>
                        @endforeach
                    </select>
                    @error('detail.room_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror  
                </div>
            </div>
            <div class="row">
                 <div class="col-md-6 mb-3">
                    <label for="schedule_from" class="form-label">Start period</label>
                    <input type="time" name="schedule_from" id="schedule_from" disabled wire:model="detail.schedule_from" class="form-select @error('detail.schedule_from') is-invalid @enderror">  
                    @error('detail.schedule_from')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror  
                </div>
                <div class="col-md-6 mb-3">
                    <label for="schedule_to" class="form-label">End period</label>
                    <input type="time" name="schedule_to" id="schedule_to" disabled wire:model="detail.schedule_to" class="form-select @error('detail.schedule_to') is-invalid @enderror">  
                    @error('detail.schedule_to')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror  
                </div>
                <label for="day" class="form-label">Days</label>
                @error('detail.day')
                    <div class="text-danger text-xs">{{ $message }}</div>
                @enderror  
                <div class="col-md-6 mb-3" wire:ignore>
                    <select name="day" id="day" wire:model.live="detail.day" disabled multiple="multiple" wire:ignore class="form-select position-relative z-10 select2 @error('detail.day') is-invalid @enderror">  
                        @foreach ($days as $key => $value )
                            <option value="{{ $value['day'] }}" >{{$value['day_full'] }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-6 mb-3 d-flex align-items-end">
                    <div>
                        Is Lecture?
                        <div class="form-check">
                            <input class="form-check-input" disabled type="radio" name="radioDefault" id="radioDefault1" value="1" wire:model="detail.is_lec">
                            <label class="form-check-label" for="radioDefault1">
                                Lecture Subject
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" disabled type="radio" name="radioDefault" id="radioDefault2" value="0" wire:model="detail.is_lec">
                            <label class="form-check-label" for="radioDefault2">
                                Laboratory Subject
                            </label>
                        </div>
                    </div>
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
    <style>
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Select2 Access Selection
            $('#day').select2();
            // Make sure Livewire receives the selected value
            $('#day').on('change', function(e) {
                var data = $(this).val(); // Get selected value
                @this.set('detail.day', data);
            });
        });
    </script>
</div>


