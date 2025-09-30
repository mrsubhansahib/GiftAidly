<?php

use App\Models\Subscription;

?>

<div class="container">

    
    <div class="card mb-3 shadow-sm">
        <div class="card-header">
            <h4>Donation Information</h4>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th>User</th>
                    <td><?php echo e($subscription->user->name); ?> (<?php echo e($subscription->user->email); ?>)</td>
                </tr>
                <tr>
                    <th>Type</th>
                    <td><?php echo e(ucfirst($subscription->type)); ?></td>
                </tr>
                <tr>
                    <th>Price</th>
                    <td><?php echo e($subscription->price); ?> <?php echo e($subscription->currency); ?></td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        <span class="badge <?php echo e($subscription->status === 'active' ? 'bg-success' : 'bg-danger'); ?>">
                            <?php echo e(ucfirst($subscription->status)); ?>

                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Start Date</th>
                    <td><?php echo e(\Carbon\Carbon::parse($subscription->start_date)->format('Y-m-d')); ?></td>
                </tr>
                <tr>
                    <th>End Date</th>
                    <td><?php echo e(\Carbon\Carbon::parse($subscription->end_date)->format('Y-m-d')); ?></td>
                </tr>
                <tr>
                    <th>Canceled At</th>
                    <td><?php echo e($subscription->canceled_at ? \Carbon\Carbon::parse($subscription->canceled_at)->format('Y-m-d') : 'N/A'); ?>

                    </td>
                </tr>
            </table>
        </div>
    </div>

    
    <div class="card mb-3 shadow-sm">
        <div class="card-header">
            <h4>Invoices</h4>
        </div>
        <div class="card-body table-responsive">
            <table id="invoices-table" class="table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Donation type</th>
                        <th>Amount Due</th>
                        <th>Status</th>
                        <th>Invoice Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $subscription->invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e(ucfirst($subscription->type)); ?></td>
                            <td><?php echo e($invoice->amount_due); ?></td>
                            <td>
                                <span
                                    class="badge <?php echo e($subscription->status === 'active' ? 'bg-success' : 'bg-danger'); ?>">
                                    <?php echo e(ucfirst($subscription->status)); ?>

                                </span>
                            </td>
                            <td><?php echo e(\Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d')); ?></td>
                            <td>
                                <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#invoiceModal<?php echo e($invoice->id); ?>">
                                    View
                                </button>
                            </td>
                        </tr>

                        <!-- Invoice Modal -->
                        <div class="modal fade" id="invoiceModal<?php echo e($invoice->id); ?>" tabindex="-1"
                            aria-labelledby="invoiceModalLabel<?php echo e($invoice->id); ?>" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content border-0 rounded-4 shadow-lg">

                                    <!-- Minimal Header -->
                                    <div class="modal-header border-0 pb-0">
                                        <h4 class="modal-title text-dark fw-semibold"
                                            id="invoiceModalLabel<?php echo e($invoice->id); ?>">
                                            Invoice #<?php echo e(substr($invoice->stripe_invoice_id ?? 'N/A', -8)); ?>

                                        </h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <!-- Modal Body -->
                                    <div class="modal-body px-4 pb-4">

                                        <!-- Customer & Status Row -->
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <div>
                                                <h6 class="text-muted mb-1">Customer</h6>
                                                <p class="mb-0 fw-medium">
                                                    <?php echo e($invoice->subscription->user->name ?? 'Guest User'); ?>

                                                </p>
                                                <small
                                                    class="text-muted"><?php echo e($invoice->subscription->user->email ?? '-'); ?></small>
                                            </div>
                                            <div class="text-end">
                                                <span
                                                    class="badge <?php echo e($invoice->paid_at ? 'bg-success bg-opacity-10 text-success border border-success' : 'bg-warning bg-opacity-10 text-warning border border-warning'); ?> px-3 py-2 rounded-3">
                                                    <?php echo e($invoice->paid_at ? 'Paid' : 'Pending'); ?>

                                                </span>
                                            </div>
                                        </div>

                                        <!-- Amount & Date Section -->
                                        <div class="row mb-4">
                                            <div class="col-6">
                                                <div class="text-center p-4 bg-light rounded-3">
                                                    <h6 class="text-muted mb-2">Amount</h6>
                                                    <h3 class="text-dark fw-bold mb-0">
                                                        <?php echo e(number_format($invoice->amount_due ?? 0)); ?>

                                                    </h3>
                                                    <small
                                                        class="text-muted"><?php echo e(strtoupper($invoice->currency ?? 'PKR')); ?></small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="text-center p-4 bg-light rounded-3">
                                                    <h6 class="text-muted mb-2">Date</h6>
                                                    <p class="fw-semibold mb-1">
                                                        <?php echo e($invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') : 'Not set'); ?>

                                                    </p>
                                                    <small class="text-muted">Invoice Date</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Details Section -->
                                        <div class="border-top pt-4">
                                            <h5 class="text-muted mb-4 fw-semibold">Invoice Details</h5>

                                            <div class="row g-4">
                                                <div class="col-sm-6">
                                                    <label class="form-label text-muted mb-2 fw-semibold">Stripe Invoice
                                                        ID</label>
                                                    <input type="text"
                                                        class="form-control bg-light border-0 fs-6 py-2"
                                                        value="<?php echo e($invoice->stripe_invoice_id ?? '-'); ?>" readonly>
                                                </div>

                                                <div class="col-sm-6">
                                                    <label class="form-label text-muted mb-2 fw-semibold">Subscription
                                                        ID</label>
                                                    <input type="text"
                                                        class="form-control bg-light border-0 fs-6 py-2"
                                                        value="<?php echo e($invoice->subscription_id ?? '-'); ?>" readonly>
                                                </div>

                                                <!--[if BLOCK]><![endif]--><?php if($invoice->paid_at): ?>
                                                    <div class="col-sm-6">
                                                        <label class="form-label text-muted mb-2 fw-semibold">Paid
                                                            At</label>
                                                        <input type="text"
                                                            class="form-control bg-light border-0 fs-6 py-2"
                                                            value="<?php echo e(\Carbon\Carbon::parse($invoice->paid_at)->format('M d, Y')); ?>"
                                                            readonly>
                                                    </div>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
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

    
    <div class="card mb-3 shadow-sm">
        <div class="card-header">
            <h4>Transactions</h4>
        </div>
        <div class="card-body table-responsive">
            <table id="transactions-table" class="table table-striped table-bordered align-middle">
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
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $subscription->invoices->flatMap->transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $txn): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e(ucfirst($subscription->type)); ?></td>
                            <td><?php echo e($subscription->price); ?></td>
                            <td>
                                <span
                                    class="badge <?php echo e($txn->status === 'completed' ? 'bg-success' : ($txn->status === 'pending' ? 'bg-warning text-dark' : 'bg-danger')); ?>">
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

                                    <!-- Minimal Header -->
                                    <div class="modal-header border-0 pb-0">
                                        <h4 class="modal-title text-dark fw-semibold"
                                            id="txnModalLabel<?php echo e($txn->id); ?>">
                                            Transaction #<?php echo e(substr($txn->stripe_transaction_id ?? 'N/A', -8)); ?>

                                        </h4>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>

                                    <!-- Modal Body -->
                                    <div class="modal-body px-4 pb-4">

                                        <!-- Status Row -->
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <div>
                                                <h6 class="text-muted mb-1">Invoice</h6>
                                                <p class="mb-0 fw-medium"><?php echo e($txn->invoice_id ?? '-'); ?></p>
                                            </div>
                                            <div class="text-end">
                                                <span
                                                    class="badge 
                            <?php if($txn->status === 'paid' || $txn->status === 'completed'): ?> bg-success bg-opacity-10 text-success border border-success
                            <?php elseif($txn->status === 'failed'): ?> 
                                bg-danger bg-opacity-10 text-danger border border-danger
                            <?php elseif($txn->status === 'pending'): ?> 
                                bg-warning bg-opacity-10 text-warning border border-warning
                            <?php else: ?> 
                                bg-secondary bg-opacity-10 text-secondary border border-secondary <?php endif; ?> px-3 py-2 rounded-3">
                                                    <?php echo e(ucfirst($txn->status ?? 'N/A')); ?>

                                                </span>
                                            </div>
                                        </div>

                                        <!-- Date & Invoice Section -->
                                        <div class="row mb-4">
                                            <div class="col-6">
                                                <div class="text-center p-4 bg-light rounded-3">
                                                    <h6 class="text-muted mb-2">Paid At</h6>
                                                    <p class="fw-semibold mb-1">
                                                        <?php echo e($txn->paid_at ? \Carbon\Carbon::parse($txn->paid_at)->format('M d, Y') : 'Not set'); ?>

                                                    </p>
                                                    <small class="text-muted">Payment Date</small>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="text-center p-4 bg-light rounded-3">
                                                    <h6 class="text-muted mb-2">Invoice ID</h6>
                                                    <p class="fw-semibold mb-1"><?php echo e($txn->invoice_id ?? '-'); ?></p>
                                                    <small class="text-muted">Linked Invoice</small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Details Section -->
                                        <div class="border-top pt-4">
                                            <h5 class="text-muted mb-4 fw-semibold">Transaction Details</h5>

                                            <div class="row g-4">
                                                <div class="col-sm-6">
                                                    <label class="form-label text-muted mb-2 fw-semibold">Stripe
                                                        Transaction ID</label>
                                                    <input type="text"
                                                        class="form-control bg-light border-0 fs-6 py-2"
                                                        value="<?php echo e($txn->stripe_transaction_id ?? '-'); ?>" readonly>
                                                </div>

                                                <div class="col-sm-6">
                                                    <label
                                                        class="form-label text-muted mb-2 fw-semibold">Status</label>
                                                    <input type="text"
                                                        class="form-control bg-light border-0 fs-6 py-2"
                                                        value="<?php echo e(ucfirst($txn->status ?? 'N/A')); ?>" readonly>
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
</div><?php /**PATH D:\Laravel\Softic-Era\Current Projects\GiftAidly\resources\views\livewire/user/donation/detail.blade.php ENDPATH**/ ?>