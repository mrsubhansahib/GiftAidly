<?php

use function Livewire\Volt\{state};

//

?>
<div class="container-fluid">
    <div class="row ">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="datatable" class="table table-striped table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>Invoice ID</th>
                                    <th>Stripe Transaction ID</th>
                                    <th>Paid At</th>
                                    <th>Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>INV_1001</td>
                                    <td>TRX_ABC123</td>
                                    <td>2025-09-01</td>
                                    <td><span class="badge bg-success">Paid</span></td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>INV_1002</td>
                                    <td>TRX_DEF456</td>
                                    <td>2025-09-02</td>
                                    <td><span class="badge bg-danger">Failed</span></td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>INV_1003</td>
                                    <td>TRX_GHI789</td>
                                    <td>2025-09-03</td>
                                    <td><span class="badge bg-warning">Pending</span></td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>INV_1004</td>
                                    <td>TRX_JKL012</td>
                                    <td>2025-09-04</td>
                                    <td><span class="badge bg-success">Paid</span></td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>INV_1005</td>
                                    <td>TRX_MNO345</td>
                                    <td>2025-09-05</td>
                                    <td><span class="badge bg-danger">Failed</span></td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>