<div class="mb-4">
    <div class="form-floating mb-3">
        <input type="text" wire:model.live.debounce.1500ms="name" class="form-control" placeholder="Full Name">
        <label>Full Name <span class="text-danger">*</span></label>
        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="form-floating mb-3">
        <input type="email" wire:model.live.debounce.1500ms="email" class="form-control" placeholder="Email">
        <label>Email <span class="text-danger">*</span></label>
        @error('email') <small class="text-danger">{{ $message }}</small> @enderror
    </div>

    <div class="form-floating mb-3">
        <input type="text" class="form-control" value="{{ strtoupper($currency) }}" readonly placeholder="">
        <label>Currency </label>
    </div>

    <div class="form-floating mb-3">
        <input type="text" class="form-control" value="{{ number_format($amount, 2) }}" readonly placeholder="">
        <label>Amount </label>
    </div>
</div>