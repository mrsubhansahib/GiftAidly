<?php

use App\Models\Subscription;

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
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Price</th>
                                    <th>Currency</th>
                                    <th>Type</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $subscriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subscription): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($subscription->user->name ?? '-'); ?></td>
                                        <td><?php echo e($subscription->user->email ?? '-'); ?></td>
                                        <td>
                                            <!--[if BLOCK]><![endif]--><?php if($subscription->status === 'active'): ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php elseif($subscription->status === 'canceled'): ?>
                                                <span class="badge bg-danger">Canceled</span>
                                            <?php elseif($subscription->status === 'pending'): ?>
                                                <span class="badge bg-warning">Pending</span>
                                            <?php else: ?>
                                                <span
                                                    class="badge bg-secondary"><?php echo e(ucfirst($subscription->status ?? 'N/A')); ?></span>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </td>
                                        <td><?php echo e(number_format($subscription->price ?? 0)); ?></td>
                                        <td><?php echo e(strtoupper($subscription->currency ?? 'PKR')); ?></td>
                                        <td><?php echo e(ucfirst($subscription->type ?? '-')); ?></td>
                                        <td>
                                            <a href="<?php echo e(route('admin.donations.detail', $subscription->id)); ?>"
                                                class="btn btn-sm btn-primary">
                                                View
                                            </a>
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
</div><?php /**PATH D:\Laravel\Softic-Era\Current Projects\GiftAidly\resources\views\livewire/admin/donations.blade.php ENDPATH**/ ?>