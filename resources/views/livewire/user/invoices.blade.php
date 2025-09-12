<?php

use App\Models\Invoice;
use function Livewire\Volt\state;

state([
    'invoices' => fn() => Invoice::whereHas('subscription', function ($q) {
        $q->where('user_id', Auth::id());
    })
        ->with('subscription.user')
        ->get(),
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
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($invoices as $invoice)
                                    <tr>
                                        <td>{{ $invoice['name'] }}</td>
                                        <td>{{ $invoice['email'] }}</td>
                                        <td>{{ $invoice['currency'] }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#invoiceModal{{ $invoice['id'] }}">
                                                View
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Bootstrap Modal -->
                                    <div class="modal fade" id="invoiceModal{{ $invoice['id'] }}" tabindex="-1"
                                        aria-labelledby="invoiceModalLabel{{ $invoice['id'] }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header text-white">
                                                    <h5 class="modal-title" id="invoiceModalLabel{{ $invoice['id'] }}">
                                                        Invoice Details - {{ $invoice['name'] }}
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-black"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p><strong>Name:</strong> {{ $invoice['name'] }}</p>
                                                            <p><strong>Email:</strong> {{ $invoice['email'] }}</p>
                                                            <p><strong>Subscription ID:</strong>
                                                                {{ $invoice['subscription_id'] }}</p>
                                                            <p><strong>Stripe Invoice ID:</strong>
                                                                {{ $invoice['stripe_invoice_id'] }}</p>
                                                            <p><strong>Currency:</strong> {{ $invoice['currency'] }}
                                                            </p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p><strong>Amount Due:</strong>
                                                                {{ $invoice['amount_due'] }}</p>
                                                            <p><strong>Invoice Date:</strong>
                                                                {{ $invoice['invoice_date'] }}</p>
                                                            <p><strong>Paid At:</strong> {{ $invoice['paid_at'] }}</p>
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
