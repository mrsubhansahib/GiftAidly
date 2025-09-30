<?php

use App\Models\Transaction;

?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-striped table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>Email</th>
                                    <th>Donation Type</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($transaction->invoice->subscription->user->email ?? '-'); ?></td>
                                        <td><?php echo e(ucfirst($transaction->invoice->subscription->type ?? '-')); ?></td>
                                        <td><?php echo e(number_format($transaction->invoice->subscription->price)); ?>

                                            <?php echo e(strtoupper($transaction->invoice->currency ?? 'PKR')); ?></td>
                                        <td>
                                            <!--[if BLOCK]><![endif]--><?php if($transaction->status === 'paid' || $transaction->status === 'completed'): ?>
                                                <span class="badge bg-success">Paid</span>
                                            <?php elseif($transaction->status === 'failed'): ?>
                                                <span class="badge bg-danger">Failed</span>
                                            <?php elseif($transaction->status === 'pending'): ?>
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            <?php else: ?>
                                                <span
                                                    class="badge bg-secondary"><?php echo e(ucfirst($transaction->status ?? 'N/A')); ?></span>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </td>
                                        <td><?php echo e($transaction->paid_at ? \Carbon\Carbon::parse($transaction->paid_at)->format('Y-m-d') : '-'); ?>

                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#transactionModal<?php echo e($transaction->id); ?>">
                                                View
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Transaction Modal -->
                                    <div class="modal fade" id="transactionModal<?php echo e($transaction->id); ?>" tabindex="-1"
                                        aria-labelledby="transactionModalLabel<?php echo e($transaction->id); ?>"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content border-0 rounded-4 shadow-lg">

                                                <!-- Minimal Header -->
                                                <div class="modal-header border-0 pb-0">
                                                    <h4 class="modal-title text-dark fw-semibold"
                                                        id="transactionModalLabel<?php echo e($transaction->id); ?>">
                                                        Transaction
                                                        #<?php echo e(substr($transaction->stripe_transaction_id ?? 'N/A', -8)); ?>

                                                    </h4>
                                                    <button type="button" class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                                </div>

                                                <!-- Modal Body -->
                                                <div class="modal-body px-4 pb-4">

                                                    <!-- Customer & Status Row -->
                                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                                        <div>
                                                            <h6 class="text-muted mb-1">Customer</h6>
                                                            <p class="mb-0 fw-medium">
                                                                <?php echo e($transaction->invoice->subscription->user->name ?? 'Guest User'); ?>

                                                            </p>
                                                            <small class="text-muted">
                                                                <?php echo e($transaction->invoice->subscription->user->email ?? '-'); ?>

                                                            </small>
                                                        </div>
                                                        <div class="text-end">
                                                            <span
                                                                class="badge 
                            <?php if($transaction->status === 'paid' || $transaction->status === 'completed'): ?> bg-success bg-opacity-10 text-success border border-success
                            <?php elseif($transaction->status === 'failed'): ?> 
                                bg-danger bg-opacity-10 text-danger border border-danger
                            <?php elseif($transaction->status === 'pending'): ?> 
                                bg-warning bg-opacity-10 text-warning border border-warning
                            <?php else: ?> 
                                bg-secondary bg-opacity-10 text-secondary border border-secondary <?php endif; ?> px-3 py-2 rounded-3">
                                                                <?php echo e(ucfirst($transaction->status ?? 'N/A')); ?>

                                                            </span>
                                                        </div>
                                                    </div>

                                                    <!-- Amount & Date Section -->
                                                    <div class="row mb-4">
                                                        <div class="col-6">
                                                            <div class="text-center p-4 bg-light rounded-3">
                                                                <h6 class="text-muted mb-2">Amount</h6>
                                                                <h3 class="text-dark fw-bold mb-0">
                                                                    <?php echo e(number_format($transaction->invoice->subscription->price ?? 0)); ?>

                                                                </h3>
                                                                <small class="text-muted">
                                                                    <?php echo e(strtoupper($transaction->invoice->currency ?? 'PKR')); ?>

                                                                </small>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="text-center p-4 bg-light rounded-3">
                                                                <h6 class="text-muted mb-2">Date</h6>
                                                                <p class="fw-semibold mb-1">
                                                                    <?php echo e($transaction->paid_at ? \Carbon\Carbon::parse($transaction->paid_at)->format('M d, Y') : 'Not set'); ?>

                                                                </p>
                                                                <small class="text-muted">Payment Date</small>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Details Section -->
                                                    <div class="border-top pt-4">
                                                        <h5 class="text-muted mb-4 fw-semibold">Transaction Details</h5>

                                                        <div class="row g-4">
                                                            <div class="col-sm-6">
                                                                <label
                                                                    class="form-label text-muted mb-2 fw-semibold">Invoice
                                                                    ID</label>
                                                                <input type="text"
                                                                    class="form-control bg-light border-0 fs-6 py-2"
                                                                    value="<?php echo e($transaction->invoice_id ?? '-'); ?>"
                                                                    readonly>
                                                            </div>

                                                            <div class="col-sm-6">
                                                                <label
                                                                    class="form-label text-muted mb-2 fw-semibold">Stripe
                                                                    Transaction ID</label>
                                                                <input type="text"
                                                                    class="form-control bg-light border-0 fs-6 py-2"
                                                                    value="<?php echo e($transaction->stripe_transaction_id ?? '-'); ?>"
                                                                    readonly>
                                                            </div>

                                                            <div class="col-sm-6">
                                                                <label
                                                                    class="form-label text-muted mb-2 fw-semibold">Donation
                                                                    Type</label>
                                                                <input type="text"
                                                                    class="form-control bg-light border-0 fs-6 py-2"
                                                                    value="<?php echo e(ucfirst($transaction->invoice->subscription->type ?? '-')); ?>"
                                                                    readonly>
                                                            </div>

                                                            <div class="col-sm-6">
                                                                <label
                                                                    class="form-label text-muted mb-2 fw-semibold">Status</label>
                                                                <input type="text"
                                                                    class="form-control bg-light border-0 fs-6 py-2"
                                                                    value="<?php echo e(ucfirst($transaction->status ?? 'N/A')); ?>"
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
            </div>
        </div>
    </div>
</div><?php /**PATH D:\Laravel\Softic-Era\Current Projects\GiftAidly\resources\views\livewire/admin/transactions.blade.php ENDPATH**/ ?>