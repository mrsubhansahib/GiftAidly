<?php

use function Livewire\Volt\{state, mount};

state([
    'rows' => [],
    'columns' => [],
]);

$load = function () {
    $this->columns = [
        "ID",
        "Name",
        "Email",
        "City",
        "Country",
        "Detail",
    ];
    // sample donors data (Detail column as object, not HTML string)
    $this->rows = [
        [201, 'Ali Raza',    'ali@example.com',   'Lahore',     'Pakistan',
            ['text' => 'Detail', 'url' => '/admin/donors/201', 'color' => 'primary']
        ],
        [202, 'Sara Khan',   'sara@example.com',  'Karachi',    'Pakistan',
            ['text' => 'Detail', 'url' => '/admin/donors/202', 'color' => 'primary']
        ],
        [203, 'Usman Dar',   'usman@example.com', 'Islamabad',  'Pakistan',
            ['text' => 'Detail',   'url' => '/admin/donors/203', 'color' => 'primary']
        ],
        [204, 'Fatima Noor', 'fatima@example.com','Multan',     'Pakistan',
            ['text' => 'Detail',   'url' => '/admin/donors/204', 'color' => 'primary']
        ],
        [205, 'Zain Malik',  'zain@example.com',  'Faisalabad', 'Pakistan',
            ['text' => 'Detail', 'url' => '/admin/donors/205', 'color' => 'primary']
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
