<div>
    <div class="mb-3">
        <label>Full Name <span style="color: red">*</span></label>
        <input type="text" wire:model.live.debounce.1500ms="name" class="form-control">
        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label>Email <span style="color: red">*</span></label>
        <input type="email" wire:model.live.debounce.1500ms="email" class="form-control">
        @error('email') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="mb-3">
        <label>Currency <span style="color: red">*</span></label>
        <input type="text" class="form-control" value="{{ strtoupper($currency) }}" readonly>
    </div>

    <div class="mb-3">
        <label>Amount <span style="color: red">*</span></label>
        <input type="text" class="form-control" value="{{ number_format($amount, 2) }}" readonly>
    </div>
</div>
