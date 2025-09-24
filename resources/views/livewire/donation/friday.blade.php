<div>
    <!-- Friday -->
    <form id="form-friday" wire:submit.prevent="donate">
        <div class="form-grid">
            <div class="form-group">
                <label for="currency-friday">Currency</label>
                <select wire:model="currency" id="currency-friday" class="select-input">
                    <option value="gbp">£</option>
                    <option value="usd">$</option>
                    <option value="eur">€</option>
                </select>
            </div>

            <div class="form-group">
                <label for="amount-friday">Amount</label>
                <input type="number" wire:model="amount" id="amount-friday" class="text-input" min="1" />
            </div>

            <div class="form-group">
                <label for="type-friday">Type</label>
                <input type="text" wire:model="type" id="type-friday" value="Friday" class="text-input"
                    min="1" readonly />
            </div>

            <!-- Single Range Picker -->
            <div class="form-group" style="grid-column: 1 / -1;">
                <label for="date-range-friday">Select Date Range</label>
                <input type="text" id="date-range-friday" class="text-input" wire:model="date_range"
                    placeholder="Pick start and end dates" />
                <input type="hidden" wire:model="start_date" id="start_date-daily">
                <input type="hidden" wire:model="cancellation" id="cancellation-daily">
                <input type="hidden" wire:model="payment_method_id" id="payment_method_id">
            </div>

            <!-- Stripe Card Element -->
            <div class="form-group" style="grid-column: 1 / -1;">
                <label for="card-element-friday">Card Details</label>
                <div id="card-element-friday" class="text-input"></div>
                <div id="card-errors-friday" role="alert" style="color: red; margin-top: 5px;">
                </div>
            </div>

        </div>

        <button type="submit" class="donate-btn">Friday Donation</button>
    </form>
</div>
