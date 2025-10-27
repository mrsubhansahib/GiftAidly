<?php $__env->startSection('content'); ?>
    <?php echo $__env->make('layouts.partials.page-title', ['title' => 'Donor', 'subtitle' => 'Detail'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.donor.detail', ['id' => $id]);

$__html = app('livewire')->mount($__name, $__params, 'lw-1477679922-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.vertical', ['subtitle' => 'Donor'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Laravel\Softic-Era\Current Projects\GiftAidly\resources\views/admin/donors/detail.blade.php ENDPATH**/ ?>