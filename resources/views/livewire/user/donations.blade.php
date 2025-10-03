<?php

use function Livewire\Volt\state;
use App\Models\Subscription;

state([
    'subscriptions' => fn() => Subscription::where('user_id', Auth::id())->get(),
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
                                    <th>Donation Type</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($subscriptions as $subscription)
                                    <tr>
                                        <td>
                                            {{ $subscription['type'] == 'day' ? 'Daily' : ($subscription['type'] == 'week' ? 'Weekly' : ($subscription['type'] == 'month' ? 'Monthly' : ucfirst($subscription['type']))) }}
                                        </td>
                                        <td>
                                            {{ match (strtoupper($subscription['currency'])) {
                                                'USD' => '$',
                                                'GBP' => '£',
                                                'EUR' => '€',
                                            } }}
                                            {{ number_format($subscription['price'], 2) }}
                                        </td>
                                        <td>
                                            <span
                                                class="badge {{ $subscription['status'] === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                                {{ ucfirst($subscription['status']) }}
                                            </span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($subscription['start_date'])->format('Y-m-d') }}
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($subscription['end_date'])->format('Y-m-d') }}</td>
                                        <td>
                                            <a href="{{ route('user.donations.detail', $subscription->id) }}"
                                                class="btn btn-sm btn-primary">
                                                View
                                            </a>
                                            <a href="{{ route('cancel.donation', $subscription->id) }}"
                                                class="btn btn-sm btn-danger"
                                                {{ $subscription['status'] === 'active' ? '' : 'disabled' }}>
                                                Cancel
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
