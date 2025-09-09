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
                                    <th>Title</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>City</th>
                                    <th>Country</th>
                                    <th>Address</th>
                                    <th>Zip Code</th>
                                    <th>Stripe ID</th>
                                    <th>Created At</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>Mr</td>
                                    <td>Wasi Butt</td>
                                    <td>wasi@example.com</td>
                                    <td>Admin</td>
                                    <td>Lahore</td>
                                    <td>Pakistan</td>
                                    <td>Johar Town</td>
                                    <td>54000</td>
                                    <td>STRP_001</td>
                                    <td>2025-09-09</td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>Mr</td>
                                    <td>Ali Khan</td>
                                    <td>ali@example.com</td>
                                    <td>User</td>
                                    <td>Karachi</td>
                                    <td>Pakistan</td>
                                    <td>Gulshan</td>
                                    <td>74000</td>
                                    <td>STRP_002</td>
                                    <td>2025-09-08</td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>Mr</td>
                                    <td>Ahmed Raza</td>
                                    <td>ahmed@example.com</td>
                                    <td>User</td>
                                    <td>Islamabad</td>
                                    <td>Pakistan</td>
                                    <td>F-8 Markaz</td>
                                    <td>44000</td>
                                    <td>STRP_003</td>
                                    <td>2025-09-07</td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>4</td>
                                    <td>Mr</td>
                                    <td>Hassan Ali</td>
                                    <td>hassan@example.com</td>
                                    <td>Manager</td>
                                    <td>Faisalabad</td>
                                    <td>Pakistan</td>
                                    <td>D Ground</td>
                                    <td>38000</td>
                                    <td>STRP_004</td>
                                    <td>2025-09-06</td>
                                    <td class="text-center">
                                        <a href="#" class="btn btn-sm btn-primary">View</a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>5</td>
                                    <td>Mr</td>
                                    <td>Muhammad Usman</td>
                                    <td>usman@example.com</td>
                                    <td>User</td>
                                    <td>Multan</td>
                                    <td>Pakistan</td>
                                    <td>Shah Rukn-e-Alam</td>
                                    <td>60000</td>
                                    <td>STRP_005</td>
                                    <td>2025-09-05</td>
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