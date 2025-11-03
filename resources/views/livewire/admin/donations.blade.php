<?php

use App\Models\Subscription;
use function Livewire\Volt\{state};

// Fetch all subscriptions with user details
state([
    'subscriptions' => fn() => Subscription::with('user')->latest()->get(),
]);

?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="" class="datatable table table-striped table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Price</th>
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
                                            @php
                                                $statusClass = match ($subscription['status']) {
                                                    'active' => 'bg-success',
                                                    'canceled' => 'bg-danger',
                                                    'ended' => 'bg-secondary',
                                                    default => 'bg-info',
                                                };
                                            @endphp

                                            <span class="badge {{ $statusClass }}">
                                                {{ ucfirst($subscription['status'] ?? 'N/A') }}
                                            </span>
                                        </td>
                                    <td>
                                        @php
                                        $symbols = [
                                        'usd' => '$',
                                        'gbp' => '£',
                                        'eur' => '€',
                                        ];
                                        $symbol =
                                        $symbols[strtolower($subscription->currency ?? '')] ??
                                        strtoupper($subscription->currency ?? '');
                                        @endphp
                                        {{ $symbol }}{{ number_format($subscription->price ?? 0, 2) }}
                                    </td>
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