@extends('layouts.master-frontend')
@section('content')
    @include('layouts.partials.alert')
    <!-- Animated Top Bar -->
    <div class="animated-bar"></div>

    <!-- Banner Section -->
    <section class="banner">

        <div class="floating-elements"></div>
        <div class="banner-content">
            <h1>Make a Difference Today</h1>
            <p>Join thousands of donors who are changing lives around the world. Every contribution matters, every
                donation counts.</p>
        </div>



    </section>

    <div class="tab-container">
        <div class="tab-header">
            <button class="tab-btn  active" onclick="openTab(event, 'daily-weekly-monthly')">Daily / Weekly /
                Monthly</button>
            <button class="tab-btn" onclick="openTab(event, 'friday')">Friday Giving</button>
            <button class="tab-btn" onclick="openTab(event, 'special')">Donate Special</button>
        </div>

        <div class="tab-content">
            <!-- daily/weekly/mothly -->
            <div id="daily-weekly-monthly" class="tab-panel active ">
                <div class="donation-card">
                    <div class="card-header">
                        <div class="card-icon">
                            <span class="iconify" data-icon="mdi:calendar-clock" data-width="40" data-height="40"></span>
                        </div>
                        <h2 class="card-title">Daily & Weekly & Monthly Donations</h2>
                        <p class="card-subtitle">
                            Make consistent impact with regular contributions. Small amounts, big difference.
                        </p>
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


                            <!-- ✅ Name Field -->
                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label for="name-daily">Full Name</label>
                                <input type="text" name="name" id="name-daily" value="{{ old('name') }}"
                                    class="text-input" placeholder="Enter your full name" />
                                <span id="error-name-daily"
                                    style="color: red; font-size: 13px; display: block; margin-top: 3px;"></span>
                            </div>

                            <!-- ✅ Email Field -->
                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label for="email-daily">Email Address</label>
                                <input type="email" name="email" id="email-daily" value="{{ old('email') }}"
                                    class="text-input" placeholder="Enter your email" />
                                <span id="error-email-daily"
                                    style="color: red; font-size: 13px; display: block; margin-top: 3px;"></span>
                            </div>

                            <div class="form-group">
                                <label for="currency">Currency</label>
                                <select name="currency" id="currency" required class="select-input">
                                    @foreach ($currencies as $code => $symbol)
                                        <option value="{{ $code }}" @selected(old('currency', $userCurrency) === $code)
                                            @disabled($userCurrency && $userCurrency !== $code)
                                            title="{{ $userCurrency && $userCurrency !== $code ? 'You cannot select this currency because your previous donations were in ' . strtoupper($userCurrency) . '.' : '' }}">
                                            {{ $symbol }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="amount-daily">Amount</label>
                                <input type="number" name="amount" id="amount-daily" value="{{ old('amount') }}"
                                    class="text-input" min="01" step="any" />
                                <span id="error-amount-daily"
                                    style="color: red; font-size: 13px; display: block; margin-top: 3px;"></span>
                            </div>

                            <div class="form-group">
                                <label for="type-daily">Type</label>
                                <select name="type" id="type-daily" required class="select-input">
                                    <option value="day" @selected(old('type') === 'day')>Daily</option>
                                    <option value="week" @selected(old('type') === 'week')>Weekly</option>
                                    <option value="month" @selected(old('type') === 'month')>Monthly</option>
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
                                        data-target="address-daily" value="yes"
                                        {{ old('gift_aid') === 'yes' ? 'checked' : '' }} />
                                </label>

                                <div style="flex: 1; min-width: 200px; display: flex; flex-direction: column;">
                                    <input type="text" name="address" id="address-daily" class="text-input"
                                        style="{{ old('gift_aid') === 'yes' ? 'display: block; width: 100%;' : 'display: none; width: 100%;' }}"
                                        value="{{ old('address') }}" placeholder="Enter your address" />
                                    <span id="error-address-daily"
                                        style="color: red; font-size: 13px; display: block; margin-top: 3px;"></span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="donate-btn">Donate Now</button>
                        <input type="hidden" name="active_tab" value="">
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
                        <p class="card-subtitle">
                            Make your Fridays more meaningful with special charitable contributions.
                        </p>
                    </div>

                    <!-- Friday -->
                    <form id="form-friday" action="{{ route('donation.friday') }}" method="POST">
                        @csrf
                        <div class="form-grid">

                            <!-- ✅ Full Name -->
                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label for="name-friday">Full Name</label>
                                <input type="text" name="name" value="{{ old('name') }}" id="name-friday"
                                    class="text-input" placeholder="Enter your full name" />
                                <span id="error-name-friday"
                                    style="color: red; font-size: 13px; display: block; margin-top: 3px;"></span>
                            </div>

                            <!-- ✅ Email -->
                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label for="email-friday">Email Address</label>
                                <input type="email" name="email" value="{{ old(key: 'email') }}" id="email-friday"
                                    class="text-input" placeholder="Enter your email" />
                                <span id="error-email-friday"
                                    style="color: red; font-size: 13px; display: block; margin-top: 3px;"></span>
                            </div>

                            <div class="form-group">
                                <label for="currency-friday">Currency</label>
                                <select name="currency" id="currency-friday" class="select-input" required>
                                    @foreach ($currencies as $code => $symbol)
                                        <option value="{{ $code }}" @selected(old('currency', $userCurrency) === $code)
                                            @disabled($userCurrency && $userCurrency !== $code)
                                            title="{{ $userCurrency && $userCurrency !== $code ? 'You cannot select this currency because your previous donations were in ' . strtoupper($userCurrency) . '.' : '' }}">
                                            {{ $symbol }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="amount-friday">Amount</label>
                                <input type="number" name="amount" id="amount-friday" class="text-input"
                                    min="01" step="any" value="{{ old('amount') }}" />
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
                                    placeholder="Pick start and end dates" required />
                                <input type="hidden" name="start_date" id="start_date-friday" />
                                <input type="hidden" name="cancellation" id="cancellation-friday" />
                                <span id="error-date-friday"
                                    style="color: red; font-size: 13px; display: block; margin-top: 3px;"></span>
                            </div>

                            <!-- Stripe Card Element -->
                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label for="card-element-friday">Card Details</label>
                                <div id="card-element-friday" class="text-input"></div>
                                <div id="card-errors-friday" role="alert" style="color: red; margin-top: 5px;"></div>
                                <span id="error-card-friday"
                                    style="color: red; font-size: 13px; display: block; margin-top: 3px;"></span>
                            </div>

                            <div class="form-group"
                                style="grid-column: 1 / -1; display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                                <label for="gift-aid-friday"
                                    style="display: flex; align-items: center; gap: 6px; white-space: nowrap; margin-top: 6px;">
                                    Gift Aid
                                    <input type="checkbox" name="gift_aid" id="gift-aid-friday"
                                        data-target="address-friday"
                                        value="{{ old('gift_aid') === 'yes' ? '' : 'display: none;' }}" />
                                </label>

                                <div style="flex: 1; min-width: 200px; display: flex; flex-direction: column;">
                                    <input type="text" name="address" value="{{ old('address') }}"
                                        id="address-friday" class="text-input" style="display: none; width: 100%;"
                                        placeholder="Enter your address" />
                                    <span id="error-address-friday"
                                        style="color: red; font-size: 13px; display: block; margin-top: 3px;"></span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="donate-btn">Friday Donation</button>
                        <input type="hidden" name="active_tab" value="">
                    </form>
                </div>
            </div>


            <!-- Special Tab -->
            <div id="special" class="tab-panel ">
                <div class="donation-card">
                    <div class="card-header">
                        <div class="card-icon">
                            <span class="iconify" data-icon="mdi:chart-line" data-width="40" data-height="40"></span>
                        </div>
                        <h2 class="card-title">Special Donations</h2>
                        <p class="card-subtitle">
                            Sustain long-term change with monthly contributions. Maximum impact, consistent support.
                        </p>
                    </div>

                    @livewire('special-donation')
                    <div class="form-grid">
                        <!-- Stripe Card Element -->
                        <div class="form-group card-input" hidden wire:ignore style="grid-column: 1 / -1;">
                            <label for="card-element-special-donations">Card Details</label>
                            <div id="card-element-special-donations" class="text-input"></div>
                            <div id="card-errors-special-donations" role="alert" style="color: red; margin-top: 5px;">
                            </div>
                        </div>

                        <div class="form-group"
                            style="grid-column: 1 / -1; display: flex; align-items: center; gap: 12px; flex-wrap: wrap;">
                            <label for="gift-aid-monthly"
                                style="display: flex; align-items: center; gap: 6px; white-space: nowrap; margin-top: 6px;">
                                Gift Aid
                                <input type="checkbox" name="gift_aid" id="gift-aid-monthly"
                                    data-target="address-monthly"
                                    value="{{ old('gift_aid') === 'yes' ? '' : 'display: none;' }}" />
                            </label>

                            <div style="flex: 1; min-width: 200px; display: flex; flex-direction: column;">
                                <input type="text" name="address" id="address-monthly" class="text-input"
                                    style="{{ old('gift_aid') === 'yes' ? 'display: block; width: 100%;' : 'display: none; width: 100%;' }}"
                                    value="{{ old('address') }}" placeholder="Enter your address" />
                                <span id="error-address-special"
                                    style="color: red; font-size: 13px; display: block; margin-top: 3px;"></span>
                            </div>
                        </div>
                    </div>
                    <button id="specialDonationSubmitBtn" class="btn btn-primary w-100 mt-3" disabled>
                        <span id="btnLoaderSpecial">Pay Now</span>
                        <span id="btnTextSpecial" class="spinner-border spinner-border-sm d-none"></span>
                    </button>
                    <input type="hidden" name="active_tab" value="">

                </div>
            </div>

        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        window.addEventListener("special-stripe-init", function(event) {
            document.querySelector(".card-input").removeAttribute("hidden");

            const clientSecret = event.detail.clientSecretSpecial;

            const stripe = Stripe("{{ env('STRIPE_KEY') }}");

            const elements = stripe.elements({
                clientSecret
            });

            const paymentElement = elements.create("payment");

            paymentElement.mount("#card-element-special-donations");

            document.getElementById("specialDonationSubmitBtn").disabled = false;

            document.getElementById("specialDonationSubmitBtn").onclick = async function(e) {
                e.preventDefault();

                document.getElementById("btnLoaderSpecial").classList.remove("d-none");
                document.getElementById("btnTextSpecial").innerHTML = "Processing...";

                const {
                    error
                } = await stripe.confirmPayment({
                    elements,
                    confirmParams: {
                        return_url: "{{ route('zakat.redirect') }}"
                    }
                });

                if (error) {
                    document.getElementById("card-errors-special-donations").innerText = error.message;
                    document.getElementById("btnLoaderSpecial").classList.add("d-none");
                    document.getElementById("btnTextSpecial").innerHTML = "Pay Now";
                }
            };
        });
    </script>
    <script>
        // ------------------------------
        // ✅ Tab Switching
        // ------------------------------
        function openTab(evt, tabName) {
            var i, tabPanels, tabBtns;
            tabPanels = document.getElementsByClassName("tab-panel");
            for (i = 0; i < tabPanels.length; i++) tabPanels[i].classList.remove("active");
            tabBtns = document.getElementsByClassName("tab-btn");
            for (i = 0; i < tabBtns.length; i++) tabBtns[i].classList.remove("active");
            document.getElementById(tabName).classList.add("active");
            evt.currentTarget.classList.add("active");
        }

        // ------------------------------
        // ✅ Card hover animations
        // ------------------------------
        document.querySelectorAll('.donation-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px) scale(1.02)';
            });
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // ------------------------------
        // ✅ Helper Functions
        // ------------------------------
        function fmt(d) {
            const pad = n => String(n).padStart(2, '0');
            return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
        }

        const firstOfMonth = (y, m) => new Date(y, m, 1);
        const lastOfMonth = (y, m) => new Date(y, m + 1, 0);

        function addMonths(date, count) {
            const d = new Date(date);
            const day = d.getDate();
            d.setMonth(d.getMonth() + count);
            if (d.getDate() < day) {
                d.setDate(0);
            }
            return d;
        }

        function fmt(d) {
            const pad = n => String(n).padStart(2, '0');
            return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
        }

        function addMonths(date, count) {
            const d = new Date(date);
            const day = d.getDate();
            d.setMonth(d.getMonth() + count);
            if (d.getDate() < day) {
                d.setDate(0);
            }
            return d;
        }

        // ------------------------------
        // ✅ Daily/Weekly/Monthly Picker with Type
        // ------------------------------
        function attachRangePickerWithType(rangeId, startHiddenId, endHiddenId, typeId, formId) {
            const rangeEl = document.getElementById(rangeId);
            const startEl = document.getElementById(startHiddenId);
            const endEl = document.getElementById(endHiddenId);
            const typeEl = document.getElementById(typeId);
            const form = document.getElementById(formId);

            if (!rangeEl || !startEl || !endEl || !typeEl || !form) return;

            // grab error spans
            const amountError = form.querySelector('#error-amount-daily');
            const dateError = form.querySelector('#error-date-daily');
            const addressError = form.querySelector('#error-address-daily');

            let fp = null;

            function initFlatpickr() {
                if (fp) fp.destroy();

                rangeEl.value = "";
                startEl.value = "";
                endEl.value = "";

                const type = typeEl.value;

                fp = flatpickr(rangeEl, {
                    mode: "range",
                    altInput: true,
                    altFormat: "F j, Y",
                    dateFormat: "Y-m-d",
                    minDate: "today",
                    onClose(selected) {
                        if (selected.length === 2) {
                            const start = selected[0];
                            const end = selected[1];
                            const diffDays = (end - start) / (1000 * 60 * 60 * 24);

                            if (type === "week" && diffDays < 7) {
                                dateError.textContent = "Please select at least one full week (7 days).";
                                fp.clear();
                                startEl.value = "";
                                endEl.value = "";
                                return;
                            }

                            if (type === "month") {
                                const minEnd = addMonths(start, 1);
                                if (end < minEnd) {
                                    dateError.textContent =
                                        `Please select at least one full month (${fmt(start)} → ${fmt(minEnd)} or later).`;
                                    fp.clear();
                                    startEl.value = "";
                                    endEl.value = "";
                                    return;
                                }
                            }

                            dateError.textContent = "";
                        }

                        startEl.value = selected[0] ? fmt(selected[0]) : "";
                        endEl.value = selected[1] ? fmt(selected[1]) : "";
                    }
                });
            }

            initFlatpickr();
            typeEl.addEventListener("change", initFlatpickr);

            // ✅ validation on submit
            form.addEventListener("submit", function(e) {
                e.preventDefault();

                const nameInput = form.querySelector('#name-daily');
                const emailInput = form.querySelector('#email-daily');
                const amount = form.querySelector('input[name="amount"]');
                const giftAidCheckbox = form.querySelector('#gift-aid-daily');
                const addressInput = form.querySelector('#address-daily');

                const nameError = form.querySelector('#error-name-daily');
                const emailError = form.querySelector('#error-email-daily');
                const amountError = form.querySelector('#error-amount-daily');
                const dateError = form.querySelector('#error-date-daily');
                const addressError = form.querySelector('#error-address-daily');

                let valid = true;

                // ✅ Name Validation
                if (!nameInput.value.trim()) {
                    nameError.textContent = 'Please enter your full name.';
                    valid = false;
                } else if (nameInput.value.trim().length < 3) {
                    nameError.textContent = 'Name must be at least 3 characters long.';
                    valid = false;
                } else {
                    nameError.textContent = '';
                }

                // ✅ Email Validation
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailInput.value.trim()) {
                    emailError.textContent = 'Please enter your email address.';
                    valid = false;
                } else if (!emailPattern.test(emailInput.value.trim())) {
                    emailError.textContent = 'Please enter a valid email format.';
                    valid = false;
                } else {
                    emailError.textContent = '';
                }

                // ✅ Amount
                if (!amount.value || parseFloat(amount.value) < 1) {
                    amountError.textContent = 'Please enter a valid amount.';
                    valid = false;
                } else {
                    amountError.textContent = '';
                }

                // ✅ Date range
                if (!startEl.value || !endEl.value) {
                    dateError.textContent = 'Please select a valid date range.';
                    valid = false;
                } else {
                    dateError.textContent = '';
                }

                // ✅ Gift Aid
                if (giftAidCheckbox.checked && addressInput.value.trim() === '') {
                    addressError.textContent = 'Please enter your address for Gift Aid.';
                    valid = false;
                } else {
                    addressError.textContent = '';
                }

                if (!valid) e.preventDefault();
            });

        }
        // ------------------------------
        // ✅ Friday Range Picker
        // ------------------------------
        document.addEventListener("DOMContentLoaded", function() {
            attachRangePickerFridays(
                "date-range-friday", // range input
                "start_date-friday", // hidden start
                "cancellation-friday", // hidden end
                "form-friday" // form
            );
        });

        function attachRangePickerFridays(rangeId, startHiddenId, endHiddenId, formId) {
            const rangeEl = document.getElementById(rangeId);
            const startEl = document.getElementById(startHiddenId);
            const endEl = document.getElementById(endHiddenId);
            const form = document.getElementById(formId);

            if (!rangeEl || !startEl || !endEl || !form) return;

            // hidden input for CSV of Fridays
            let fridaysHidden = form.querySelector('input[name="fridays"]');
            if (!fridaysHidden) {
                fridaysHidden = document.createElement('input');
                fridaysHidden.type = 'hidden';
                fridaysHidden.name = 'fridays';
                form.appendChild(fridaysHidden);
            }

            // error spans
            const dateError = form.querySelector('#error-date-friday');
            const amountError = form.querySelector('#error-amount-friday');
            const addressError = form.querySelector('#error-address-friday');

            const fp = flatpickr(rangeEl, {
                mode: "range",
                altInput: true,
                altFormat: "F j, Y",
                dateFormat: "Y-m-d",
                minDate: "today",

                onChange(selectedDates, dateStr, instance) {
                    if (selectedDates.length >= 1) {
                        const start = selectedDates[0];
                        const end = selectedDates[1] || selectedDates[0]; // ✅ if only one selected, use same date

                        // ✅ get all Fridays
                        let fridays = [];
                        let current = new Date(start);
                        while (current <= end) {
                            if (current.getDay() === 5) fridays.push(fmt(current));
                            current.setDate(current.getDate() + 1);
                        }

                        // ✅ enforce at least 2 Fridays
                        if (fridays.length < 2) {
                            dateError.textContent = "Please select at least 2 Fridays.";
                            startEl.value = "";
                            endEl.value = "";
                            fridaysHidden.value = "";
                            return;
                        }

                        // ✅ backend hidden fields
                        startEl.value = fridays[0];
                        endEl.value = fridays[fridays.length - 1];
                        fridaysHidden.value = fridays.join(",");

                        // ✅ show range on input
                        instance._input.value =
                            `${instance.formatDate(start, "F j, Y")} → ${instance.formatDate(end, "F j, Y")}`;

                        // ✅ highlight Fridays
                        highlightFridays(instance, start, end);

                        dateError.textContent = "";
                    }
                },


                onMonthChange: (sel, str, inst) => {
                    if (sel.length === 2) highlightFridays(inst, sel[0], sel[1]);
                },
                onYearChange: (sel, str, inst) => {
                    if (sel.length === 2) highlightFridays(inst, sel[0], sel[1]);
                }
            });

            function highlightFridays(instance, start, end) {
                setTimeout(() => {
                    instance.calendarContainer.querySelectorAll(".flatpickr-day").forEach(el => {
                        const date = el.dateObj;
                        if (date >= start && date <= end) {
                            if (date.getDay() === 5) {
                                el.classList.add("friday-highlight");
                                el.classList.remove("flatpickr-disabled");
                            } else {
                                el.classList.add("flatpickr-disabled");
                                el.classList.remove("friday-highlight");
                            }
                        } else {
                            el.classList.remove("friday-highlight");
                            el.classList.remove("flatpickr-disabled");
                        }
                    });
                });
            }

            function fmt(d) {
                const pad = n => String(n).padStart(2, '0');
                return `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
            }

            // ✅ Friday Form Validation (NEW)
            form.addEventListener("submit", function(e) {
                e.preventDefault();

                const nameInput = form.querySelector('#name-friday');
                const emailInput = form.querySelector('#email-friday');
                const amount = form.querySelector('input[name="amount"]');
                const giftAidCheckbox = form.querySelector('#gift-aid-friday');
                const addressInput = form.querySelector('#address-friday');

                const nameError = form.querySelector('#error-name-friday');
                const emailError = form.querySelector('#error-email-friday');
                const amountError = form.querySelector('#error-amount-friday');
                const dateError = form.querySelector('#error-date-friday');
                const addressError = form.querySelector('#error-address-friday');

                let valid = true;

                // ✅ Name Validation
                if (!nameInput.value.trim()) {
                    nameError.textContent = 'Please enter your full name.';
                    valid = false;
                } else if (nameInput.value.trim().length < 3) {
                    nameError.textContent = 'Name must be at least 3 characters long.';
                    valid = false;
                } else {
                    nameError.textContent = '';
                }

                // ✅ Email Validation
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailInput.value.trim()) {
                    emailError.textContent = 'Please enter your email address.';
                    valid = false;
                } else if (!emailPattern.test(emailInput.value.trim())) {
                    emailError.textContent = 'Please enter a valid email format.';
                    valid = false;
                } else {
                    emailError.textContent = '';
                }

                // ✅ Floating amount allowed (any decimal > 0)
                if (!amount.value || parseFloat(amount.value) <= 0) {
                    amountError.textContent = 'Please enter a valid amount (greater than 0).';
                    valid = false;
                } else {
                    amountError.textContent = '';
                }


                // ✅ Date range check
                if (!startEl.value || !endEl.value) {
                    dateError.textContent = 'Please select a valid date range.';
                    valid = false;
                } else {
                    dateError.textContent = '';
                }

                // ✅ Gift Aid check
                if (giftAidCheckbox.checked && addressInput.value.trim() === '') {
                    addressError.textContent = 'Please enter your address for Gift Aid.';
                    valid = false;
                } else {
                    addressError.textContent = '';
                }

                if (!valid) e.preventDefault();
            });
        }

        // ------------------------------
        // ✅ Special Donations Form Validation
        // ------------------------------
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById("form-monthly");
            if (!form) return;

            const nameInput = form.querySelector('#name-monthly');
            const emailInput = form.querySelector('#email-monthly');
            const amountInput = form.querySelector('#pay-amount');
            const specialSelect = form.querySelector('#pay-special');
            const giftAidCheckbox = form.querySelector('#gift-aid-monthly');
            const addressInput = form.querySelector('#address-monthly');

            const nameError = form.querySelector('#error-name-monthly');
            const emailError = form.querySelector('#error-email-monthly');
            const amountError = form.querySelector('#error-amount-special');
            const productError = form.querySelector('#error-product-special');
            const addressError = form.querySelector('#error-address-special');

            form.addEventListener("submit", function(e) {
                e.preventDefault();
                let valid = true;

                // ✅ Name
                if (!nameInput.value.trim()) {
                    nameError.textContent = 'Please enter your full name.';
                    valid = false;
                } else if (nameInput.value.trim().length < 3) {
                    nameError.textContent = 'Name must be at least 3 characters long.';
                    valid = false;
                } else {
                    nameError.textContent = '';
                }

                // ✅ Email
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailInput.value.trim()) {
                    emailError.textContent = 'Please enter your email address.';
                    valid = false;
                } else if (!emailPattern.test(emailInput.value.trim())) {
                    emailError.textContent = 'Please enter a valid email format.';
                    valid = false;
                } else {
                    emailError.textContent = '';
                }

                // ✅ Special product selection
                if (!specialSelect.value) {
                    productError.textContent = 'Please select a special donation option.';
                    valid = false;
                } else {
                    productError.textContent = '';
                }

                // ✅ Amount
                if (!amountInput.value || parseFloat(amountInput.value) < 1) {
                    amountError.textContent = 'Please enter a valid amount.';
                    valid = false;
                } else {
                    amountError.textContent = '';
                }

                // ✅ Gift Aid
                if (giftAidCheckbox.checked && addressInput.value.trim() === '') {
                    addressError.textContent = 'Please enter your address for Gift Aid.';
                    valid = false;
                } else {
                    addressError.textContent = '';
                }

                if (!valid) e.preventDefault();
            });
        });

        // ------------------------------
        // ✅ INIT Flatpickr
        // ------------------------------
        document.addEventListener("DOMContentLoaded", function() {
            attachRangePickerWithType("date-range-daily", "start_date-daily", "cancellation-daily", "type-daily",
                "form-daily");
            attachRangePickerFridays("date-range-friday", "start_date-friday", "cancellation-friday",
                "form-friday");
        });

        // ------------------------------
        // ✅ Stripe (Original Working Version)
        // ------------------------------
        document.addEventListener("DOMContentLoaded", function() {
            const stripe = Stripe("{{ config('services.stripe.key') }}");

            function setupStripeForm(formId, elementId, errorId) {
                const form = document.getElementById(formId);
                if (!form) return;

                // Create card element
                const elements = stripe.elements();
                const card = elements.create("card", {
                    style: {
                        base: {
                            color: "#32325d",
                            fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                            fontSmoothing: "antialiased",
                            fontSize: "16px",
                            "::placeholder": {
                                color: "#aab7c4"
                            }
                        },
                        invalid: {
                            color: "#fa755a",
                            iconColor: "#fa755a"
                        }
                    },
                    hidePostalCode: true,
                    disableLink: true
                });
                card.mount(`#${elementId}`);

                // Handle validation errors
                card.on("change", function(event) {
                    const displayError = document.getElementById(errorId);
                    if (event.error) {
                        displayError.textContent = event.error.message;
                    } else {
                        displayError.textContent = "";
                    }
                });

                // Handle form submit
                // Handle form submit
                form.addEventListener("submit", async function(event) {
                    event.preventDefault();

                    const displayError = document.getElementById(errorId);

                    // ✅ Check if any visible error spans already have messages (from your custom validation)
                    const errors = form.querySelectorAll("span[id^='error-']");
                    let hasError = false;
                    errors.forEach((el) => {
                        if (el.textContent.trim() !== "") hasError = true;
                    });

                    // ✅ Also ensure required basic fields exist before creating a Stripe token
                    const nameField = form.querySelector("input[name='name']");
                    const emailField = form.querySelector("input[name='email']");
                    const amountField = form.querySelector("input[name='amount']");

                    if (!nameField?.value.trim() || !emailField?.value.trim() || !amountField?.value
                        .trim()) {
                        hasError = true;
                    }

                    // ✅ NEW: Check if card field has any value (Stripe Element empty check)
                    // Stripe doesn’t expose value directly, so we detect if Element is "complete"
                    const elementComplete = card._complete === true;
                    if (!elementComplete) {
                        displayError.textContent = "Please enter your card details.";
                        hasError = true;
                    } else {
                        displayError.textContent = "";
                    }

                    if (hasError) {
                        form.querySelector("button[type=submit]").disabled = false;
                        return;
                    }

                    // ✅ Proceed only if form is valid
                    form.querySelector("button[type=submit]").disabled = true;

                    const {
                        token,
                        error
                    } = await stripe.createToken(card);

                    if (error) {
                        displayError.textContent = error.message;
                        form.querySelector("button[type=submit]").disabled = false;
                    } else {
                        displayError.textContent = "";
                        const hiddenInput = document.createElement("input");
                        hiddenInput.setAttribute("type", "hidden");
                        hiddenInput.setAttribute("name", "stripeToken");
                        hiddenInput.setAttribute("value", token.id);
                        form.appendChild(hiddenInput);

                        // ✅ Double-check no other errors before submitting
                        let stillValid = true;
                        errors.forEach((el) => {
                            if (el.textContent.trim() !== "") stillValid = false;
                        });

                        if (stillValid) form.submit();
                    }
                });
            }

            setupStripeForm("form-daily", "card-element", "card-errors");
            setupStripeForm("form-friday", "card-element-friday", "card-errors-friday");
            setupStripeForm("form-monthly", "card-element-monthly", "card-errors-monthly");
        });

        // ------------------------------
        // ✅ Gift Aid Checkbox Toggle (Updated)
        // ------------------------------
        document.querySelectorAll('input[type="checkbox"][name="gift_aid"]').forEach((checkbox) => {
            checkbox.addEventListener('change', function() {
                const targetId = this.getAttribute('data-target');
                const addressInput = document.getElementById(targetId);
                const errorSpan = document.getElementById(`error-${targetId}`);

                if (this.checked) {
                    addressInput.style.display = 'block';
                    addressInput.setAttribute('placeholder', 'Enter your address');
                } else {
                    addressInput.style.display = 'none';
                    addressInput.value = '';
                    if (errorSpan) {
                        errorSpan.textContent = '';
                    }
                }
            });
        });

        // 🔄 Preserve active tab between redirects
        document.addEventListener("DOMContentLoaded", () => {
            const forms = document.querySelectorAll("form");
            const active = "{{ session('active_tab') }}";
            if (active) openTab({
                currentTarget: document.querySelector(`.tab-btn[onclick*='${active}']`)
            }, active);
            forms.forEach(f => f.addEventListener("submit", () => {
                f.querySelector("input[name='active_tab']").value =
                    document.querySelector(".tab-panel.active")?.id || '';
            }));
        });
    </script>
    {{-- Jquery Script --}}
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
                    $('#currency-monthly').val('GBP');
                }

                const updateAmount = () => {
                    const s = $('#pay-special').find(':selected'),
                        p = parseFloat(s.data('price')) || 0,
                        c = $('#currency-monthly').val(),
                        r = rates?.[c];
                    if (!p || !c) return $('#pay-amount').val('');
                    $('#pay-amount').val(c === 'GBP' ? p.toFixed(2) : (p * r).toFixed(2));
                };

                if (!$('#pay-special').data('bound')) {
                    $('#pay-special, #currency-monthly')
                        .on('change', updateAmount)
                        .data('bound', true);
                }

                fetchRates(updateAmount);
            };

            if ($('#special').hasClass('active')) initSpecial();

            $('.tab-btn').on('click', function() {
                if (this.outerHTML.includes("'special'")) {
                    setTimeout(() => {
                        if (!specialInitialized) initSpecial();
                    }, 200);
                }
            });
        });
    </script>
@endsection
