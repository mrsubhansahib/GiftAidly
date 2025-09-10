<?php

use App\Models\Invoice;
use function Livewire\Volt\{state};

// Fetch all invoices dynamically
state([
    'invoices' => fn() => Invoice::all(),
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
                                    <th>Email</th>
                                    <th>Currency</th>
                                    <th>Invoice Date</th>
                                    <th>Paid At</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($this->invoices as $invoice)
                                    <tr>
                                        <td>{{ $invoice->subscription->user->name ?? '-' }}</td>
                                        <td>{{ $invoice->subscription->user->email ?? '-' }}</td>
                                        <td>{{ strtoupper($invoice->currency ?? 'PKR') }}</td>
                                        <td>{{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d') : '-' }}</td>
                                        <td>{{ $invoice->paid_at ? \Carbon\Carbon::parse($invoice->paid_at)->format('Y-m-d') : '-' }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#invoiceModal{{ $invoice->id }}">
                                                View
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Bootstrap Modal -->
                                    <div class="modal fade" id="invoiceModal{{ $invoice->id }}" tabindex="-1"
                                        aria-labelledby="invoiceModalLabel{{ $invoice->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header text-white">
                                                    <h5 class="modal-title" id="invoiceModalLabel{{ $invoice->id }}">
                                                        Invoice Details - {{ $invoice->user->name ?? 'N/A' }}
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p><strong>Name:</strong> {{ $invoice->user->name ?? '-' }}</p>
                                                            <p><strong>Email:</strong> {{ $invoice->user->email ?? '-' }}</p>
                                                            <p><strong>Subscription ID:</strong> {{ $invoice->subscription_id ?? '-' }}</p>
                                                            <p><strong>Stripe Invoice ID:</strong> {{ $invoice->stripe_invoice_id ?? '-' }}</p>
                                                            <p><strong>Currency:</strong> {{ strtoupper($invoice->currency ?? 'PKR') }}</p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p><strong>Amount Due:</strong> {{ number_format($invoice->amount_due ?? 0) }}</p>
                                                            <p><strong>Invoice Date:</strong> {{ $invoice->invoice_date ? \Carbon\Carbon::parse($invoice->invoice_date)->format('Y-m-d') : '-' }}</p>
                                                            <p><strong>Paid At:</strong> {{ $invoice->paid_at ? \Carbon\Carbon::parse($invoice->paid_at)->format('Y-m-d') : '-' }}</p>
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
