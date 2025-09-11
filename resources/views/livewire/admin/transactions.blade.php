<?php

use App\Models\Transaction;
use function Livewire\Volt\{state};

state([
    'transactions' => fn() => Transaction::all(),
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
                                    <th>Email</th>
                                    <th>Donation Type</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->invoice->subscription->user->email ?? '-' }}</td>
                                        <td>{{ ucfirst($transaction->invoice->subscription->type ?? '-') }}</td>
                                        <td>{{ number_format($transaction->invoice->subscription->price) }}
                                            {{ strtoupper($transaction->invoice->currency ?? 'PKR') }}</td>
                                        <td>
                                            @if ($transaction->status === 'paid' || $transaction->status === 'completed')
                                                <span class="badge bg-success">Paid</span>
                                            @elseif($transaction->status === 'failed')
                                                <span class="badge bg-danger">Failed</span>
                                            @elseif($transaction->status === 'pending')
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @else
                                                <span
                                                    class="badge bg-secondary">{{ ucfirst($transaction->status ?? 'N/A') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $transaction->paid_at ? \Carbon\Carbon::parse($transaction->paid_at)->format('Y-m-d') : '-' }}
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#transactionModal{{ $transaction->id }}">
                                                View
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Transaction Modal -->
                                    <div class="modal fade" id="transactionModal{{ $transaction->id }}" tabindex="-1"
                                        aria-labelledby="transactionModalLabel{{ $transaction->id }}"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title"
                                                        id="transactionModalLabel{{ $transaction->id }}">
                                                        Transaction Details -
                                                        {{ $transaction->invoice->subscription->user->name }}
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p><strong>Email:</strong>
                                                                {{ $transaction->invoice->subscription->user->email ?? '-' }}
                                                            </p>
                                                            <p><strong>Invoice ID:</strong>
                                                                {{ $transaction->invoice_id ?? '-' }}</p>
                                                            <p><strong>Stripe Transaction ID:</strong>
                                                                {{ $transaction->stripe_transaction_id ?? '-' }}</p>
                                                            <p><strong>Status:</strong>
                                                                @if ($transaction->status === 'paid' || $transaction->status === 'completed')
                                                                    <span class="badge bg-success">Paid</span>
                                                                @elseif($transaction->status === 'failed')
                                                                    <span class="badge bg-danger">Failed</span>
                                                                @elseif($transaction->status === 'pending')
                                                                    <span
                                                                        class="badge bg-warning text-dark">Pending</span>
                                                                @else
                                                                    <span
                                                                        class="badge bg-secondary">{{ ucfirst($transaction->status ?? 'N/A') }}</span>
                                                                @endif
                                                            </p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p><strong>Paid At:</strong>
                                                                {{ $transaction->paid_at ? \Carbon\Carbon::parse($transaction->paid_at)->format('Y-m-d') : '-' }}
                                                            </p>
                                                            <p><strong>Amount:</strong>
                                                                {{ number_format($transaction->invoice->subscription->price ?? 0) }}
                                                            </p>
                                                            <p><strong>Currency:</strong>
                                                                {{ strtoupper($transaction->invoice->currency ?? 'PKR') }}
                                                            </p>
                                                            <p><strong>Donation Type:</strong>
                                                                {{ ucfirst($transaction->invoice->subscription->type ?? '-') }}
                                                            </p>
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
