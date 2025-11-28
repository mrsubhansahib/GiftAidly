@extends('layouts.base', ['subtitle' => 'Zakat Payment'])

@section('body-attribuet')
    class="authentication-bg"
@endsection

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <div class="card shadow-sm rounded-3">
                    <div class="card-body">
                        <h3 class="text-center my-4 fw-bold">Zakat Payment</h3>

                        {{-- Zakat Form --}}
                        <form id="zakatForm">
                            @csrf

                            <div class="mb-3">
                                <label for="name-zakat" class="form-label">Full Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="name" id="name-zakat" class="form-control"
                                    placeholder="Enter your full name" required>
                            </div>

                            <div class="mb-3">
                                <label for="email-zakat" class="form-label">Email Address <span
                                        class="text-danger">*</span></label>
                                <input type="email" name="email" id="email-zakat" class="form-control"
                                    placeholder="Enter your email" required>
                            </div>

                            {{-- Currency --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Currency:</label>
                                <input type="text" class="form-control" value="{{ strtoupper($currency) }}" readonly>
                                <input type="hidden" id="currency" value="{{ $currency }}">
                            </div>

                            {{-- Amount --}}
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Zakat Amount:</label>
                                <input type="text" class="form-control" value="{{ number_format($zakat, 2) }}" readonly>
                                <input type="hidden" id="amount" value="{{ $zakat }}">
                            </div>

                            {{-- Payment Element --}}
                            <div class="mb-3">
                                <label class="form-label">Card Details <span class="text-danger">*</span></label>
                                <div id="payment-element" class="form-control p-2"></div>
                                <small id="card-errors" class="text-danger mt-2 d-block"></small>
                            </div>

                            <div class="d-grid">
                                <button type="submit" id="submitBtn"
                                    style="background: linear-gradient(45deg, #1d43ab, #94740dff);"
                                    class="btn btn-lg fw-medium text-light">
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
    <script src="https://js.stripe.com/v3/"></script>

    <script>
        document.addEventListener("DOMContentLoaded", async function() {

            const stripe = Stripe("{{ env('STRIPE_KEY') }}");

            const elements = stripe.elements({
                clientSecret: "{{ $clientSecret }}"
            });

            const paymentElement = elements.create("payment");
            paymentElement.mount("#payment-element");

            const form = document.getElementById("zakatForm");
            const submitBtn = document.getElementById("submitBtn");
            const btnText = document.getElementById("btnText");
            const btnLoader = document.getElementById("btnLoader");

            form.addEventListener("submit", async function(e) {
                e.preventDefault();

                submitBtn.disabled = true;
                btnLoader.classList.remove("d-none");
                btnText.textContent = "Processing...";

                const {
                    error
                } = await stripe.confirmPayment({
                    elements,
                    confirmParams: {
                        return_url: "{{ route('zakat.redirect') }}",
                    }
                });

                if (error) {
                    document.getElementById("card-errors").textContent = error.message;
                    btnLoader.classList.add("d-none");
                    btnText.textContent = "Pay Now";
                    submitBtn.disabled = false;
                }
            });
        });
    </script>
@endsection
