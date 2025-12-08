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

                        {{-- Stripe Card Section (STATIC - Never rerendered by Livewire) --}}
                        <div class="mt-4 card-input" hidden wire:ignore >
                            <label class="form-label">Card Details <span style="color:red">*</span></label>
                            <div id="payment-element" class="form-control p-2"></div>
                            <small id="card-errors" class="text-danger mt-2 d-block"></small>
                        </div>

                        <button id="submitBtn" class="btn btn-primary w-100 mt-3" disabled>
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
        let stripe;
        let elements;

        window.addEventListener("stripe-init", function(event) {
            document.querySelector(".card-input").removeAttribute("hidden");
            const clientSecret = event.detail.clientSecret;

            stripe = Stripe("{{ env('STRIPE_KEY') }}");

            elements = stripe.elements({
                clientSecret: clientSecret
            });

            const paymentElement = elements.create("payment");

            paymentElement.mount("#payment-element");

            document.getElementById("submitBtn").disabled = false;
        });

        document.getElementById("submitBtn").addEventListener("click", async function(e) {
            e.preventDefault();

            document.getElementById("btnLoader").classList.remove("d-none");
            document.getElementById("btnText").innerHTML = "Processing...";

            const {
                error
            } = await stripe.confirmPayment({
                elements: elements,
                confirmParams: {
                    return_url: "{{ route('zakat.redirect') }}"
                }
            });

            if (error) {
                document.getElementById("card-errors").innerText = error.message;
                document.getElementById("btnLoader").classList.add("d-none");
                document.getElementById("btnText").innerHTML = "Pay Now";
            }
        });
    </script>
@endsection
