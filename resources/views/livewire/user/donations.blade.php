<?php

use function Livewire\Volt\state;

state([
    'donations' => [
        [
            'id' => 1,
            'name' => 'Muhammad Wasi',
            'email' => 'mwasi5276@gmail.com',
            'type' => 'Daily',
            'amount' => '$ 30.00',
            'status' => 'Active',
            'start_date' => '2025-09-04',
            'end_date' => '2025-09-11',
            'cancel_date' => '2025-09-11',
        ],
    ],
]);

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
                                    <th>Name</th>
                                    <th>User Email</th>
                                    <th>Donation Type</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($donations as $donation)
                                    <tr>
                                        <td>{{ $donation['name'] }}</td>
                                        <td>{{ $donation['email'] }}</td>
                                        <td>{{ $donation['type'] }}</td>
                                        <td>{{ $donation['amount'] }}</td>
                                        <td>{{ $donation['status'] }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#donationModal{{ $donation['id'] }}">
                                                View
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Bootstrap Modal -->
                                    <div class="modal fade" id="donationModal{{ $donation['id'] }}" tabindex="-1"
                                        aria-labelledby="donationModalLabel{{ $donation['id'] }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header text-white">
                                                    <h5 class="modal-title"
                                                        id="donationModalLabel{{ $donation['id'] }}">
                                                        Donation Details - {{ $donation['name'] }}
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-black"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p><strong>Name:</strong>{{ $donation['name'] }}</p>
                                                            <p><strong>Email:</strong> {{ $donation['email'] }}</p>
                                                            <p><strong>Donation Type:</strong> {{ $donation['type'] }}
                                                            </p>
                                                            <p><strong>Amount:</strong> {{ $donation['amount'] }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p><strong>Status:</strong> {{ $donation['status'] }}</p>
                                                            <p><strong>Start Date:</strong>
                                                                {{ $donation['start_date'] }}</p>
                                                            <p><strong>End Date:</strong> {{ $donation['end_date'] }}
                                                            </p>
                                                            <p><strong>Cancel Date:</strong>
                                                                {{ $donation['cancel_date'] }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
