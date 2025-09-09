<?php

use function Livewire\Volt\{state, mount};

state([
    'rows' => [],
    'columns' => [],
]);

$load = function () {
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

    // static sample subscriptions (Detail column as object)
    $this->rows = [
        [301, 1, 'sub_9A12XY', 'price_01', 'Active',    15.00, 'GBP', 'Monthly', '2025-07-01', '2025-09-01', null,
            ['text' => 'Detail', 'url' => '/admin/subscriptions/301', 'color' => 'primary']
        ],
        [302, 2, 'sub_9B34CD', 'price_02', 'Active',   120.00, 'GBP', 'Yearly',  '2025-01-15', '2026-01-15', null,
            ['text' => 'Detail', 'url' => '/admin/subscriptions/302', 'color' => 'primary']
        ],
        [303, 3, 'sub_7K45PQ', 'price_03', 'Paused',    35.00, 'GBP', 'Monthly', '2025-06-10', '2025-09-10', null,
            ['text' => 'Detail', 'url' => '/admin/subscriptions/303/resume', 'color' => 'primary']
        ],
        [304, 4, 'sub_2M89RS', 'price_01', 'Cancelled', 15.00, 'GBP', 'Monthly', '2025-03-05', null, '2025-05-01',
            ['text' => 'Detail', 'url' => '/admin/subscriptions/304', 'color' => 'primary']
        ],
        [305, 5, 'sub_8H77TT', 'price_04', 'Active',   300.00, 'GBP', 'Yearly',  '2025-08-10', '2026-08-10', null,
            ['text' => 'Detail', 'url' => '/admin/subscriptions/305', 'color' => 'primary']
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
