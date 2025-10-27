<?php

use App\Models\Subscription;

?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="" class="datatable table table-striped table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>Donation Type</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $subscriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subscription): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <?php echo e($subscription['type'] == 'day' ? 'Daily' : ($subscription['type'] == 'week' ? 'Weekly' : ($subscription['type'] == 'month' ? 'Monthly' : ucfirst($subscription['type'])))); ?>

                                    </td>
                                    <td>
                                        <?php echo e(match (strtoupper($subscription['currency'])) {
                                                'USD' => '$',
                                                'GBP' => '£',
                                                'EUR' => '€',
                                            }); ?>

                                        <?php echo e(number_format($subscription['price'], 2)); ?>

                                    </td>
                                    <td>
                                        <?php
                                        $statusClass = match($subscription['status']) {
                                        'active' => 'bg-success',
                                        'canceled' => 'bg-danger',
                                        'pending' => 'bg-warning',
                                        'ended' => 'bg-secondary',
                                        'trialing' => 'bg-primary',
                                        default => 'bg-info',
                                        };
                                        ?>

                                        <span class="badge <?php echo e($statusClass); ?>">
                                            <?php echo e(ucfirst($subscription['status'] ?? 'N/A')); ?>

                                        </span>
                                    </td>

                                    <td><?php echo e(\Carbon\Carbon::parse($subscription['start_date'])->format('Y-m-d')); ?>

                                    </td>
                                    <td><?php echo e(\Carbon\Carbon::parse($subscription['end_date'])->format('Y-m-d')); ?></td>
                                    <td>
                                        <a href="<?php echo e(route('user.donations.detail', $subscription->id)); ?>"
                                            class="btn btn-sm btn-primary">
                                            View
                                        </a>
                                        <!--[if BLOCK]><![endif]--><?php if($subscription['status'] === 'active'): ?>
                                        <button
                                            onclick="window.location.href='<?php echo e(route('cancel.donation', $subscription->id)); ?>'"
                                            class="btn btn-sm btn-danger"
                                            <?php echo e($subscription['status'] === 'active' ? '' : 'disabled'); ?>>
                                            Cancel
                                        </button>
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div><?php /**PATH D:\Laravel\Softic-Era\Current Projects\GiftAidly\resources\views\livewire/user/donations.blade.php ENDPATH**/ ?>