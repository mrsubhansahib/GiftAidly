<!-- DataTables JS and jquery-->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('.datatable').DataTable({
            "ordering": false
        });
    });
</script>
<?php echo app('Illuminate\Foundation\Vite')('resources/js/app.js'); ?>
<?php echo app('Illuminate\Foundation\Vite')('resources/js/loader.js'); ?>
<?php echo $__env->yieldContent('scripts'); ?>

<?php /**PATH D:\Laravel\Softic-Era\Current Projects\GiftAidly\resources\views/layouts/partials/vendor-scripts.blade.php ENDPATH**/ ?>