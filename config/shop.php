<?php

return [
    // Currency (ISO)
    'currency' => env('SHOP_CURRENCY', 'JPY'),

    // Flat shipping fee (integer, yen)
    'shipping_fee' => (int) env('SHOP_SHIPPING_FEE', 500),

    // Tax rate percentage (integer). e.g. 10 for 10%
    'tax_rate' => (float) env('SHOP_TAX_RATE', 10),

    // If true, prices stored in products include tax. If false, tax is added on top.
    'tax_included' => (bool) env('SHOP_TAX_INCLUDED', false),

    // S3 disk name used for image urls
    'image_disk' => env('SHOP_IMAGE_DISK', 's3'),
];