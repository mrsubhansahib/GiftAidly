<?php

use function Livewire\Volt\{state, mount};

state([
    'rows' => [],
    'columns' => [],
]);

$load = function () {
    // Columns based on your Transaction model
    $this->columns = [
        "ID",
        "Invoice ID",
        "Stripe Transaction ID",
        "Status",
        "Paid At",
        "Detail",
    ];

    // Static sample transactions with Detail column as object
    $this->rows = [
        [5001, 1001, 'txn_001AA', 'Success',  '2025-08-01 14:25',
            ['text' => 'Detail', 'url' => '/admin/transactions/5001', 'color' => 'primary']
        ],
        [5002, 1002, 'txn_001BB', 'Pending',  null,
            ['text' => 'Detail', 'url' => '/admin/transactions/5002/retry', 'color' => 'primary']
        ],
        [5003, 1003, 'txn_001CC', 'Success',  '2025-08-05 18:42',
            ['text' => 'Detail', 'url' => '/admin/transactions/5003', 'color' => 'primary']
        ],
        [5004, 1004, 'txn_001DD', 'Failed',   '2025-08-07 11:05',
            ['text' => 'Detail', 'url' => '/admin/transactions/5004', 'color' => 'primary']
        ],
        [5005, 1005, 'txn_001EE', 'Refunded', '2025-08-10 16:30',
            ['text' => 'Detail', 'url' => '/admin/transactions/5005', 'color' => 'primary']
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
