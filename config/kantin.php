<?php

return [
    // Batas total harga yang masih boleh dibayar dengan COD.
    'cod_max_amount' => (int) env('COD_MAX_AMOUNT', 20000),
];
