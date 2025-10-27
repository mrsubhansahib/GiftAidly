<?php $__env->startSection('body-attribuet'); ?>
class="authentication-bg"
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<?php echo $__env->make('layouts.partials.alert', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow-sm rounded-3">
                <div class="card-body">
                    <h3 class="text-center mb-4 fw-bold">Zakat Payment</h3>



                    
                    <form action="<?php echo e(route('zakat.process')); ?>" method="POST" id="zakatForm">

                        <?php echo csrf_field(); ?>
                        <?php
                        // Normalize any symbol to valid ISO code
                        $currency = match($currency) {
                        '£' => 'GBP',
                        '$' => 'USD',
                        '€' => 'EUR',
                        };
                        ?>

                        <input type="hidden" name="currency" value="<?php echo e($currency); ?>">


                        <input type="hidden" name="zakat" value="<?php echo e($zakat); ?>">

                        <div class="mb-3">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Enter your full name" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
                        </div>

                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Currency:</label>
                            <input type="text" class="form-control" value="<?php echo e(strtoupper($currency)); ?>" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Zakat Amount:</label>
                            <input type="text" class="form-control" value="<?php echo e(number_format($zakat, 2)); ?>" readonly>
                        </div>

                        
                        <div class="mb-3">
                            <label class="form-label">Card Details <span class="text-danger">*</span></label>
                            <div id="card-element" class="form-control p-2" style="height:auto;"></div>
                            <small id="card-errors" class="text-danger mt-2 d-block"></small>
                        </div>

                        <input type="hidden" name="stripeToken" id="stripeToken">

                        <button type="submit" id="submitBtn" class="btn btn-primary w-100 fw-semibold">
                            <span id="btnText">Pay Now</span>
                            <span id="btnLoader" class="spinner-border spinner-border-sm d-none"></span>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>

<script src="https://js.stripe.com/v3/"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const stripe = Stripe("<?php echo e(env('STRIPE_KEY')); ?>");
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.base', ['subtitle' => 'Zakat Payment'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Laravel\Softic-Era\Current Projects\GiftAidly\resources\views/zakah/form.blade.php ENDPATH**/ ?>