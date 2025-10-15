<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.partials.page-title', ['title' => 'GiftAidly', 'subtitle' => 'Dashboard'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="row">
        <!-- Card 1 -->
        <?php if(auth()->check() && auth()->user()->role === 'admin'): ?>
            <div class="col-md-6 col-xl-3">
                <a href="<?php echo e(route('third', ['admin', 'donors', 'index'])); ?>" class="text-decoration-none">
                    <div class="card hover-shadow" style="cursor: pointer;">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="avatar-md bg-primary bg-opacity-10 rounded-circle">
                                        <iconify-icon icon="solar:users-group-rounded-outline"
                                            class="fs-32 text-primary avatar-title"></iconify-icon>
                                    </div>
                                </div>
                                <div class="col-6 text-end">
                                    <p class="text-muted mb-0 text-truncate">Donors</p>
                                    <h3 class="text-dark mt-2 mb-0">
                                        <?php echo e(\App\Models\User::where('role', 'donor')->count()); ?>

                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif; ?>

        <!-- Card 2 -->
        <?php
            $user = auth()->user();
            $isAdmin = $user && $user->role === 'admin';
            $isDonor = $user && $user->role === 'donor';

            $donationCount = $isAdmin
                ? \App\Models\Subscription::count()
                : ($isDonor
                    ? \App\Models\Subscription::where('user_id', $user->id)->count()
                    : 0);

            $donationRoute = $isAdmin
                ? route('third', ['admin', 'donations', 'index'])
                : route('third', ['user', 'donations', 'index']);

            $donationColClass = $isDonor ? 'col-md-6 col-xl-6' : 'col-md-6 col-xl-3';
        ?>

        <?php if($isAdmin || $isDonor): ?>
            <div class="<?php echo e($donationColClass); ?>">
                <a href="<?php echo e($donationRoute); ?>" class="text-decoration-none">
                    <div class="card hover-shadow" style="cursor: pointer;">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="avatar-md bg-primary bg-opacity-10 rounded-circle">
                                        <iconify-icon icon="solar:hand-heart-outline"
                                            class="fs-32 text-primary avatar-title"></iconify-icon>
                                    </div>
                                </div>
                                <div class="col-6 text-end">
                                    <p class="text-muted mb-0 text-truncate">
                                        <?php echo e($isAdmin ? 'Donations' : 'My Donations'); ?>

                                    </p>
                                    <h3 class="text-dark mt-2 mb-0"><?php echo e($donationCount); ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif; ?>


        <!-- Card 3 -->
        <?php
            $invoiceCount = $isAdmin
                ? \App\Models\Invoice::count()
                : ($isDonor
                    ? \App\Models\Invoice::whereHas('subscription', function ($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })->count()
                    : 0);

            $invoiceRoute = $isAdmin
                ? route('third', ['admin', 'invoices', 'index'])
                : route('third', ['user', 'invoices', 'index']);

            $invoiceColClass = $isDonor ? 'col-md-6 col-xl-6' : 'col-md-6 col-xl-3';
        ?>

        <?php if($isAdmin || $isDonor): ?>
            <div class="<?php echo e($invoiceColClass); ?>">
                <a href="<?php echo e($invoiceRoute); ?>" class="text-decoration-none">
                    <div class="card hover-shadow" style="cursor: pointer;">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="avatar-md bg-primary bg-opacity-10 rounded-circle">
                                        <iconify-icon icon="solar:bill-list-outline"
                                            class="fs-32 text-primary avatar-title"></iconify-icon>
                                    </div>
                                </div>
                                <div class="col-6 text-end">
                                    <p class="text-muted mb-0 text-truncate">
                                        <?php echo e($isAdmin ? 'Invoices' : 'My Invoices'); ?>

                                    </p>
                                    <h3 class="text-dark mt-2 mb-0"><?php echo e($invoiceCount); ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif; ?>


        <?php if(auth()->check() && auth()->user()->role === 'admin'): ?>
            <div class="col-md-6 col-xl-3">
                <a href="<?php echo e(route('third', ['admin', 'transactions', 'index'])); ?>" class="text-decoration-none">
                    <div class="card hover-shadow" style="cursor: pointer;">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-6">
                                    <div class="avatar-md bg-primary bg-opacity-10 rounded-circle">
                                        <iconify-icon icon="solar:transfer-horizontal-outline"
                                            class="fs-32 text-primary avatar-title"></iconify-icon>
                                    </div>
                                </div>
                                <div class="col-6 text-end">
                                    <p class="text-muted mb-0 text-truncate">Transactions</p>
                                    <h3 class="text-dark mt-2 mb-0"><?php echo e(\App\Models\Transaction::count()); ?></h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif; ?>
    </div>


    
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/js/pages/dashboard.js']); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.vertical', ['subtitle' => 'Dashboard'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Laravel\Softic-Era\Current Projects\GiftAidly\resources\views/index.blade.php ENDPATH**/ ?>