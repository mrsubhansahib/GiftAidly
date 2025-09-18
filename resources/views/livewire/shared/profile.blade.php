<?php

use function Livewire\Volt\{state, rules};
use Illuminate\Support\Facades\Auth;

state([
    'user' => fn() => Auth::user()->only(['name', 'title', 'city', 'country', 'address', 'zip_code']),
]);
rules([
    'user.name' => 'required|string|max:255',
    'user.title' => 'nullable|string|max:255',
    'user.city' => 'nullable|string|max:255',
    'user.country' => 'nullable|string|max:255',
    'user.address' => 'nullable|string|max:255',
    'user.zip_code' => 'nullable|string|max:20',
]);

$updateProfile = function () {
    $this->validate();
    Auth::user()->update($this->user);
    $this->dispatch('profile-updated', message: 'Profile updated successfully!');
};
?>

<div class="card">
    <div class="card-header d-flex justify-content-end align-items-center">
        {{-- <h5 class="card-title mb-0">Profile</h5> --}}
        <a href="#" class="fs-5 btn btn-primary" data-bs-toggle="modal" data-bs-target="#editProfileModal">
            <i class="bx bx-edit"></i> Edit
        </a>
    </div>

    <!-- Modal  -->
    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true"
        wire:ignore.self>
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
                        <button type="button" wire:click="updateProfile" class="btn btn-primary">
                            <i class="bx bx-save"></i> Save
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
