<div>
    <!-- Monthly -->
    <form id="form-monthly" wire:submit.prevent="donate">
        <div class="form-grid">
            <div class="form-group">
                <label for="currency-monthly">Currency</label>
                <select wire:model="currency" id="currency-monthly" class="select-input">
                    <option value="GBP">£</option>
                    <option value="usd">$</option>
                    <option value="eur">€</option>
                </select>
            </div>

            <div class="form-group">
                <label for="amount-monthly">Amount</label>
                <input type="number" wire:model="amount" id="amount-monthly" class="text-input" min="1" />
            </div>

            <div class="form-group">
                <label for="type-monthly">Type</label>
                <input type="text" id="type-monthly" value="Monthly" wire:model="type" class="text-input" readonly />
            </div>

            <!-- Single Range Picker -->
            <div class="form-group" style="grid-column: 1 / -1;">
                <label for="date-range-monthly">Select Date Range</label>
                <input type="text" id="date-range-monthly" class="text-input"
                    placeholder="Pick start and end dates" />
                <input type="hidden" wire:model="start_date" id="start_date-daily">
                <input type="hidden" wire:model="cancellation" id="cancellation-daily">
                <input type="hidden" wire:model="payment_method_id" id="payment_method_id">
            </div>

            <!-- Stripe Card Element -->
            <div class="form-group" style="grid-column: 1 / -1;">
                <label for="card-element-monthly">Card Details</label>
                <div id="card-element-monthly" class="text-input"></div>
                <div id="card-errors-monthly" role="alert" style="color: red; margin-top: 5px;">
                </div>
            </div>
        </div>

        <button type="submit" class="donate-btn">Monthly Commitment</button>
    </form>
</div>
