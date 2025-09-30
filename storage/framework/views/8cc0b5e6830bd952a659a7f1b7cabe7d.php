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
                                        <td><?php echo e(ucfirst($subscription['type'])); ?></td>
                                        <td><?php echo e($subscription['price'] . ' ' . ucfirst($subscription['currency'])); ?></td>
                                        <td>
                                            <span
                                                class="badge <?php echo e($subscription['status'] === 'active' ? 'bg-success' : 'bg-danger'); ?>">
                                                <?php echo e(ucfirst($subscription['status'])); ?>

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