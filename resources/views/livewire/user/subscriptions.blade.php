<?php

use function Livewire\Volt\{state, mount};

state([
    'rows' => [],
    'columns' => [],
]);

$load = function () {
    // exactly as per your Subscription model (+ Detail)
    $this->columns = [
        "ID",
        "User ID",
        "Stripe Subscription ID",
        "Stripe Price ID",
        "Status",
        "Price",
        "Currency",
        "Type",
        "Start Date",
        "End Date",
        "Canceled At",
        "Detail",
    ];

    // static example rows for the logged-in user (Detail as object for the universal script)
    $this->rows = [
        [301, 7, 'sub_9A12XY', 'price_01', 'Active',    15.00, 'GBP', 'Monthly', '2025-07-01', '2025-09-01', null,
            ['text' => 'Detail', 'url' => '/user/subscriptions/301', 'color' => 'primary']
        ],
        [302, 7, 'sub_9B34CD', 'price_02', 'Active',   120.00, 'GBP', 'Yearly',  '2025-01-15', '2026-01-15', null,
            ['text' => 'Detail', 'url' => '/user/subscriptions/302', 'color' => 'primary']
        ],
        [303, 7, 'sub_7K45PQ', 'price_03', 'Paused',    35.00, 'GBP', 'Monthly', '2025-06-10', '2025-09-10', null,
            ['text' => 'Detail', 'url' => '/user/subscriptions/303', 'color' => 'primary']
        ],
        [304, 7, 'sub_2M89RS', 'price_01', 'Cancelled', 15.00, 'GBP', 'Monthly', '2025-03-05', null,         '2025-05-01',
            ['text' => 'Detail', 'url' => '/user/subscriptions/304', 'color' => 'primary']
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
