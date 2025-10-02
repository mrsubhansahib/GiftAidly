@extends('layouts.master-frontend')
@section('content')

@include('layouts.partials.alert')
<!-- Animated Top Bar -->
<div class="animated-bar"></div>

<!-- Banner Section -->
<section class="banner">
    <div style="display: flex; justify-content: end; margin-bottom: 20px;">
        <a type="button" href="{{ route('any', ['index']) }}"
            style="background: linear-gradient(45deg, #1d43ab, #94740dff); 
                        background-size: 200% 200%;
                        background-position: left center;
                        text-decoration: none;
                        border: none; 
                        color: white;
                        font-weight: 600; 
                        padding: 6px 18px; 
                        border-radius: 25px;  
                        cursor: pointer;
                        z-index: 10;
                        transition: background-position 0.5s ease-in-out, transform 0.3s ease;
                        "
            onmouseover="this.style.backgroundPosition='right center'; this.style.transform='scale(1.05)';"
            onmouseout="this.style.backgroundPosition='left center'; this.style.transform='scale(1)';">
            <!-- Classic dashboard (Material Design Icons) -->
            <iconify-icon icon="mdi:view-dashboard" class="fs-20 align-middle"
                style="margin-right: 6px;"></iconify-icon>

            Dashboard
        </a>
    </div>
    <div class="floating-elements"></div>
    <div class="banner-content">
        <h1>Make a Difference Today</h1>
        <p>Join thousands of donors who are changing lives around the world. Every contribution matters, every
            donation counts.</p>
    </div>



</section>

<div class="tab-container">
    <div class="tab-header">
        <button class="tab-btn  active" onclick="openTab(event, 'daily-weekly-monthly')">Daily / Weekly / Monthly</button>
        <button class="tab-btn" onclick="openTab(event, 'friday')">Friday Giving</button>
        <button class="tab-btn" onclick="openTab(event, 'special')">Donate Special</button>
    </div>

    <div class="tab-content">
        <div id="daily-weekly-monthly" class="tab-panel active ">
            <div class="donation-card">
                <div class="card-header">
                    <div class="card-icon">
                        <span class="iconify" data-icon="mdi:calendar-clock" data-width="40"
                            data-height="40"></span>
                    </div>
                    <h2 class="card-title">Daily & Weekly & Monthly Donations</h2>
                    <p class="card-subtitle">Make consistent impact with regular contributions. Small amounts, big
                        difference.</p>
                </div>

                <form id="form-daily" action="{{ route('donation.daily_weekly_monthly') }}" method="POST">
                    @csrf
                    <div class="form-grid">
                        @php
                        $userCurrency = auth()->check()
                        ? auth()->user()->subscriptions()->pluck('currency')->unique()->first()
                        : null;
                        $currencies = ['gbp' => '£', 'usd' => '$', 'eur' => '€'];
                        @endphp
                        <div class="form-group">
                            <label for="currency">Currency</label>
                            <select name="currency" id="currency" required class="select-input">
                                @foreach ($currencies as $code => $symbol)
                                <option value="{{ $code }}" @selected($userCurrency===$code)
                                    @disabled($userCurrency && $userCurrency !==$code)
                                    title="{{ $userCurrency && $userCurrency !== $code ? 'You cannot select this currency because your previous donations were in ' . strtoupper($userCurrency) . '.' : '' }}">
                                    {{ $symbol }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="amount-daily">Amount</label>
                            <input type="number" name="amount" id="amount-daily" class="text-input"
                                min="1" />
                            <span id="error-amount-daily"
                                style="color: red; font-size: 13px; display: block; margin-top: 3px;"></span>
                        </div>

                        <div class="form-group">
                            <label for="type-daily">Type</label>
                            <select name="type" id="type-daily" required class="select-input">
                                <option value="day">Daily</option>
                                <option value="week">Weekly</option>
                                <option value="month">Monthly</option>
                            </select>
                        </div>

                        <!-- Single Range Picker -->
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="date-range-daily">Select Date Range</label>
                            <input type="text" id="date-range-daily" required class="text-input"
                                placeholder="Pick start and end dates" />
                            <input type="hidden" name="start_date" id="start_date-daily" />
                            <input type="hidden" name="cancellation" id="cancellation-daily" />
                            <span id="error-date-daily"
                                style="color: red; font-size: 13px; display: block; margin-top: 3px;"></span>
                        </div>

                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="card-element">Card Details</label>
                            <div id="card-element" class="text-input"></div>
                            <div id="card-errors" role="alert" style="color: red; margin-top: 5px;"></div>
                            <span id="error-card-daily"
                                style="color: red; font-size: 13px; display: block; margin-top: 3px;"></span>
                        </div>
                        <div class="form-group"
                            style="grid-column: 1 / -1; display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <label for="gift-aid-daily"
                                style="display: flex; align-items: center; gap: 6px; white-space: nowrap; margin-top: 6px;">
                                Gift Aid
                                <input type="checkbox" name="gift_aid" id="gift-aid-daily"
                                    data-target="address-daily" value="yes" />
                            </label>

                            <div style="flex: 1; min-width: 200px; display: flex; flex-direction: column;">
                                <input type="text" name="address" id="address-daily" class="text-input"
                                    style="display: none; width: 100%;" placeholder="Enter your address"
                                    value="{{ auth()->user()->address ? auth()->user()->address : '' }}" />
                                <span id="error-address-daily"
                                    style="color: red; font-size: 13px; display: block; margin-top: 3px;"></span>
                            </div>
                        </div>

                    </div>

                    <button type="submit" class="donate-btn">Donate Now</button>
                </form>

            </div>
        </div>

        <!-- Friday Tab -->
        <div id="friday" class="tab-panel ">
            <div class="donation-card">
                <div class="card-header">
                    <div class="card-icon">
                        <span class="iconify" data-icon="mdi:mosque" data-width="40" data-height="40"></span>
                    </div>
                    <h2 class="card-title">Friday Giving</h2>
                    <p class="card-subtitle">Make your Fridays more meaningful with special charitable
                        contributions.</p>
                </div>

                <!-- Friday -->
                <form id="form-friday" action="{{ route('donation.friday') }}" method="POST">
                    @csrf
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="currency-friday">Currency</label>
                            <select name="currency" id="currency-friday" class="select-input">
                                @foreach ($currencies as $code => $symbol)
                                <option value="{{ $code }}" @selected($userCurrency===$code)
                                    @disabled($userCurrency && $userCurrency !==$code)
                                    title="{{ $userCurrency && $userCurrency !== $code ? 'You cannot select this currency because your previous donations were in ' . strtoupper($userCurrency) . '.' : '' }}">
                                    {{ $symbol }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="amount-friday">Amount</label>
                            <input type="number" name="amount" id="amount-friday" class="text-input"
                                min="1" />
                            <span id="error-amount-friday"
                                style="color: red; font-size: 13px; display: block; margin-top: 3px;"></span>
                        </div>

                        <div class="form-group">
                            <label for="type-friday">Type</label>
                            <input type="text" id="type-friday" value="Friday" class="text-input" readonly />
                        </div>

                        <!-- Single Range Picker -->
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="date-range-friday">Select Date Range</label>
                            <input type="text" id="date-range-friday" class="text-input"
                                placeholder="Pick start and end dates" />
                            <input type="hidden" name="start_date" id="start_date-friday" />
                            <input type="hidden" name="cancellation" id="cancellation-friday" />
                            <span id="error-date-friday"
                                style="color: red; font-size: 13px; display: block; margin-top: 3px;"></span>
                        </div>

                        <!-- Stripe Card Element -->
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="card-element-friday">Card Details</label>
                            <div id="card-element-friday" class="text-input"></div>
                            <div id="card-errors-friday" role="alert" style="color: red; margin-top: 5px;">
                            </div>
                            <span id="error-card-friday"
                                style="color: red; font-size: 13px; display: block; margin-top: 3px;"></span>
                        </div>
                        <div class="form-group"
                            style="grid-column: 1 / -1; display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <label for="gift-aid-friday"
                                style="display: flex; align-items: center; gap: 6px; white-space: nowrap; margin-top: 6px;">
                                Gift Aid
                                <input type="checkbox" name="gift_aid" id="gift-aid-friday"
                                    data-target="address-friday" value="yes" />
                            </label>

                            <div style="flex: 1; min-width: 200px; display: flex; flex-direction: column;">
                                <input type="text" name="address" id="address-friday" class="text-input"
                                    style="display: none; width: 100%;" placeholder="Enter your address"
                                    value="{{ auth()->user()->address ? auth()->user()->address : '' }}" />
                                <span id="error-address-friday"
                                    style="color: red; font-size: 13px; display: block; margin-top: 3px;"></span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="donate-btn">Friday Donation</button>
                </form>

            </div>
        </div>

        <!-- Special Tab -->
        <div id="special" class="tab-panel ">
            <div class="donation-card">
                <div class="card-header">
                    <div class="card-icon">
                        <span class="iconify" data-icon="mdi:chart-line" data-width="40"
                            data-height="40"></span>
                    </div>
                    <h2 class="card-title">Special Donations</h2>
                    <p class="card-subtitle">Sustain long-term change with monthly contributions. Maximum impact,
                        consistent support.</p>
                </div>

                <!-- Monthly -->
                <form id="form-monthly" action="{{ route('donation.special') }}" method="POST">
                    @csrf
                    <div class="form-grid">
                        @php
                        use App\Models\SpecialDonation;
                        $specials = SpecialDonation::all();
                        @endphp

                        <div class="form-group">
                            <label for="currency-monthly">Currency</label>
                            <select name="currency" id="currency-monthly" class="select-input">
                                <option value="GBP" {{ old('currency', 'GBP') == 'GBP' ? 'selected' : '' }}>£
                                </option>
                                <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>$
                                </option>
                                <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>€
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="pay-amount">Amount</label>
                            <input type="number" name="amount" placeholder="100.00" readonly id="pay-amount"
                                class="text-input" />
                            <span id="error-amount-special"
                                style="color: red; font-size: 13px; display: block; margin-top: 3px;"></span>
                        </div>

                        <div class="form-group">
                            <label for="pay-special">Pay Special</label>
                            <select name="special" id="pay-special" class="select-input">
                                <option value="">-- Select Special --</option>
                                @foreach ($specials as $special)
                                <option value="{{ $special->id }}" data-price="{{ $special->price }}">
                                    {{ $special->name }}
                                </option>
                                @endforeach
                            </select>
                            <span id="error-product-special"
                                style="color: red; font-size: 13px; display: block; margin-top: 3px;"></span>
                        </div>


                        <!-- Stripe Card Element -->
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label for="card-element-monthly">Card Details</label>
                            <div id="card-element-monthly" class="text-input"></div>
                            <div id="card-errors-monthly" role="alert" style="color: red; margin-top: 5px;">
                            </div>
                        </div>
                        <div class="form-group"
                            style="grid-column: 1 / -1; display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <label for="gift-aid-monthly"
                                style="display: flex; align-items: center; gap: 6px; white-space: nowrap; margin-top: 6px;">
                                Gift Aid
                                <input type="checkbox" name="gift_aid" id="gift-aid-monthly"
                                    data-target="address-monthly" value="yes" />
                            </label>

                            <div style="flex: 1; min-width: 200px; display: flex; flex-direction: column;">
                                <input type="text" name="address" id="address-monthly" class="text-input"
                                    style="display: none; width: 100%;" placeholder="Enter your address"
                                    value="{{ auth()->user()->address ? auth()->user()->address : '' }}" />
                                <span id="error-address-special"
                                    style="color: red; font-size: 13px; display: block; margin-top: 3px;"></span>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="donate-btn">Donate Now</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection