<?php

use App\Models\User;
use function Livewire\Volt\{state, mount};

state([
    'user' => null,
]);


mount(function ($reference_id) {
    $this->user = User::where('reference_id', $reference_id)->first();
});
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
                                    <th>Donation Type</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($user->subscriptions as $subscription)
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
                                            @php
                                                $statusClass = match ($subscription['status']) {
                                                    'active' => 'bg-success',
                                                    'canceled' => 'bg-danger',
                                                    'pending' => 'bg-warning',
                                                    'ended' => 'bg-secondary',
                                                    'trialing' => 'bg-primary',
                                                    default => 'bg-info',
                                                };
                                            @endphp

                                            <span class="badge {{ $statusClass }}">
                                                {{ ucfirst($subscription['status'] ?? 'N/A') }}
                                            </span>
                                        </td>

                                        <td>{{ \Carbon\Carbon::parse($subscription['start_date'])->format('Y-m-d') }}
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($subscription['end_date'])->format('Y-m-d') }}</td>
                                        <td class="text-center align-middle">
                                            {{-- <a href="{{ route('user.donations.detail', $subscription->id) }}"
                                            class="btn btn-sm btn-primary">
                                            View
                                        </a> --}}
                                            @if ($subscription['status'] === 'active')
                                                <button
                                                    onclick="confirmCancel('{{ route('cancel.donation', $subscription->id) }}')"
                                                    class="btn btn-sm btn-danger">
                                                    Cancel
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-danger" disabled>
                                                    Cancel
                                                </button>
                                            @endif
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
