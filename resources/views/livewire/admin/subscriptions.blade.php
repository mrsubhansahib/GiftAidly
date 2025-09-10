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
                                @foreach($subscriptions as $subscription)
                                    <tr>
                                        <td>{{ $subscription->user->name ?? '-' }}</td>
                                        <td>{{ $subscription->user->email ?? '-' }}</td>
                                        <td>
                                            @if($subscription->status === 'active')
                                                <span class="badge bg-success">Active</span>
                                            @elseif($subscription->status === 'canceled')
                                                <span class="badge bg-danger">Canceled</span>
                                            @elseif($subscription->status === 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($subscription->status ?? 'N/A') }}</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format($subscription->price ?? 0) }}</td>
                                        <td>{{ strtoupper($subscription->currency ?? 'PKR') }}</td>
                                        <td>{{ ucfirst($subscription->type ?? '-') }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#subscriptionModal{{ $subscription->id }}">
                                                View
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Bootstrap Modal -->
                                    <div class="modal fade" id="subscriptionModal{{ $subscription->id }}" tabindex="-1"
                                        aria-labelledby="subscriptionModalLabel{{ $subscription->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header text-white">
                                                    <h5 class="modal-title" id="subscriptionModalLabel{{ $subscription->id }}">
                                                        Subscription Details - {{ $subscription->user->name ?? 'N/A' }}
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p><strong>Name:</strong> {{ $subscription->user->name ?? '-' }}</p>
                                                            <p><strong>Email:</strong> {{ $subscription->user->email ?? '-' }}</p>
                                                            <p><strong>Stripe Subscription ID:</strong> {{ $subscription->stripe_subscription_id ?? '-' }}</p>
                                                            <p><strong>Stripe Price ID:</strong> {{ $subscription->stripe_price_id ?? '-' }}</p>
                                                            <p><strong>Status:</strong>
                                                                @if($subscription->status === 'active')
                                                                    <span class="badge bg-success">Active</span>
                                                                @elseif($subscription->status === 'canceled')
                                                                    <span class="badge bg-danger">Canceled</span>
                                                                @elseif($subscription->status === 'pending')
                                                                    <span class="badge bg-warning">Pending</span>
                                                                @else
                                                                    <span class="badge bg-secondary">{{ ucfirst($subscription->status ?? 'N/A') }}</span>
                                                                @endif
                                                            </p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p><strong>Price:</strong> {{ number_format($subscription->price ?? 0) }}</p>
                                                            <p><strong>Currency:</strong> {{ strtoupper($subscription->currency ?? 'PKR') }}</p>
                                                            <p><strong>Type:</strong> {{ ucfirst($subscription->type ?? '-') }}</p>
                                                            <p><strong>Start Date:</strong> {{ $subscription->start_date ? \Carbon\Carbon::parse($subscription->start_date)->format('Y-m-d') : '-' }}</p>
                                                            <p><strong>End Date:</strong> {{ $subscription->end_date ? \Carbon\Carbon::parse($subscription->end_date)->format('Y-m-d') : '-' }}</p>
                                                            <p><strong>Canceled At:</strong> {{ $subscription->canceled_at ? \Carbon\Carbon::parse($subscription->canceled_at)->format('Y-m-d') : '-' }}</p>
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
