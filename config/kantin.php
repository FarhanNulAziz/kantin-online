<?php

return [
    // Batas subtotal menu (tanpa biaya admin) yang masih boleh dibayar dengan COD.
    'cod_max_amount' => (int) env('COD_MAX_AMOUNT', 20000),
    'admin_fee' => (int) env('ADMIN_FEE', 1000),
];
