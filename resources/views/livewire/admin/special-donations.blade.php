<?php

use Livewire\Volt\Component;
use App\Models\SpecialDonation;
use Livewire\Attributes\On;


new class extends Component {
    public $donations = [];
    public $showModal = false;
    public $editingId = null;

    // form fields
    public $name;
    public $price;
    public $currency = 'gbp'; // fixed

    public function mount()
    {
        $this->loadDonations();
    }

    public function loadDonations()
    {
        $this->donations = SpecialDonation::latest()->get()->toArray();
    }

    public function create()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $donation = SpecialDonation::findOrFail($id);
        $this->editingId = $id;
        $this->name = $donation->name;
        $this->price = $donation->price;
        // currency always GBP
        $this->currency = 'gbp';
        $this->showModal = true;
    }

    public function save()
    {
        $this->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric',
        ]);

        if ($this->editingId) {
            SpecialDonation::findOrFail($this->editingId)->update([
                'name'     => $this->name,
                'price'    => $this->price,
                'currency' => 'gbp',
            ]);
            session()->flash('success', 'Donation updated successfully!');
            $this->dispatch('toast', type: 'success', message: 'Donation updated successfully!');
        } else {
            SpecialDonation::create([
                'name'     => $this->name,
                'price'    => $this->price,
                'currency' => 'gbp',
            ]);
            session()->flash('success', 'Donation created successfully!');
             $this->dispatch('toast', type: 'success', message: 'Donation created successfully!');
        }

        $this->loadDonations();
        $this->resetForm();
        $this->showModal = false;
    }

    public function delete($id)
    {
        SpecialDonation::findOrFail($id)->delete();
        $this->loadDonations();
        session()->flash('success', 'Donation deleted successfully!');
            $this->dispatch('toast', type: 'danger', message: 'Donation deleted successfully!');

    }

    private function resetForm()
    {
        $this->editingId = null;
        $this->name      = '';
        $this->price     = '';
        $this->currency  = 'gbp';
    }
};
?>


<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-end mb-3">
                        <button wire:click="create" class="btn text-light" style="background-color:#0B539B;">
                            + Add
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table id="datatable" class="table table-striped table-bordered align-middle">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Currency</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($donations as $donation)
                                    <tr>
                                        <td>{{ $donation['name'] }}</td>
                                        <td>{{ number_format($donation['price']) }}</td>
                                        <td>{{ strtoupper($donation['currency']) }}</td>
                                        <td class="text-center">
                                            <button wire:click="edit({{ $donation['id'] }})"
                                                class="btn btn-sm text-white"
                                                style="background-color:#0B539B;">
                                                Edit
                                            </button>
                                            <button wire:click="delete({{ $donation['id'] }})"
                                                class="btn btn-sm btn-danger">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No donations found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ Modal -->
    @if ($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,.5);">
            <div class="modal-dialog modal-md modal-dialog-centered">
                <div class="modal-content border-0 rounded-4 shadow-lg">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-semibold">
                            {{ $editingId ? 'Edit Donation' : 'Add Donation' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="$set('showModal', false)"></button>
                    </div>
                    <div class="modal-body px-4 pb-4">
                        <form wire:submit.prevent="save">
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" wire:model="name" class="form-control">
                                @error('name') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Price</label>
                                <input type="number" wire:model="price" class="form-control">
                                @error('price') <div class="text-danger small">{{ $message }}</div> @enderror
                            </div>

                            <!-- ✅ Currency always GBP -->
                            <div class="mb-3">
                                <label class="form-label">Currency</label>
                                <input type="text" class="form-control" value="GBP" disabled>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-secondary" wire:click="$set('showModal', false)">
                                    Cancel
                                </button>
                                <button type="submit" class="btn text-light" style="background-color:#0B539B;">
                                    Save
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
