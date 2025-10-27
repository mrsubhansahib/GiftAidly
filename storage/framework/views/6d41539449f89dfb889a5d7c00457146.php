<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.partials.page-title', ['title' => 'Special Donations', 'subtitle' => 'List'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <!-- <?php echo $__env->make('layouts.partials.alert', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>; -->
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.special-donations', []);

$__html = app('livewire')->mount($__name, $__params, 'lw-857897780-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
<script>
    window.addEventListener('show-donation-modal', () => {
        var myModal = new bootstrap.Modal(document.getElementById('donationModal'));
        myModal.show();
    });

    window.addEventListener('hide-donation-modal', () => {
        var myModalEl = document.getElementById('donationModal');
        var modal = bootstrap.Modal.getInstance(myModalEl);
        if (modal) {
            modal.hide();
        }
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.vertical', ['subtitle' => 'Create Special'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Laravel\Softic-Era\Current Projects\GiftAidly\resources\views/admin/special-donations/index.blade.php ENDPATH**/ ?>