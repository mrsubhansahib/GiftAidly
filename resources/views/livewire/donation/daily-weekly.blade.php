<div>
    <form wire:submit.prevent="donate" id="form-daily">
        <div class="form-grid">
            <div class="form-group">
                <label for="currency-daily">Currency</label>
                <select wire:model="currency" id="currency-daily" class="select-input">
                    <option value="gbp">£</option>
                    <option value="usd">$</option>
                    <option value="eur">€</option>
                </select>
                @error('currency') <span class="text-danger-500">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="amount-daily">Amount</label>
                <input type="number" wire:model="amount" id="amount-daily" class="text-input" min="1" />
                @error('amount') <span class="text-danger-500">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="type-daily">Type</label>
                <select wire:model="type" id="type-daily" class="select-input">
                    <option value="day">Daily</option>
                    <option value="week">Weekly</option>
                </select>
                @error('type') <span class="text-danger-500">{{ $message }}</span> @enderror
            </div>

            <!-- Date Range -->
            <div class="form-group" style="grid-column: 1 / -1;">
                <label for="date-range-daily">Select Date Range</label>
                <input type="text" id="date-range-daily" class="text-input"
                    placeholder="Pick start and end dates" />
                <input type="hidden" wire:model="start_date" id="start_date-daily">
                <input type="hidden" wire:model="cancellation" id="cancellation-daily">
                <input type="hidden" wire:model="payment_method_id" id="payment_method_id">
                @error('start_date') <span class="text-danger-500">{{ $message }}</span> @enderror
                @error('cancellation') <span class="text-danger-500">{{ $message }}</span> @enderror
            </div>

            <!-- Stripe -->
            <div class="form-group" style="grid-column: 1 / -1;">
                <label for="card-element">Card Details</label>
                <div id="card-element" class="text-input"></div>
                <div id="card-errors" role="alert" style="color: red; margin-top: 5px;"></div>
            </div>
        </div>

        <button type="submit" class="donate-btn">Donate Now</button>
    </form>
</div>
