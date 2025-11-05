<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@2/dist/iconify-icon.min.js"></script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
    <link rel="shortcut icon" href="/images/favicon.png">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make a Difference | GiftAidly</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f9c001 0%, #1d43ab 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Animated Top Bar */
        .animated-bar {
            height: 8px;
            background: linear-gradient(90deg, #f9c001, #1d43ab, #f9c001, #96ceb4, #1d43ab);
            background-size: 300% 100%;
            animation: gradient-flow 5s ease infinite;
            position: relative;
            overflow: hidden;
        }

        .animated-bar::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            animation: shine 2s infinite;
        }

        @keyframes gradient-flow {
            0% {
                background-position: 0% 50%
            }

            50% {
                background-position: 100% 50%
            }

            100% {
                background-position: 0% 50%
            }
        }

        @keyframes shine {
            0% {
                left: -100%
            }

            100% {
                left: 100%
            }
        }

        /* Banner Section */
        .banner {
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.8) 0%, rgba(0, 0, 0, 0.4) 100%),
                url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 300"><defs><pattern id="grid" width="50" height="50" patternUnits="userSpaceOnUse"><path d="M 50 0 L 0 0 0 50" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grid)"/></svg>');
            color: white;
            text-align: center;
            padding: 40px 20px;
            position: relative;
        }

        .banner::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 30% 70%, #1d43ab, transparent),
                radial-gradient(circle at 70% 30%, #f9c001, transparent);
            pointer-events: none;
        }

        .banner-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            margin: 0 auto;
        }

        .banner h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            background: linear-gradient(45deg, #f9c001);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: glow 2s ease-in-out infinite alternate;
        }

        .banner p {
            font-size: 1.3rem;
            opacity: 0.9;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        @keyframes glow {
            from {
                filter: drop-shadow(0 0 20px rgba(255, 107, 107, 0.3))
            }

            to {
                filter: drop-shadow(0 0 30px rgba(78, 205, 196, 0.4))
            }
        }

        .stat-item {
            text-align: center;
            padding: 20px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            min-width: 150px;
        }



        .stat-label {
            font-size: 0.9rem;
            opacity: 0.8;
            margin-top: 5px;
        }

        /* Professional Tab Section */
        .tab-container {
            width: 100%;
            max-width: 1200px;
            margin: -40px auto 0;
            position: relative;
            z-index: 10;
            padding: 0 20px;
        }

        .tab-header {
            display: flex;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px 20px 0 0;
            overflow: hidden;
            box-shadow: 0 -10px 30px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .tab-btn {
            flex: 1;
            padding: 25px 20px;
            background: transparent;
            border: none;
            font-size: 1.1rem;
            font-weight: 600;
            color: black;
            cursor: pointer;
            position: relative;
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .tab-btn::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, #1d43ab, #f9c001);
            transform: translateY(100%);
            transition: transform 0.3s ease;
            z-index: -1;
        }

        .tab-btn.active::before {
            transform: translateY(0);
        }

        .tab-btn.active {
            color: white;
            transform: translateY(-5px);
        }

        .tab-btn:not(.active):hover {
            color: #667eea;
            transform: translateY(-1px);
        }

        .tab-content {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 0 0 20px 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-top: none;
            min-height: 500px;
        }

        .tab-panel {
            display: none;
            animation: fadeInUp 0.5s ease;
        }

        .tab-panel.active {
            display: block;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px)
            }

            to {
                opacity: 1;
                transform: translateY(0)
            }
        }

        /* Advanced Cards */
        .donation-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9ff 100%);
            border-radius: 25px;
            padding: 40px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1), 0 0 0 1px rgba(255, 255, 255, 0.5) inset;
            transition: all 0.4s ease;
        }

        .donation-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #1d43ab, #f9c001, #45b7d1);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }

        .donation-card:hover::before {
            transform: scaleX(1);
        }

        .donation-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(255, 255, 255, 0.8) inset;
        }

        .card-header {
            text-align: center;
            margin-bottom: 35px;
        }

        .card-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #1d43ab, #f9c001);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .card-icon::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transform: rotate(45deg);
            animation: iconShine 3s infinite;
        }

        @keyframes iconShine {
            0% {
                transform: translate(-100%, -100%) rotate(45deg)
            }

            100% {
                transform: translate(100%, 100%) rotate(45deg)
            }
        }

        .card-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }

        .card-subtitle {
            color: #666;
            font-size: 1rem;
            line-height: 1.5;
        }

        .amount-selection {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
            margin: 30px 0;
        }

        .amount-btn {
            padding: 15px 10px;
            border: 2px solid #e0e0e0;
            border-radius: 15px;
            background: white;
            font-size: 1rem;
            font-weight: 600;
            color: #666;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .amount-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.1), transparent);
            transition: left 0.5s ease;
        }

        .amount-btn:hover::before {
            left: 100%;
        }

        .amount-btn:hover,
        .amount-btn.selected {
            border-color: #667eea;
            color: #667eea;
            transform: scale(1.05);
        }

        .amount-btn.selected {
            background: linear-gradient(135deg, #1d43ab, #f9c001);
            color: white;
        }

        .custom-amount {
            margin: 20px 0;
        }

        .custom-amount input {
            width: 100%;
            padding: 18px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 15px;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            background: white;
        }

        .custom-amount input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .donate-btn {
            width: 100%;
            padding: 20px;
            background: linear-gradient(135deg, #1d43ab 0%, #f9c001 100%);
            color: white;
            border: none;
            border-radius: 15px;
            font-size: 1.2rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .donate-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .donate-btn:hover::before {
            left: 100%;
        }

        .donate-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px #667eea66;
        }

        .donate-btn:active {
            transform: translateY(-1px);
        }

        /* ------- minimal form grid to match existing look ------- */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
            margin: 20px 0 10px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
        }

        .text-input {
            width: 100%;
            padding: 18px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 15px;
            font-size: 1.05rem;
            background: white;
            transition: all 0.3s ease;
        }

        .select-input {
            width: 100%;
            padding: 18px 40px 18px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 15px;
            font-size: 1.05rem;
            background: white;
            transition: all 0.3s ease;

            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;

            /* custom arrow */
            background-image: url("data:image/svg+xml;utf8,<svg fill='black' height='20' viewBox='0 0 24 24' width='20' xmlns='http://www.w3.org/2000/svg'><path d='M7 10l5 5 5-5z'/></svg>");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 18px;
        }

        .text-input:focus,
        .select-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .banner h1 {
                font-size: 2.5rem;
            }

            .tab-btn {
                padding: 20px 15px;
                font-size: 1rem;
            }

            .tab-content {
                padding: 30px 20px;
            }

            .donation-card {
                padding: 30px 20px;
            }

            .amount-selection {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .banner h1 {
                font-size: 2rem;
            }

            .tab-header {
                flex-direction: column;
            }

            .amount-selection {
                grid-template-columns: 1fr;
            }
        }

        .friday-highlight {
            background: #3b82f6 !important;
            /* blue circle */
            color: #fff !important;
            border-radius: 50% !important;
            font-weight: bold;
        }

        .flatpickr-disabled {
            opacity: 0.4 !important;
            pointer-events: none !important;
        }
    </style>
    @livewireStyles

</head>

<body>
    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
    @livewireScripts
    {{-- Stripe script --}}
    <script src="https://js.stripe.com/v3/"></script>
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
    </script>
    {{-- Jquery Script --}}
    <script>
        $(function() {
            let rates, fetched = false;
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
                if (!$('#special').hasClass('active')) return;

                @if(!$userCurrency)
                $('#currency-monthly').val('GBP');
                @endif

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
                if (this.outerHTML.includes("'special'"))
                    setTimeout(initSpecial, 200);
            });
        });
    </script>
</body>

</html>