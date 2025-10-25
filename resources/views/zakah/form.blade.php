@extends('layouts.base', ['subtitle' => 'Zakat Payment'])

@section('body-attribuet')
class="authentication-bg"
@endsection
@section('content')
@include('layouts.partials.alert')

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow-sm rounded-3">
                <div class="card-body">
                    <h3 class="text-center my-4 fw-bold">Zakat Payment</h3>



                    {{-- Zakat Form --}}
                    <form action="{{ route('zakat.process') }}" method="POST" id="zakatForm">

                        @csrf
                        @php
                        // Normalize any symbol to valid ISO code
                        $currency = match($currency) {
                        '£' => 'GBP',
                        '$' => 'USD',
                        '€' => 'EUR',
                        };
                        @endphp

                        <input type="hidden" name="currency" value="{{ $currency }}">


                        <input type="hidden" name="zakat" value="{{ $zakat }}">

                        <div class="mb-3">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Enter your full name" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                        </div>

                        {{-- Zakat Information --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Currency:</label>
                            <input type="text" class="form-control" value="{{ strtoupper($currency) }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Zakat Amount:</label>
                            <input type="text" class="form-control" value="{{ number_format($zakat, 2) }}" readonly>
                        </div>

                        {{-- Stripe Card Field --}}
                        <div class="mb-3">
                            <label class="form-label">Card Details <span class="text-danger">*</span></label>
                            <div id="card-element" class="form-control p-2" style="height:auto;"></div>
                            <small id="card-errors" class="text-danger mt-2 d-block"></small>
                        </div>

                        <input type="hidden" name="stripeToken" id="stripeToken">
                        <div class="d-grid">
                            <button type="submit" id="submitBtn" style="background: linear-gradient(45deg, #1d43ab, #94740dff);" class="btn btn-lg fw-medium text-light">
                                <span id="btnText">Pay Now</span>
                                <span id="btnLoader" class="spinner-border spinner-border-sm d-none"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
{{-- Stripe JS --}}
<script src="https://js.stripe.com/v3/"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const stripe = Stripe("{{ env('STRIPE_KEY') }}");
        const elements = stripe.elements();

        const card = elements.create('card', {
            hidePostalCode: true,
            style: {
                base: {
                    fontSize: '16px',
                    color: '#32325d',
                    '::placeholder': {
                        color: '#aab7c4'
                    },
                },
                invalid: {
                    color: '#fa755a',
                    iconColor: '#fa755a'
                },
            },
        });
        card.mount('#card-element');

        const form = document.getElementById('zakatForm');
        const errorDiv = document.getElementById('card-errors');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnLoader = document.getElementById('btnLoader');

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            btnText.textContent = 'Processing...';
            btnLoader.classList.remove('d-none');
            submitBtn.disabled = true;

            const {
                token,
                error
            } = await stripe.createToken(card);

            if (error) {
                errorDiv.textContent = error.message;
                btnText.textContent = 'Pay Now';
                btnLoader.classList.add('d-none');
                submitBtn.disabled = false;
            } else {
                errorDiv.textContent = '';
                document.getElementById('stripeToken').value = token.id;
                form.submit();
            }
        });
    });
</script>
@endsection