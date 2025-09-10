<?php

use function Livewire\Volt\state;

state([
    'subscriptions' => [
        [
            'id' => 1,
            'user_id' => 101,
            'name' => 'Muhammad Wasi',
            'email' => 'mwasi5276@gmail.com',
            'stripe_subscription_id' => 'SUB_123456',
            'stripe_price_id' => 'PRICE_98765',
            'status' => 'active',
            'price' => '3000',
            'currency' => 'PKR',
            'type' => 'Daily',
            'start_date' => '2025-09-01',
            'end_date' => '2025-09-30',
            'canceled_at' => null,
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
                                    <th>Email</th>
                                    <th>Stripe Subscription ID</th>
                                    <th>Status</th>
                                    <th>Currency</th>
                                    <th>Type</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($subscriptions as $subscription)
                                    <tr>
                                        <td>{{ $subscription['name'] }}</td>
                                        <td>{{ $subscription['email'] }}</td>
                                        <td>{{ $subscription['stripe_subscription_id'] }}</td>
                                        <td>
                                            @if ($subscription['status'] === 'active')
                                                <span class="badge bg-success">Active</span>
                                            @elseif($subscription['status'] === 'canceled')
                                                <span class="badge bg-danger">Canceled</span>
                                            @elseif($subscription['status'] === 'pending')
                                                <span class="badge bg-warning">Pending</span>
                                            @else
                                                <span
                                                    class="badge bg-secondary">{{ ucfirst($subscription['status']) }}</span>
                                            @endif
                                        </td>
                                        <td>{{ strtoupper($subscription['currency']) }}</td>
                                        <td>{{ ucfirst($subscription['type']) }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                data-bs-target="#subscriptionModal{{ $subscription['id'] }}">
                                                View
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Bootstrap Modal -->
                                    <div class="modal fade" id="subscriptionModal{{ $subscription['id'] }}"
                                        tabindex="-1"
                                        aria-labelledby="subscriptionModalLabel{{ $subscription['id'] }}"
                                        aria-hidden="true">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header text-white">
                                                    <h5 class="modal-title"
                                                        id="subscriptionModalLabel{{ $subscription['id'] }}">
                                                        Subscription Details - {{ $subscription['name'] }}
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-black"
                                                        data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <p><strong>Name:</strong> {{ $subscription['name'] }}</p>
                                                            <p><strong>Email:</strong> {{ $subscription['email'] }}</p>
                                                            <p><strong>Stripe Subscription ID:</strong>
                                                                {{ $subscription['stripe_subscription_id'] }}</p>
                                                            <p><strong>Stripe Price ID:</strong>
                                                                {{ $subscription['stripe_price_id'] }}</p>
                                                            <p><strong>Status:</strong>
                                                                @if ($subscription['status'] === 'active')
                                                                    <span class="badge bg-success">Active</span>
                                                                @elseif($subscription['status'] === 'canceled')
                                                                    <span class="badge bg-danger">Canceled</span>
                                                                @elseif($subscription['status'] === 'pending')
                                                                    <span class="badge bg-warning">Pending</span>
                                                                @else
                                                                    <span
                                                                        class="badge bg-secondary">{{ ucfirst($subscription['status']) }}</span>
                                                                @endif
                                                            </p>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <p><strong>Price:</strong>
                                                                {{ number_format($subscription['price']) }}</p>
                                                            <p><strong>Currency:</strong>
                                                                {{ strtoupper($subscription['currency']) }}</p>
                                                            <p><strong>Type:</strong>
                                                                {{ ucfirst($subscription['type']) }}</p>
                                                            <p><strong>Start Date:</strong>
                                                                {{ $subscription['start_date'] }}</p>
                                                            <p><strong>End Date:</strong>
                                                                {{ $subscription['end_date'] }}</p>
                                                            <p><strong>Canceled At:</strong>
                                                                {{ $subscription['canceled_at'] ?? '-' }}</p>
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
