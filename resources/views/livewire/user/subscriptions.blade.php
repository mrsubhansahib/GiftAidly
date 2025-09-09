<?php

use function Livewire\Volt\{state};

//

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
                                    <th>User ID</th>
                                    <th>Stripe Subscription ID</th>
                                    <th>Stripe Price ID</th>
                                    <th>Status</th>
                                    <th>Price</th>
                                    <th>Currency</th>
                                    <th>Type</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Canceled At</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>SUBS_001</td>
                                    <td>PRICE_001</td>
                                    <td><span class="badge bg-success">Active</span></td>
                                    <td>5000</td>
                                    <td>PKR</td>
                                    <td>Monthly</td>
                                    <td>2025-09-01</td>
                                    <td>2025-10-01</td>
                                    <td>-</td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>SUBS_002</td>
                                    <td>PRICE_002</td>
                                    <td><span class="badge bg-danger">Canceled</span></td>
                                    <td>4500</td>
                                    <td>PKR</td>
                                    <td>Monthly</td>
                                    <td>2025-08-15</td>
                                    <td>2025-09-15</td>
                                    <td>2025-09-10</td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>SUBS_003</td>
                                    <td>PRICE_003</td>
                                    <td><span class="badge bg-success">Active</span></td>
                                    <td>7000</td>
                                    <td>PKR</td>
                                    <td>Yearly</td>
                                    <td>2025-01-01</td>
                                    <td>2026-01-01</td>
                                    <td>-</td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>SUBS_004</td>
                                    <td>PRICE_004</td>
                                    <td><span class="badge bg-warning">Pending</span></td>
                                    <td>6000</td>
                                    <td>PKR</td>
                                    <td>Monthly</td>
                                    <td>2025-09-07</td>
                                    <td>2025-10-07</td>
                                    <td>-</td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>SUBS_005</td>
                                    <td>PRICE_005</td>
                                    <td><span class="badge bg-success">Active</span></td>
                                    <td>8000</td>
                                    <td>PKR</td>
                                    <td>Monthly</td>
                                    <td>2025-09-09</td>
                                    <td>2025-10-09</td>
                                    <td>-</td>
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