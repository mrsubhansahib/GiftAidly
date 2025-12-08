<div class="form-grid">
    <div class="mb-3 form-group"  style="grid-column: 1 / -1;">
        <label>Full Name *</label>
        <input type="text" wire:model.live.debounce.800ms="name" class="text-input">
        @error('name')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="mb-3 form-group"  style="grid-column: 1 / -1;">
        <label>Email *</label>
        <input type="email" wire:model.live.debounce.800ms="email" class="text-input">
        @error('email')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="mb-3 form-group">
        <label>Special Donation *</label>
        <select wire:model.live="special" class="text-input">
            <option value="">-- Select Special --</option>
            @foreach ($specials as $sp)
                <option value="{{ $sp->id }}">{{ $sp->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3 form-group">
        <label>Amount *</label>
        <input type="number" wire:model.live="amount" class="text-input">
    </div>

    <div class="mb-3 form-group">
        <label>Currency *</label>
        <select wire:model.live="currency" class="text-input">
            <option value="gbp">£</option>
            <option value="usd">$</option>
            <option value="eur">€</option>
        </select>
    </div>
</div>
