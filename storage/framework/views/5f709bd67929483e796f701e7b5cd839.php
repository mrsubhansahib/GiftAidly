<?php

use App\Models\User;

?>

<div class="container">

    
    <div class="card mb-3 shadow-sm">
        <div class="card-header">
            <h4>Donor Information</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th>Name</th>
                    <td><?php echo e($user->name); ?></td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td><?php echo e($user->email); ?></td>
                </tr>
                <tr>
                    <th>Country</th>
                    <td><?php echo e($user->country ?? 'N/A'); ?></td>
                </tr>
                <tr>
                    <th>City</th>
                    <td><?php echo e($user->city ?? 'N/A'); ?></td>
                </tr>
            </table>
        </div>
    </div>

    
    <div class="card mb-3 shadow-sm">
        <div class="card-header">
            <h4>Donations</h4>
        </div>
        <div class="card-body table-responsive">
            <table id="" class="datatable table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Donation Type</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Cancel Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $user->subscriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <?php echo e($sub->type === 'day'
                                    ? 'Daily'
                                    : ($sub->type === 'week'
                                        ? 'Weekly'
                                        : ($sub->type === 'month'
                                            ? 'Monthly'
                                            : ($sub->type
                                                ? ucfirst($sub->type)
                                                : '-')))); ?>

                            </td>
                            <td>
                                <?php echo e(match (strtoupper($sub->currency)) {
                                    'USD' => '$',
                                    'GBP' => '£',
                                    'EUR' => '€',
                                }); ?>

                                <?php echo e(number_format($sub->price, 2)); ?>

                            </td>
                            <td>
                                <span class="badge <?php echo e($sub->status === 'active' ? 'bg-success' : 'bg-danger'); ?>">
                                    <?php echo e(ucfirst($sub->status)); ?>

                                </span>
                            </td>
                            <td><?php echo e(\Carbon\Carbon::parse($sub->start_date)->format('Y-m-d')); ?></td>
                            <td><?php echo e(\Carbon\Carbon::parse($sub->end_date)->format('Y-m-d')); ?></td>
                            <td><?php echo e($sub->canceled_at ? \Carbon\Carbon::parse($sub->canceled_at)->format('Y-m-d') : 'N/A'); ?>

                            </td>
                            <td>
                                <a href="<?php echo e(route('admin.donations.detail', $sub->id)); ?>"
                                    class="btn btn-sm btn-primary">View</a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </tbody>
            </table>
        </div>
    </div>

    
    

    
    <div class="card mb-3 shadow-sm">
        <div class="card-header">
            <h4>Transactions</h4>
        </div>
        <div class="card-body table-responsive">
            <table id="" class="datatable table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Donation Type</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Paid at</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $user->subscriptions->flatMap->invoices->flatMap->transactions->sortByDesc('paid_at'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $txn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <?php echo e($txn->invoice->subscription->type === 'day'
                                    ? 'Daily'
                                    : ($txn->invoice->subscription->type === 'week'
                                        ? 'Weekly'
                                        : ($txn->invoice->subscription->type === 'month'
                                            ? 'Monthly'
                                            : ($txn->invoice->subscription->type
                                                ? ucfirst($txn->invoice->subscription->type)
                                                : '-')))); ?>

                            </td>
                            <td>
                                <?php echo e(match (strtoupper($txn->invoice->currency)) {
                                    'USD' => '$',
                                    'GBP' => '£',
                                    'EUR' => '€',
                                }); ?>

                                <?php echo e(number_format($txn->invoice->subscription->price, 2)); ?>

                            </td>
                            <td>
                                <span
                                    class="badge 
                                    <?php echo e($txn->status === 'paid' ? 'bg-success' : ($txn->status === 'pending' ? 'bg-warning text-light' : 'bg-danger')); ?>">
                                    <?php echo e(ucfirst($txn->status)); ?>

                                </span>
                            </td>
                            <td><?php echo e(\Carbon\Carbon::parse($txn->paid_at)->format('Y-m-d')); ?></td>
                            <td>
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#txnModal<?php echo e($txn->id); ?>">
                                    View
                                </button>
                            </td>
                        </tr>

                        <!-- Transaction Modal -->
                        <div class="modal fade" id="txnModal<?php echo e($txn->id); ?>" tabindex="-1"
                            aria-labelledby="txnModalLabel<?php echo e($txn->id); ?>" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content border-0 rounded-4 shadow-lg">

                                    <!-- Header -->
                                    <div class="modal-header border-0 pb-0">
                                        <h4 class="modal-title text-dark fw-semibold"
                                            id="txnModalLabel<?php echo e($txn->id); ?>">
                                            Transaction #<?php echo e(substr($txn->stripe_transaction_id ?? 'N/A', -8)); ?>

                                        </h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <!-- Body -->
                                    <div class="modal-body px-4 pb-4">

                                        <!-- Customer & Status -->
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <div>
                                                <h6 class="text-muted mb-1">Customer</h6>
                                                <p class="mb-0 fw-medium">
                                                    <?php echo e($txn->invoice->subscription->user->name ?? 'Guest User'); ?>

                                                </p>
                                                <small class="text-muted">
                                                    <?php echo e($txn->invoice->subscription->user->email ?? '-'); ?>

                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <span
                                                    class="badge 
                            <?php if($txn->status === 'paid' || $txn->status === 'completed'): ?> bg-success bg-opacity-10 text-success border border-success
                            <?php elseif($txn->status === 'failed'): ?> bg-danger bg-opacity-10 text-danger border border-danger
                            <?php elseif($txn->status === 'pending'): ?> bg-warning bg-opacity-10 text-warning border border-warning
                            <?php else: ?> bg-secondary bg-opacity-10 text-secondary border border-secondary <?php endif; ?>
                            px-3 py-2 rounded-3">
                                                    <?php echo e(ucfirst($txn->status ?? 'N/A')); ?>

                                                </span>
                                            </div>
                                        </div>

                                        <!-- Invoice & Transaction IDs -->
                                        <div class="row mb-4">
                                            <div class="col-sm-6">
                                                <h6 class="text-muted mb-1">Invoice ID</h6>
                                                <p class="fw-medium mb-0">
                                                    <?php echo e($txn->invoice->stripe_invoice_id ?? ($txn->invoice_id ?? '-')); ?>

                                                </p>
                                            </div>
                                            <div class="col-sm-6 text-sm-end">
                                                <h6 class="text-muted mb-1">Transaction ID</h6>
                                                <p class="fw-medium mb-0">
                                                    <?php echo e($txn->stripe_transaction_id ?? '-'); ?>

                                                </p>
                                            </div>
                                        </div>

                                        <!-- Amount Section -->
                                        <div class="row mb-4">
                                            <div class="col-12">
                                                <div class="text-center p-4 bg-light rounded-3">
                                                    <h6 class="text-muted mb-2">Amount</h6>
                                                    <h3 class="text-dark fw-bold mb-0">
                                                        <?php echo e(match (strtoupper($txn->invoice->currency)) {
                                                            'USD' => '$',
                                                            'GBP' => '£',
                                                            'EUR' => '€',
                                                        }); ?>

                                                        <?php echo e(number_format($txn->invoice->subscription->price ?? 0, 2)); ?>

                                                    </h3>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Details Section -->
                                        <div class="border-top pt-4">
                                            <div class="row g-4">
                                                <div class="col-sm-6">
                                                    <label class="form-label text-muted mb-2 fw-semibold">Paid
                                                        At</label>
                                                    <input type="text"
                                                        class="form-control bg-light border-0 fs-6 py-2"
                                                        value="<?php echo e($txn->paid_at ? \Carbon\Carbon::parse($txn->paid_at)->format('M d, Y') : 'Not set'); ?>"
                                                        readonly>
                                                </div>

                                                <div class="col-sm-6">
                                                    <label class="form-label text-muted mb-2 fw-semibold">Invoice
                                                        Date</label>
                                                    <input type="text"
                                                        class="form-control bg-light border-0 fs-6 py-2"
                                                        value="<?php echo e($txn->invoice->invoice_date ? \Carbon\Carbon::parse($txn->invoice->invoice_date)->format('M d, Y') : '-'); ?>"
                                                        readonly>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </tbody>
            </table>
        </div>
    </div>

</div><?php /**PATH D:\Laravel\Softic-Era\Current Projects\GiftAidly\resources\views\livewire/admin/donor/detail.blade.php ENDPATH**/ ?>