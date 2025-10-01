<?php

use App\Models\Subscription;
use function Livewire\Volt\{state};

// Fetch all subscriptions with user details
state([
    'subscriptions' => fn() => Subscription::with('user')->get(),
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
                                    <th>Status</th>
                                    <th>Price</th>
                                    <th>Currency</th>
                                    <th>Type</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($subscriptions as $subscription)
                                    <tr>
                                        <td>{{ $subscription->user->name ?? '-' }}</td>
                                        <td>{{ $subscription->user->email ?? '-' }}</td>
                                        <td>
                                            @if ($subscription->status === 'active')
                                                <span class="badge bg-success">Active</span>
                                            @elseif($subscription->status === 'canceled')
                                                <span class="badge bg-danger">Canceled</span>
                                            @elseif($subscription->status === 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @else
                                                <span
                                                    class="badge bg-secondary">{{ ucfirst($subscription->status ?? 'N/A') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format($subscription->price ?? 0) }}</td>
                                        <td>{{ strtoupper($subscription->currency ?? 'PKR') }}</td>
                                        <td>
                                            {{ $subscription->type === 'day'
                                                ? 'Daily'
                                                : ($subscription->type === 'week'
                                                    ? 'Weekly'
                                                    : ($subscription->type === 'month'
                                                        ? 'Monthly'
                                                        : ($subscription->type
                                                            ? ucfirst($subscription->type)
                                                            : '-'))) }}
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.donations.detail', $subscription->id) }}"
                                                class="btn btn-sm btn-primary">
                                                View
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
