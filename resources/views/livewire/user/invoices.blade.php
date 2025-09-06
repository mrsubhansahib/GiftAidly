<?php

use function Livewire\Volt\{state, mount};

state([
    'rows' => [],
    'columns' => [],
]);

$load = function () {
    // columns for user invoices + Detail
    $this->columns = ["ID", "Amount", "Status", "Due Date", "Detail"];

    // static example rows for one logged-in user (all Detail buttons)
    $this->rows = [
        [201, 129.99, 'Paid',    '2025-08-01',
            ['text' => 'Detail', 'url' => '/user/invoices/201', 'color' => 'primary']
        ],
        [202, 75.50,  'Unpaid',  '2025-08-05',
            ['text' => 'Detail', 'url' => '/user/invoices/202', 'color' => 'primary']
        ],
        [203, 260.00, 'Overdue', '2025-07-28',
            ['text' => 'Detail', 'url' => '/user/invoices/203', 'color' => 'primary']
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
