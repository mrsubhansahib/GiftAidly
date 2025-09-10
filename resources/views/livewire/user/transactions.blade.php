<?php

use function Livewire\Volt\state;

state([
    'transactions' => [
        [
            'id' => 1,
            'user_name' => 'Muhammad Wasi',
            'user_email' => 'mwasi5276@gmail.com',
            'invoice_id' => 'INV_1001',
            'stripe_transaction_id' => 'TXN_98765',
            'status' => 'paid',
            'paid_at' => '2025-09-05',
            'amount' => 3000,
            'currency' => 'PKR',
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
                                    <th>Invoice ID</th>
                                    <th>Stripe Transaction ID</th>
                                    <th>Paid At</th>
                                    <th>Status</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction['invoice_id'] }}</td>
                                        <td>{{ $transaction['stripe_transaction_id'] }}</td>
                                        <td>{{ $transaction['paid_at'] }}</td>
                                        <td>
                                            @if ($transaction['status'] === 'paid')
                                                <span class="badge bg-success">Paid</span>
                                            @elseif($transaction['status'] === 'failed')
                                                <span class="badge bg-danger">Failed</span>
                                            @elseif($transaction['status'] === 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @else
                                                <span
                                                    class="badge bg-secondary">{{ ucfirst($transaction['status']) }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#transactionModal{{ $transaction['id'] }}">
                                                View
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Bootstrap Modal -->
                                    <div class="modal fade" id="transactionModal{{ $transaction['id'] }}" tabindex="-1"
                                        aria-labelledby="transactionModalLabel{{ $transaction['id'] }}"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header text-white">
                                                    <h5 class="modal-title"
                                                        id="transactionModalLabel{{ $transaction['id'] }}">
                                                        Transaction Details - {{ $transaction['invoice_id'] }}
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-black"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p><strong>User Name:</strong>
                                                                {{ $transaction['user_name'] }}</p>
                                                            <p><strong>User Email:</strong>
                                                                {{ $transaction['user_email'] }}</p>
                                                            <p><strong>Invoice ID:</strong>
                                                                {{ $transaction['invoice_id'] }}</p>
                                                            <p><strong>Stripe Transaction ID:</strong>
                                                                {{ $transaction['stripe_transaction_id'] }}</p>
                                                            <p><strong>Status:</strong>
                                                                @if ($transaction['status'] === 'paid')
                                                                    <span class="badge bg-success">Paid</span>
                                                                @elseif($transaction['status'] === 'failed')
                                                                    <span class="badge bg-danger">Failed</span>
                                                                @elseif($transaction['status'] === 'pending')
                                                                    <span class="badge bg-warning">Pending</span>
                                                                @else
                                                                    <span
                                                                        class="badge bg-secondary">{{ ucfirst($transaction['status']) }}</span>
                                                                @endif
                                                            </p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p><strong>Paid At:</strong> {{ $transaction['paid_at'] }}
                                                            </p>
                                                            <p><strong>Amount:</strong>
                                                                {{ number_format($transaction['amount']) }}</p>
                                                            <p><strong>Currency:</strong>
                                                                {{ strtoupper($transaction['currency']) }}</p>
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
