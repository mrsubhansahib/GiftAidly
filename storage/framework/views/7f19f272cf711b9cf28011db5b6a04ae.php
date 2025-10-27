<?php

use Livewire\Volt\Component;
use App\Models\SpecialDonation;
use Livewire\Attributes\On;

?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-end mb-3">
                        <button wire:click="create" class="btn text-light" style="background-color:#0B539B;">
                            + Add
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table id="" class="datatable table table-striped table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <!-- <th>Currency</th> -->
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $donations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $donation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($donation['name']); ?></td>
                                    <td>£ <?php echo e(number_format($donation['price'], 2)); ?></td>
                                    <!-- <td><?php echo e(strtoupper($donation['currency'])); ?></td> -->
                                    <td class="text-center">
                                        <button wire:click="edit(<?php echo e($donation['id']); ?>)"
                                            class="btn btn-sm text-white"
                                            style="background-color:#0B539B;">
                                            Edit
                                        </button>
                                        <button wire:click="delete(<?php echo e($donation['id']); ?>)"
                                            class="btn btn-sm btn-danger">
                                            Delete
                                        </button>
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

    <!-- ✅ Modal -->
    <!--[if BLOCK]><![endif]--><?php if($showModal): ?>
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,.5);">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-semibold">
                        <?php echo e($editingId ? 'Edit Donation' : 'Add Donation'); ?>

                    </h5>
                    <button type="button" class="btn-close" wire:click="$set('showModal', false)"></button>
                </div>
                <div class="modal-body px-4 pb-4">
                    <form wire:submit.prevent="save">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Name</label>
                            <input type="text" wire:model="name" class="form-control" placeholder="Enter donation name">
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>

                        <!-- ✅ Price input with £ symbol -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Price</label>
                            <div class="input-group">
                                <span class="input-group-text fw-bold" style="background-color:#f1f1f1;">£</span>
                                <input type="number" wire:model="price" step="0.01" min="0" class="form-control" placeholder="Enter amount">
                            </div>
                            <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <div class="text-danger small"><?php echo e($message); ?></div> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button" class="btn btn-secondary" wire:click="$set('showModal', false)">
                                Cancel
                            </button>
                            <button type="submit" class="btn text-light" style="background-color:#0B539B;">
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

</div><?php /**PATH D:\Laravel\Softic-Era\Current Projects\GiftAidly\resources\views\livewire/admin/special-donations.blade.php ENDPATH**/ ?>