<?php

use function Livewire\Volt\{state, rules};
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

state([
    'old_password' => '',
    'new_password' => '',
    'confirm_password' => '',
]);

rules([
    'old_password' => 'required',
    'new_password' => 'required|min:8',
    'confirm_password' => 'required',
]);

$changePassword = function () {
    $this->validate(); // Run validation

    $user = Auth::user();

    // Old password check
    if (!Hash::check($this->old_password, $user->password)) {
        $this->addError('old_password', 'The old password is not correct.');
        return;
    }

    // Confirm password check
    if ($this->new_password !== $this->confirm_password) {
        $this->addError('confirm_password', 'The new password and confirmation password do not match.');
        return;
    }

    // Update password
    $user->update([
        'password' => Hash::make($this->new_password),
    ]);

    // Fields reset
    $this->reset(['old_password', 'new_password', 'confirm_password']);

    // Success message flash
    session()->flash('success', 'Password changed successfully!');
    $this->dispatch('toast', type: 'success', message: 'Password changed successfully!');
};

// Live check function for password confirmation
$updated = function ($property) {
    if ($property === 'confirm_password' || $property === 'new_password') {
        if ($this->confirm_password && $this->new_password) {
            if ($this->confirm_password === $this->new_password) {
                $this->resetErrorBag('confirm_password');
            } else {
                $this->addError('confirm_password', 'Passwords do not match.');
            }
        }
    }
};

?>

<div>
    {{-- Custom Styles for the professional look --}}
    <style>
        .password-input-container {
            position: relative;
        }

        .password-toggle-icon {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            /* Muted icon color */
            transition: color 0.2s ease-in-out;
            z-index: 100;
        }

        .password-toggle-icon:hover {
            color: #343a40;
            /* Darker color on hover */
        }

        /* Add padding to the input to prevent text from overlapping the icon */
        .form-control-password {
            padding-right: 45px !important;
        }

        .card-change-password {
            border: none;
            border-radius: 1rem;
            transition: all 0.3s ease-in-out;
        }

        .card-change-password:hover {
            transform: translateY(-5px);
        }
    </style>

    <div class="container pb-5">
        <div class="row d-flex justify-content-center">
            <div class="col-12 col-md-10 col-lg-8">
                <div class="card shadow-lg card-change-password">
                    <div class="card-body p-4 p-md-5">
                        <h3 class="card-title text-center mb-2 fw-bold">Change Password</h3>
                        <p class="card-text text-center text-muted mb-4">
                            Update your password for enhanced security.
                        </p>
                        <form wire:submit.prevent="changePassword">
                            <div class="row">
                                <!-- Old Password -->
                                <div class="col-12 mb-4">
                                    <label for="old_password" class="form-label">Old Password</label>
                                    <div class="password-input-container">
                                        <input type="password" wire:model="old_password"
                                            class="form-control form-control-lg form-control-password" id="old_password"
                                            placeholder="Enter your current password">
                                        <span class="password-toggle-icon"
                                            onclick="togglePassword('old_password', this)">
                                            <iconify-icon icon="mdi:eye-outline" width="24"
                                                height="24"></iconify-icon>
                                        </span>
                                    </div>
                                    @error('old_password')
                                        <small class="text-danger mt-1 d-block">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- New Password -->
                                <div class="col-md-6 mb-4">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <div class="password-input-container">
                                        <input type="password" wire:model.live="new_password"
                                            class="form-control form-control-lg form-control-password" id="new_password"
                                            placeholder="Enter new password">
                                        <span class="password-toggle-icon"
                                            onclick="togglePassword('new_password', this)">
                                            <iconify-icon icon="mdi:eye-outline" width="24"
                                                height="24"></iconify-icon>
                                        </span>
                                    </div>
                                    @error('new_password')
                                        <small class="text-danger mt-1 d-block">{{ $message }}</small>
                                    @enderror
                                </div>

                                <!-- Confirm Password -->
                                <div class="col-md-6 mb-4">
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                    <div class="password-input-container">
                                        <input type="password" wire:model.live="confirm_password"
                                            class="form-control form-control-lg form-control-password"
                                            id="confirm_password" placeholder="Confirm new password">
                                        <span class="password-toggle-icon"
                                            onclick="togglePassword('confirm_password', this)">
                                            <iconify-icon icon="mdi:eye-outline" width="24"
                                                height="24"></iconify-icon>
                                        </span>
                                    </div>
                                    @if ($confirm_password && $new_password)
                                        @if ($confirm_password === $new_password)
                                            <small class="text-success mt-1 d-block">✅ Passwords match</small>
                                        @else
                                            <small class="text-danger mt-1 d-block">❌ Passwords do not match</small>
                                        @endif
                                    @endif
                                    @error('confirm_password')
                                        <small class="text-danger mt-1 d-block">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid gap-2 mt-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <div wire:loading.remove wire:target="changePassword">
                                        Update Password
                                    </div>
                                    <div wire:loading wire:target="changePassword">
                                        <span class="spinner-border spinner-border-sm" role="status"
                                            aria-hidden="true"></span>
                                        Updating...
                                    </div>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Show/Hide Password Script -->
    <script>
        function togglePassword(fieldId, iconContainer) {
            const field = document.getElementById(fieldId);
            const icon = iconContainer.querySelector("iconify-icon");

            if (field.type === "password") {
                field.type = "text";
                icon.setAttribute("icon", "mdi:eye-off-outline");
            } else {
                field.type = "password";
                icon.setAttribute("icon", "mdi:eye-outline");
            }
        }
    </script>
</div>
