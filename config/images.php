<?php

return [

    /*
    |--------------------------------------------------------------------------
    | JPEG output quality (1–100)
    |--------------------------------------------------------------------------
    */

    'jpeg_quality' => (int) env('IMAGE_JPEG_QUALITY', 85),

    /*
    |--------------------------------------------------------------------------
    | Maximum dimensions (longest side fits inside this box; aspect preserved)
    |--------------------------------------------------------------------------
    */

    'categories' => [
        'max_width' => (int) env('IMAGE_CATEGORY_MAX_WIDTH', 900),
        'max_height' => (int) env('IMAGE_CATEGORY_MAX_HEIGHT', 900),
    ],

    'products' => [
        'max_width' => (int) env('IMAGE_PRODUCT_MAX_WIDTH', 1400),
        'max_height' => (int) env('IMAGE_PRODUCT_MAX_HEIGHT', 1400),
    ],

];
