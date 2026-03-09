<?php

return [


    'images' => [
        'disk' => env('VEHICLE_IMAGES_DISK', 'public'),
        'directory' => env('VEHICLE_IMAGES_DIRECTORY', 'vehicles'),
        'allowed_extensions' => explode(',', env('VEHICLE_IMAGES_ALLOWED_EXTENSIONS', 'jpg,jpeg,png,bmp')),

        /*
        |--------------------------------------------------------------------------
        | Tamanho máximo em kilobytes
        |--------------------------------------------------------------------------
        |
        | Exemplo:
        | 2048 = 2 MB
        | 5120 = 5 MB
        | 10240 = 10 MB
        |
        */
        'max_size_kb' => (int) env('VEHICLE_IMAGES_MAX_SIZE_KB', 2048),

        /*
        |--------------------------------------------------------------------------
        | Imagens base para seeders/factories
        |--------------------------------------------------------------------------
        */
        'default_image_path' => env('VEHICLE_IMAGES_DEFAULT_IMAGE_PATH', 'default.png'),
    ],
];
