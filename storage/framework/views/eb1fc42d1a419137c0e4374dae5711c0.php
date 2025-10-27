<?php

use App\Models\User;

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
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Active Donations</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($user->name); ?></td>
                                    <td><?php echo e($user->email); ?></td>
                                    <td><?php echo e($user->subscriptions->where('status', 'active')->count()); ?></td>
                                    <td class="text-center">
                                        <a href="<?php echo e(route('admin.donor.detail', $user->id)); ?>" class="btn btn-sm btn-primary">View</a>
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
</div><?php /**PATH D:\Laravel\Softic-Era\Current Projects\GiftAidly\resources\views\livewire/admin/donors.blade.php ENDPATH**/ ?>