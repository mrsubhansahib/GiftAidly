<div class="form-grid">
    <div class="mb-3 form-group" style="grid-column: 1 / -1;">
        <label>Full Name <span style="color:red">*</span></label>
        <input type="text" wire:model.live.debounce.800ms="name" class="text-input">
        @error('name')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="mb-3 form-group" style="grid-column: 1 / -1;">
        <label>Email <span style="color:red">*</span></label>
        <input type="email" wire:model.live.debounce.800ms="email" class="text-input">
        @error('email')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="mb-3 form-group">
        <label>Special Donation <span style="color:red">*</span></label>
        <select wire:model.live.debounce.800ms="special" id="pay-special-donation" class="select-input">
            <option value="">-- Select Special --</option>
            @foreach ($specials as $sp)
                <option value="{{ $sp->id }}" data-price="{{ $sp->price }}">{{ $sp->name }}</option>
            @endforeach
        </select>
        @error('special')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="mb-3 form-group">
        <label>Amount <span style="color:red">*</span></label>
        <input type="number" wire:model="amount" readonly placeholder="00.00" id="pay-special-donation-amount"
            class="text-input">
        @error('amount')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="mb-3 form-group">
        <label>Currency <span style="color:red">*</span></label>
        <select wire:model.live.debounce.800ms="currency" id="special-donation-currency" class="select-input">
            <option value="GBP">£</option>
            <option value="USD">$</option>
            <option value="EUR">€</option>
        </select>
        @error('currency')
            <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
    <div class="form-group"
        style="grid-column: 1 / -1; display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
        <label for="gift-aid-monthly"
            style="display: flex; align-items: center; gap: 6px; white-space: nowrap; margin-top: 6px;padding: 6px 0px;">
            Gift Aid
            <input type="checkbox" name="gift_aid" id="gift-aid-monthly" data-target="address-monthly"/>
        </label>

        <div style="flex: 1; min-width: 200px; display: flex; flex-direction: column;">
            <input type="text" name="address" id="address-monthly" class="text-input"
                style="padding: 6px 20px;display: none; width: 100%;" placeholder="Enter your address" />
            <span id="error-address-special"
                style="color: red; font-size: 13px; display: block; margin-top: 3px;"></span>
        </div>
    </div>
</div>
{{-- Currency Conversion Script --}}
@script
    <script>
        $(function() {
            let rates, fetched = false;
            let specialInitialized = false;
            const fetchRates = cb => {
                if (fetched) return cb();
                $.get('https://api.frankfurter.app/latest', {
                        from: 'GBP',
                        to: 'USD,EUR'
                    })
                    .done(data => {
                        if (data?.rates) {
                            rates = data.rates;
                            fetched = true;
                            cb();
                        }
                    })
                    .fail(() => console.error('Currency API failed'));
            };

            const initSpecial = () => {
                if (specialInitialized) return;
                specialInitialized = true; // 🔥 Only run once
                const userCurrency = @json($userCurrency ?? null);
                if (!userCurrency) {
                    $('#special-donation-currency').val('GBP');
                }

                const updateAmount = () => {
                    const s = $('#pay-special-donation').find(':selected'),
                        p = parseFloat(s.data('price')) || 0,
                        c = $('#special-donation-currency').val(),
                        r = rates?.[c];
                    if (!p || !c) return $('#pay-special-donation-amount').val('');
                    $('#pay-special-donation-amount').val(c === 'GBP' ? p.toFixed(2) : (p * r).toFixed(2));
                    @this.set('amount', $('#pay-special-donation-amount').val());
                };

                if (!$('#pay-special-donation').data('bound')) {
                    $('#pay-special-donation, #special-donation-currency')
                        .on('change', updateAmount)
                        .data('bound', true);
                }

                fetchRates(updateAmount);
            };

            if ($('#special').hasClass('active')) initSpecial();

            $('.tab-btn').on('click', function() {

                setTimeout(() => {
                    if (!specialInitialized) initSpecial();
                }, 200);

            });
        });
    </script>
@endscript
