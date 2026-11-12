<?php

return [
    'dump_binary' => env('DB_DUMP_BINARY', 'mysqldump'),
    'timeout' => (int) env('DB_DUMP_TIMEOUT', 300),
];
