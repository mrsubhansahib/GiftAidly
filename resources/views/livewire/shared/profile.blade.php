<?php

use function Livewire\Volt\{state, rules};
use Illuminate\Support\Facades\Auth;
use App\Models\User;

state([
    'reference_id' => request('reference_id'),
    'user' => function () {
        if (Auth::check()) {
            // Logged-in admin
            return Auth::user()->only(['name', 'title', 'city', 'country', 'address', 'zip_code']);
        }
        // Donor (via reference_id)
        $reference_id = request('reference_id');
        if (!$reference_id) {
            return [];
        }
        $user = User::where('reference_id', $reference_id)->first();
        return $user?->only(['name', 'title', 'city', 'country', 'address', 'zip_code']) ?? [];
    },
]);

rules([
    'user.name' => 'required|string|max:255',
    'user.title' => 'nullable|string|max:255',
    'user.city' => 'nullable|string|max:255',
    'user.country' => 'nullable|string|max:255',
    'user.address' => 'nullable|string|max:255',
    'user.zip_code' => 'nullable|string|max:20',
])
    ->messages([
        'user.name.required' => 'Name is required.',
    ])
    ->attributes([
        'user.name' => 'name',
    ]);

$updateProfile = function () {
    $this->validate();
    // Case 1: Admin
    if (Auth::check()) {
        Auth::user()->update($this->user);
        $this->dispatch('profile-updated', message: 'Profile updated successfully!');
        return;
    }
    // Case 2: Donor
    $reference_id = $this->reference_id;
    if (!$reference_id) {
        $this->dispatch('profile-updated', message: 'Error: Missing reference ID.');
        return;
    }
    $user = User::where('reference_id', $reference_id)->first();
    if ($user) {
        $user->update($this->user);
        $this->dispatch('profile-updated', message: 'Profile updated successfully!');
    } else {
        $this->dispatch('profile-updated', message: 'Error: User not found.');
    }
};
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="modal-title text-dark fw-semibold">Account Information</h4>
        <a href="#" class="btn text-light fs-5 d-flex align-items-center" style="background-color:#0B539B;"
            data-bs-toggle="modal" data-bs-target="#editProfileModal">
            <i class="bx bx-edit me-1"></i> Edit
        </a>
    </div>

    <!-- Modal  -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel"
        aria-hidden="true" wire:ignore.self>
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h4 class="modal-title text-dark fw-semibold">Edit Profile</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body px-4 pb-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" wire:model.defer="user.name"
                                class="form-control @error('user.name') is-invalid @enderror">
                            @error('user.name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Title</label>
                            <input type="text" wire:model.defer="user.title"
                                class="form-control @error('user.title') is-invalid @enderror">
                            @error('user.title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">City</label>
                            <input type="text" wire:model.defer="user.city"
                                class="form-control @error('user.city') is-invalid @enderror">
                            @error('user.city')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Country</label>
                            <input type="text" wire:model.defer="user.country"
                                class="form-control @error('user.country') is-invalid @enderror">
                            @error('user.country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Address</label>
                            <input type="text" wire:model.defer="user.address"
                                class="form-control @error('user.address') is-invalid @enderror">
                            @error('user.address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Zip Code</label>
                            <input type="number" wire:model.defer="user.zip_code"
                                class="form-control @error('user.zip_code') is-invalid @enderror">
                            @error('user.zip_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" wire:click="updateProfile" wire:loading.attr="disabled"
                            wire:target="updateProfile" class="btn btn-primary">
                            <span wire:loading.remove wire:target="updateProfile">
                                <i class="bx bx-save"></i> Save
                            </span>
                            <span wire:loading wire:target="updateProfile">
                                <i class="bx bx-loader bx-spin"></i> Save
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Body -->
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Name</label>
                <input type="text" class="form-control" value="{{ $user['name'] ?: 'N/A' }}" disabled>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Title</label>
                <input type="text" class="form-control" value="{{ $user['title'] ?: 'N/A' }}" disabled>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">City</label>
                <input type="text" class="form-control" value="{{ $user['city'] ?: 'N/A' }}" disabled>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Country</label>
                <input type="text" class="form-control" value="{{ $user['country'] ?: 'N/A' }}" disabled>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Address</label>
                <input type="text" class="form-control" value="{{ $user['address'] ?: 'N/A' }}" disabled>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label">Zip Code</label>
                <input type="text" class="form-control" value="{{ $user['zip_code'] ?: 'N/A' }}" disabled>
            </div>
        </div>
    </div>
</div>
