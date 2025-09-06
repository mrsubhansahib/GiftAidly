<?php

use function Livewire\Volt\{state, mount};

state([
    'rows' => [],
    'columns' => [],
]);

$load = function () {
    // Columns reflect your model fields + a Detail action
    $this->columns = [
        "ID",
        "Subscription ID",
        "Stripe Invoice ID",
        "Amount Due",
        "Currency",
        "Invoice Date",
        "Paid At",
        "Detail",
    ];

    // Static sample rows (Detail as object with text/url/color)
    $this->rows = [
        [1001, 'sub_9A12XY', 'in_0001A', 129.99, 'GBP', '2025-08-01', '2025-08-01 10:12',
            ['text' => 'Detail', 'url' => '/admin/invoices/1001', 'color' => 'primary']
        ],
        [1002, 'sub_9A12XY', 'in_0001B',  75.50, 'GBP', '2025-08-05', null,
            ['text' => 'Detail', 'url' => '/admin/invoices/1002/retry', 'color' => 'primary']
        ],
        [1003, 'sub_7K45PQ', 'in_0001C', 260.00, 'GBP', '2025-07-28', null,
            ['text' => 'Detail', 'url' => '/admin/invoices/1003', 'color' => 'primary']
        ],
        [1004, 'sub_2M89RS', 'in_0001D',  49.00, 'GBP', '2025-08-02', '2025-08-02 08:45',
            ['text' => 'Detail', 'url' => '/admin/invoices/1004', 'color' => 'primary']
        ],
        [1005, 'sub_2M89RS', 'in_0001E', 310.75, 'GBP', '2025-08-10', null,
            ['text' => 'Detail', 'url' => '/admin/invoices/1005', 'color' => 'primary']
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
