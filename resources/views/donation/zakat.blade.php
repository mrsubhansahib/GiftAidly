@extends('layouts.master-frontend')
@section('content')
@include('layouts.partials.alert')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow-sm rounded-3">
                <div class="card-body">

                    <h3 class="text-center my-4 fw-bold">Zakat Payment</h3>

                    {{-- Livewire component only renders user fields --}}
                    @livewire('zakat', ['currency' => $currency, 'amount' => $amount])

                    {{-- STRIPE PAYMENT FIELD --}}
                    <div class="mt-4 card-input" hidden wire:ignore>
                        <label class="form-label">Card Details <span class="text-danger">*</span></label>
                        <div id="payment-element" class="form-control p-2"></div>
                        <small id="card-errors" class="text-danger mt-2 d-block"></small>
                    </div>

                    {{-- BUTTON --}}
                    <button id="submitBtn"
                        style="background: linear-gradient(45deg, #1d43ab, #94740dff);"
                        class="btn text-light w-100 mt-3" disabled>

                        <span id="btnText">Pay Now</span>
                        <span id="btnLoader" class="spinner-border spinner-border-sm d-none"></span>
                    </button>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://js.stripe.com/v3/"></script>

<script>
    let stripe, elements;

    window.addEventListener("stripe-init", function(event) {

        document.querySelector(".card-input").hidden = false;

        stripe = Stripe("{{ env('STRIPE_KEY') }}");
        elements = stripe.elements({
            clientSecret: event.detail.clientSecret
        });

        const paymentElement = elements.create("payment");
        paymentElement.mount("#payment-element");

        document.getElementById("submitBtn").disabled = false;
    });


    document.getElementById("submitBtn").addEventListener("click", async function(e) {
        e.preventDefault();

        const btnText = document.getElementById("btnText");
        const btnLoader = document.getElementById("btnLoader");
        const submitBtn = document.getElementById("submitBtn");

        // Disable button + show loader
        submitBtn.disabled = true;
        btnLoader.classList.remove("d-none");
        btnText.innerHTML = "Processing...";

        const result = await stripe.confirmPayment({
            elements,
            confirmParams: {
                return_url: "{{ route('zakat.redirect') }}"
            }
        });

        if (result.error) {
            document.getElementById("card-errors").innerText = result.error.message;

            // Restore button
            submitBtn.disabled = false;
            btnLoader.classList.add("d-none");
            btnText.innerHTML = "Pay Now";
        }
    });
</script>
@endsection