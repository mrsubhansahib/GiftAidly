<?php

use function Livewire\Volt\{state, mount};

state([
    'rows' => [],
    'columns' => [],
]);

$load = function () {
    // columns based on your Transaction model + Detail
    $this->columns = [
        "ID",
        "Invoice ID",
        "Stripe Transaction ID",
        "Status",
        "Paid At",
        "Detail",
    ];

    // static example rows for logged-in user (Detail as object)
    $this->rows = [
        [5001, 2001, 'txn_001AA', 'Success',  '2025-08-01 14:25',
            ['text' => 'Detail', 'url' => '/user/transactions/5001', 'color' => 'primary']
        ],
        [5002, 2002, 'txn_001BB', 'Pending',  null,
            ['text' => 'Detail', 'url' => '/user/transactions/5002', 'color' => 'primary']
        ],
        [5003, 2003, 'txn_001CC', 'Failed',   '2025-08-07 11:05',
            ['text' => 'Detail', 'url' => '/user/transactions/5003', 'color' => 'primary']
        ],
        [5004, 2004, 'txn_001DD', 'Refunded', '2025-08-10 16:30',
            ['text' => 'Detail', 'url' => '/user/transactions/5004', 'color' => 'primary']
        ],
    ];
};

mount($load);
?>

<div>
    <div class="card">
        <div class="card-body">
            <div wire:ignore>
                <div id="table-gridjs"
                     data-columns='@json($columns)'
                     data-rows='@json($rows)'></div>
            </div>
        </div>
    </div>
</div>
