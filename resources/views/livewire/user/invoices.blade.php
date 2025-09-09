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
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Subscription ID</th>
                                    <th>Stripe Invoice ID</th>
                                    <th>Amount Due</th>
                                    <th>Currency</th>
                                    <th>Invoice Date</th>
                                    <th>Paid At</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Wasi Butt</td>
                                    <td>wasi@example.com</td>
                                    <td>SUBS_001</td>
                                    <td>INV_1001</td>
                                    <td>5000</td>
                                    <td>PKR</td>
                                    <td>2025-09-01</td>
                                    <td>2025-09-02</td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Ali Khan</td>
                                    <td>ali@example.com</td>
                                    <td>SUBS_002</td>
                                    <td>INV_1002</td>
                                    <td>4500</td>
                                    <td>PKR</td>
                                    <td>2025-09-03</td>
                                    <td>2025-09-04</td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Ahmed Raza</td>
                                    <td>ahmed@example.com</td>
                                    <td>SUBS_003</td>
                                    <td>INV_1003</td>
                                    <td>7000</td>
                                    <td>PKR</td>
                                    <td>2025-09-05</td>
                                    <td>2025-09-06</td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>Hassan Ali</td>
                                    <td>hassan@example.com</td>
                                    <td>SUBS_004</td>
                                    <td>INV_1004</td>
                                    <td>6000</td>
                                    <td>PKR</td>
                                    <td>2025-09-07</td>
                                    <td>2025-09-08</td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>Muhammad Usman</td>
                                    <td>usman@example.com</td>
                                    <td>SUBS_005</td>
                                    <td>INV_1005</td>
                                    <td>8000</td>
                                    <td>PKR</td>
                                    <td>2025-09-09</td>
                                    <td>2025-09-10</td>
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