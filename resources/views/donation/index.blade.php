<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/iconify-icon@2/dist/iconify-icon.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Hub - Make a Difference</title>
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
            padding: 80px 20px;
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

        .donation-slider {
            position: relative;
            max-width: 100%;
            overflow: hidden;
            margin: 20px auto;
        }

        .slider-wrapper {
            overflow: hidden;
            width: 100%;
        }

        .slider-track {
            display: flex;
            transition: transform 0.6s ease-in-out;
            will-change: transform;
        }

        .slider-card {
            min-width: 250px;
            max-width: 250px;
            margin: 0 10px;
            background: white;
            border-radius: 12px;
            text-align: center;
            padding: 15px;
            color: black;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }

        .slider-card h5 {
            color: #111;
            /* heading dark */
        }

        .slider-card p {
            color: #555;
            /* price grey */
        }

        .slider-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .btn-donate {
            background: linear-gradient(135deg, #1d43ab, #f9c001);
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-donate:hover {
            transform: scale(1.05);
        }

        .slider-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.4);
            border: none;
            color: #fff;
            font-size: 24px;
            cursor: pointer;
            padding: 6px 15px;
            border-radius: 50%;
            transition: background 0.3s;
            z-index: 10;
        }

        .slider-btn:hover {
            background: rgba(0, 0, 0, 0.8);
        }

        .slider-btn.prev {
            left: 10px;
        }

        .slider-btn.next {
            right: 10px;
        }
    </style>
</head>

<body>
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
                <iconify-icon icon="mdi:view-dashboard" class="fs-20 align-middle" style="margin-right: 6px;"></iconify-icon>

                Dashboard
            </a>
        </div>
        <div class="floating-elements"></div>
        <div class="banner-content">
            <h1>Make a Difference Today</h1>
            <p>Join thousands of donors who are changing lives around the world. Every contribution matters, every donation counts.</p>
        </div>
        <!-- Custom Donation Slider -->
        <div class="donation-slider mt-5">
            <div class="slider-wrapper">
                <div class="slider-track">

                    <!-- Card 1 -->
                    <div class="slider-card">
                        <img src="/images/donation-food/istockphoto-1224414210-612x612.jpg" alt="Food Pack">
                        <h5>Food Pack</h5>
                        <p>£50</p>
                        <button class="btn-donate">Donate Now</button>
                    </div>

                    <!-- Card 2 -->
                    <div class="slider-card">
                        <img src="/images/donation-food/istockphoto-1224414210-612x612.jpg" alt="Water Pump">
                        <h5>Water Pump</h5>
                        <p>£100</p>
                        <button class="btn-donate">Donate Now</button>
                    </div>
                    
                    <!-- Card 2 -->
                    <div class="slider-card">
                        <img src="/images/donation-food/istockphoto-1224414210-612x612.jpg" alt="Water Pump">
                        <h5>Water Pump</h5>
                        <p>£100</p>
                        <button class="btn-donate">Donate Now</button>
                    </div>
                    <!-- Card 2 -->
                    <div class="slider-card">
                        <img src="/images/donation-food/istockphoto-1224414210-612x612.jpg" alt="Water Pump">
                        <h5>Water Pump</h5>
                        <p>£100</p>
                        <button class="btn-donate">Donate Now</button>
                    </div>
                    <!-- Card 2 -->
                    <div class="slider-card">
                        <img src="/images/donation-food/istockphoto-1224414210-612x612.jpg" alt="Water Pump">
                        <h5>Water Pump</h5>
                        <p>£100</p>
                        <button class="btn-donate">Donate Now</button>
                    </div>
                    <!-- Card 2 -->
                    <div class="slider-card">
                        <img src="/images/donation-food/istockphoto-1224414210-612x612.jpg" alt="Water Pump">
                        <h5>Water Pump</h5>
                        <p>£100</p>
                        <button class="btn-donate">Donate Now</button>
                    </div>
                    <!-- Card 2 -->
                    <div class="slider-card">
                        <img src="/images/donation-food/istockphoto-1224414210-612x612.jpg" alt="Water Pump">
                        <h5>Water Pump</h5>
                        <p>£100</p>
                        <button class="btn-donate">Donate Now</button>
                    </div>
                    <!-- Card 2 -->
                    <div class="slider-card">
                        <img src="/images/donation-food/istockphoto-1224414210-612x612.jpg" alt="Water Pump">
                        <h5>Water Pump</h5>
                        <p>£100</p>
                        <button class="btn-donate">Donate Now</button>
                    </div>
                    <!-- Card 2 -->
                    <div class="slider-card">
                        <img src="/images/donation-food/istockphoto-1224414210-612x612.jpg" alt="Water Pump">
                        <h5>Water Pump</h5>
                        <p>£100</p>
                        <button class="btn-donate">Donate Now</button>
                    </div>
                    <!-- Card 2 -->
                    <div class="slider-card">
                        <img src="/images/donation-food/istockphoto-1224414210-612x612.jpg" alt="Water Pump">
                        <h5>Water Pump</h5>
                        <p>£100</p>
                        <button class="btn-donate">Donate Now</button>
                    </div>
                </div>
            </div>

            <!-- Controls -->
            <button class="slider-btn prev">❮</button>
            <button class="slider-btn next">❯</button>
        </div>


    </section>

    <!-- Professional Tab Section -->
    <div class="tab-container">
        <div class="tab-header">
            <button class="tab-btn active" onclick="openTab(event, 'daily-weekly')">Daily/Weekly</button>
            <button class="tab-btn" onclick="openTab(event, 'friday')">Friday Special</button>
            <button class="tab-btn" onclick="openTab(event, 'monthly')">Monthly</button>
        </div>

        <div class="tab-content">
            <!-- Daily/Weekly Tab -->
            <div id="daily-weekly" class="tab-panel active">
                <div class="donation-card">
                    <div class="card-header">
                        <div class="card-icon">
                            <span class="iconify" data-icon="mdi:calendar-clock" data-width="40" data-height="40"></span>
                        </div>
                        <h2 class="card-title">Daily & Weekly Donations</h2>
                        <p class="card-subtitle">Make consistent impact with regular contributions. Small amounts, big difference.</p>
                    </div>

                    <form id="form-daily" action="#" method="POST">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="currency-daily">Currency</label>
                                <select name="currency" id="currency-daily" class="select-input">
                                    <option value="gbp">£</option>
                                    <option value="usd">$</option>
                                    <option value="eur">€</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="amount-daily">Amount</label>
                                <input type="number" name="amount" id="amount-daily" class="text-input" min="1" />
                            </div>

                            <div class="form-group">
                                <label for="type-daily">Type</label>
                                <select name="type" id="type-daily" class="select-input">
                                    <option value="day">Daily</option>
                                    <option value="week">Weekly</option>
                                </select>
                            </div>

                            <!-- Single Range Picker -->
                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label for="date-range-daily">Select Date Range</label>
                                <input type="text" id="date-range-daily" class="text-input" placeholder="Pick start and end dates" />
                                <input type="hidden" name="start_date" id="start_date-daily" />
                                <input type="hidden" name="cancellation" id="cancellation-daily" />
                            </div>

                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label for="card-element">Card Details</label>
                                <div id="card-element" class="text-input"></div>
                                <div id="card-errors" role="alert" style="color: red; margin-top: 5px;"></div>
                            </div>
                        </div>

                        <button type="submit" class="donate-btn">Donate Now</button>
                    </form>

                </div>
            </div>

            <!-- Friday Tab -->
            <div id="friday" class="tab-panel">
                <div class="donation-card">
                    <div class="card-header">
                        <div class="card-icon">
                            <span class="iconify" data-icon="mdi:mosque" data-width="40" data-height="40"></span>
                        </div>
                        <h2 class="card-title">Friday Special Donation</h2>
                        <p class="card-subtitle">Make your Fridays more meaningful with special charitable contributions.</p>
                    </div>

                    <!-- Friday -->
                    <form id="form-friday" action="#" method="POST">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="currency-friday">Currency</label>
                                <select name="currency" id="currency-friday" class="select-input">
                                    <option value="gbp">£</option>
                                    <option value="usd">$</option>
                                    <option value="eur">€</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="amount-friday">Amount</label>
                                <input type="number" name="amount" id="amount-friday" class="text-input" min="1" />
                            </div>

                            <div class="form-group">
                                <label for="type-friday">Type</label>
                                <input type="text" name="type" id="type-friday" value="Friday" class="text-input" min="1" readonly />
                            </div>

                            <!-- Single Range Picker -->
                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label for="date-range-friday">Select Date Range</label>
                                <input type="text" id="date-range-friday" class="text-input" placeholder="Pick start and end dates" />
                                <input type="hidden" name="start_date" id="start_date-friday" />
                                <input type="hidden" name="cancellation" id="cancellation-friday" />
                            </div>

                            <!-- Stripe Card Element -->
                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label for="card-element-friday">Card Details</label>
                                <div id="card-element-friday" class="text-input"></div>
                                <div id="card-errors-friday" role="alert" style="color: red; margin-top: 5px;"></div>
                            </div>

                        </div>

                        <button type="submit" class="donate-btn">Friday Donation</button>
                    </form>

                </div>
            </div>

            <!-- Monthly Tab -->
            <div id="monthly" class="tab-panel">
                <div class="donation-card">
                    <div class="card-header">
                        <div class="card-icon">
                            <span class="iconify" data-icon="mdi:chart-line" data-width="40" data-height="40"></span>
                        </div>
                        <h2 class="card-title">Monthly Donation Plan</h2>
                        <p class="card-subtitle">Sustain long-term change with monthly contributions. Maximum impact, consistent support.</p>
                    </div>

                    <!-- Monthly -->
                    <form id="form-monthly" action="#" method="POST">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="currency-monthly">Currency</label>
                                <select name="currency" id="currency-monthly" class="select-input">
                                    <option value="gbp">£</option>
                                    <option value="usd">$</option>
                                    <option value="eur">€</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="amount-monthly">Amount</label>
                                <input type="number" name="amount" id="amount-monthly" class="text-input" min="1" />
                            </div>

                            <div class="form-group">
                                <label for="type-monthly">Type</label>
                                <input type="text" id="type-monthly" value="Monthly" name="type" class="text-input" readonly />
                            </div>

                            <!-- Single Range Picker -->
                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label for="date-range-monthly">Select Date Range</label>
                                <input type="text" id="date-range-monthly" class="text-input" placeholder="Pick start and end dates" />
                                <input type="hidden" name="start_date" id="start_date-monthly" />
                                <input type="hidden" name="cancellation" id="cancellation-monthly" />
                            </div>

                            <!-- Stripe Card Element -->
                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label for="card-element-monthly">Card Details</label>
                                <div id="card-element-monthly" class="text-input"></div>
                                <div id="card-errors-monthly" role="alert" style="color: red; margin-top: 5px;"></div>
                            </div>
                        </div>

                        <button type="submit" class="donate-btn">Monthly Commitment</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const track = document.querySelector(".slider-track");
            const prevBtn = document.querySelector(".slider-btn.prev");
            const nextBtn = document.querySelector(".slider-btn.next");
            const originalCards = Array.from(document.querySelectorAll(".slider-card"));

            let cardWidth, index, isTransitioning, totalCards, allCards;
            let sliderEnabled = false;

            function initSlider() {
                cardWidth = originalCards[0].offsetWidth + 20;
                const wrapperWidth = document.querySelector(".slider-wrapper").offsetWidth;
                const trackWidth = originalCards.length * cardWidth;

                // Reset track
                track.innerHTML = "";
                index = 0;
                isTransitioning = false;

                if (trackWidth <= wrapperWidth) {
                    // Slider disable
                    sliderEnabled = false;
                    prevBtn.style.display = "none";
                    nextBtn.style.display = "none";
                    originalCards.forEach(card => track.appendChild(card.cloneNode(true)));
                    track.style.transform = "translateX(0)";
                    track.style.transition = "none";
                } else {
                    // Slider enable with infinite loop
                    sliderEnabled = true;
                    prevBtn.style.display = "block";
                    nextBtn.style.display = "block";

                    // Clone first & last few cards
                    const visibleCards = Math.floor(wrapperWidth / cardWidth);
                    const prepend = originalCards.slice(-visibleCards).map(c => c.cloneNode(true));
                    const append = originalCards.slice(0, visibleCards).map(c => c.cloneNode(true));

                    prepend.forEach(c => track.appendChild(c));
                    originalCards.forEach(c => track.appendChild(c.cloneNode(true)));
                    append.forEach(c => track.appendChild(c));

                    allCards = Array.from(track.querySelectorAll(".slider-card"));
                    totalCards = allCards.length;

                    index = visibleCards; // start after prepended clones
                    track.style.transition = "none";
                    track.style.transform = `translateX(-${index * cardWidth}px)`;
                }
            }

            function slideTo(newIndex) {
                track.style.transition = "transform 0.6s ease-in-out";
                index = newIndex;
                track.style.transform = `translateX(-${index * cardWidth}px)`;
            }

            function jumpTo(newIndex) {
                track.style.transition = "none";
                index = newIndex;
                track.style.transform = `translateX(-${index * cardWidth}px)`;
            }

            nextBtn.addEventListener("click", () => {
                if (!sliderEnabled || isTransitioning) return;
                isTransitioning = true;
                index++;
                slideTo(index);
            });

            prevBtn.addEventListener("click", () => {
                if (!sliderEnabled || isTransitioning) return;
                isTransitioning = true;
                index--;
                slideTo(index);
            });

            track.addEventListener("transitionend", () => {
                if (!sliderEnabled) return;

                const wrapperWidth = document.querySelector(".slider-wrapper").offsetWidth;
                const visibleCards = Math.floor(wrapperWidth / cardWidth);

                if (index >= totalCards - visibleCards) {
                    jumpTo(visibleCards);
                }
                if (index < visibleCards) {
                    jumpTo(totalCards - visibleCards * 2);
                }
                isTransitioning = false;
            });

            // Auto Slide
            setInterval(() => {
                if (sliderEnabled && !isTransitioning) {
                    index++;
                    slideTo(index);
                }
            }, 4000);

            // Re-init on resize
            window.addEventListener("resize", initSlider);

            // Init on load
            initSlider();
        });
    </script>






    <script>
        function openTab(evt, tabName) {
            var i, tabPanels, tabBtns;
            tabPanels = document.getElementsByClassName("tab-panel");
            for (i = 0; i < tabPanels.length; i++) tabPanels[i].classList.remove("active");
            tabBtns = document.getElementsByClassName("tab-btn");
            for (i = 0; i < tabBtns.length; i++) tabBtns[i].classList.remove("active");
            document.getElementById(tabName).classList.add("active");
            evt.currentTarget.classList.add("active");
        }

        // Card hover animations (unchanged)
        document.querySelectorAll('.donation-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px) scale(1.02)';
            });
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Date -> YYYY-MM-DD (single definition)
        function fmt(d) {
            const pad = n => String(n).padStart(2, '0');
            return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}`;
        }

        // Helpers for monthly picker
        const firstOfMonth = (y, m) => new Date(y, m, 1); // m = 0-based
        const lastOfMonth = (y, m) => new Date(y, m + 1, 0);

        // Generic daily/weekly day-range
        function attachRangePicker(rangeId, startHiddenId, endHiddenId, formId) {
            const rangeEl = document.getElementById(rangeId);
            const startEl = document.getElementById(startHiddenId);
            const endEl = document.getElementById(endHiddenId);

            flatpickr(rangeEl, {
                mode: "range",
                altInput: true,
                altFormat: "F j, Y",
                dateFormat: "Y-m-d",
                minDate: "today",
                onChange(selectedDates) {
                    startEl.value = selectedDates[0] ? fmt(selectedDates[0]) : "";
                    endEl.value = selectedDates[1] ? fmt(selectedDates[1]) : "";
                }
            });

            const form = document.getElementById(formId);
            form.addEventListener('submit', function(e) {
                if (!startEl.value || !endEl.value) {
                    e.preventDefault();
                    alert('Please select a start and end date.');
                }
            });
        }

        // Friday-only day-range
        // Friday: multiple selectable Fridays (today se aage)
        function attachRangePickerFridays(rangeId, startHiddenId, endHiddenId, formId) {
            const rangeEl = document.getElementById(rangeId);
            const startEl = document.getElementById(startHiddenId);
            const endEl = document.getElementById(endHiddenId);
            const form = document.getElementById(formId);
            if (!rangeEl || !startEl || !endEl || !form) return;

            // helper: ensure a single hidden <input name="fridays"> exists
            function ensureFridaysHidden() {
                let h = form.querySelector('input[name="fridays"]');
                if (!h) {
                    h = document.createElement('input');
                    h.type = 'hidden';
                    h.name = 'fridays'; // CSV of all selected Fridays
                    h.id = rangeId + '-fridays';
                    form.appendChild(h);
                }
                return h;
            }

            const fridaysHidden = ensureFridaysHidden();

            flatpickr(rangeEl, {
                mode: "multiple", // <-- multiple dates
                altInput: true,
                altFormat: "F j, Y",
                dateFormat: "Y-m-d",
                minDate: "today",
                // Sirf Friday clickable (0=Sun ... 5=Fri)
                enable: [date => date.getDay() === 5],
                onChange(selectedDates) {
                    // sort just in case
                    selectedDates.sort((a, b) => a - b);

                    // first/last -> start/end hidden
                    startEl.value = selectedDates[0] ? fmt(selectedDates[0]) : "";
                    endEl.value = selectedDates.length ? fmt(selectedDates[selectedDates.length - 1]) : "";

                    // CSV of all selected Fridays for backend
                    fridaysHidden.value = selectedDates.map(d => fmt(d)).join(',');
                }
            });

            // Validation: at least 1 Friday required
            form.addEventListener('submit', function(e) {
                if (!fridaysHidden.value) {
                    e.preventDefault();
                    alert('Please select at least one Friday.');
                }
            });
        }

        // INIT calls (unchanged except Friday uses updated function)
        document.addEventListener('DOMContentLoaded', function() {
            attachRangePicker('date-range-daily', 'start_date-daily', 'cancellation-daily', 'form-daily');
            attachRangePickerFridays('date-range-friday', 'start_date-friday', 'cancellation-friday', 'form-friday');
            attachRangePickerMonths('date-range-monthly', 'start_date-monthly', 'cancellation-monthly', 'form-monthly');
        });

        // Month range picker (maps to first/last day in hidden fields)
        function attachRangePickerMonths(rangeId, startHiddenId, endHiddenId, formId) {
            const rangeEl = document.getElementById(rangeId);
            const startEl = document.getElementById(startHiddenId);
            const endEl = document.getElementById(endHiddenId);

            flatpickr(rangeEl, {
                mode: "range",
                dateFormat: "Y-m",
                altInput: true,
                altFormat: "F Y",
                minDate: "today",
                plugins: [new monthSelectPlugin({
                    shorthand: true,
                    dateFormat: "Y-m",
                    altFormat: "F Y"
                })],
                onChange(selectedDates) {
                    if (selectedDates[0]) {
                        const s = firstOfMonth(selectedDates[0].getFullYear(), selectedDates[0].getMonth());
                        startEl.value = fmt(s); // e.g. 2025-09-01
                    } else {
                        startEl.value = "";
                    }
                    if (selectedDates[1]) {
                        const e = lastOfMonth(selectedDates[1].getFullYear(), selectedDates[1].getMonth());
                        endEl.value = fmt(e); // e.g. 2025-12-31
                    } else {
                        endEl.value = "";
                    }
                }
            });

            const form = document.getElementById(formId);
            form.addEventListener('submit', function(e) {
                if (!startEl.value || !endEl.value) {
                    e.preventDefault();
                    alert('Please select a start and end month.');
                }
            });
        }

        // Initialize all three
        document.addEventListener('DOMContentLoaded', function() {
            // Daily/Weekly: normal day range
            attachRangePicker('date-range-daily', 'start_date-daily', 'cancellation-daily', 'form-daily');

            // Friday: only Fridays selectable
            attachRangePickerFridays('date-range-friday', 'start_date-friday', 'cancellation-friday', 'form-friday');

            // Monthly: month range (maps to first/last day)
            attachRangePickerMonths('date-range-monthly', 'start_date-monthly', 'cancellation-monthly', 'form-monthly');
        });
    </script>

    {{-- Stripe script --}}
    <script src="https://js.stripe.com/v3/"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const stripe = Stripe("{{ config('services.stripe.key') }}");

            function setupStripeCard(formId, elementId, errorId) {
                const elements = stripe.elements();
                const card = elements.create("card", {
                    hidePostalCode: true,
                    style: {
                        base: {
                            fontSize: "16px",
                            color: "#000",
                            fontFamily: "inherit",
                            "::placeholder": {
                                color: "#999"
                            }
                        },
                        invalid: {
                            color: "#e3342f"
                        }
                    }
                });
                card.mount(`#${elementId}`);

                const form = document.getElementById(formId);
                form.addEventListener("submit", async (event) => {
                    event.preventDefault();
                    const {
                        paymentMethod,
                        error
                    } = await stripe.createPaymentMethod({
                        type: "card",
                        card: card,
                        billing_details: {
                            name: "Anonymous Donor"
                        },
                    });

                    if (error) {
                        document.getElementById(errorId).textContent = error.message;
                        return;
                    }

                    const hiddenInput = document.createElement("input");
                    hiddenInput.type = "hidden";
                    hiddenInput.name = "payment_method_id";
                    hiddenInput.value = paymentMethod.id;
                    form.appendChild(hiddenInput);
                    form.submit();
                });
            }

            setupStripeCard("form-daily", "card-element", "card-errors");
            setupStripeCard("form-friday", "card-element-friday", "card-errors-friday");
            setupStripeCard("form-monthly", "card-element-monthly", "card-errors-monthly");
        });
    </script>


</body>

</html>