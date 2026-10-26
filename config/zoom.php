<?php

return [
    'account_id'    => env('ZOOM_ACCOUNT_ID'),
    'client_id'     => env('ZOOM_CLIENT_ID'),
    'client_secret' => env('ZOOM_CLIENT_SECRET'),
    'base_url'      => env('ZOOM_BASE_URL', 'https://api.zoom.us/v2/'),
    'timezone'      => env('ZOOM_TIMEZONE', 'Asia/Jakarta'),
];
