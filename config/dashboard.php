<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Dashboard Service Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default dashboard service driver that will be used
    | to fetch dashboard analytics data. You may set this to any of the
    | drivers defined in the "drivers" array below.
    |
    | Supported: "mysql", "sqlite"
    |
    */

    'driver' => env('DASHBOARD_DRIVER', 'mysql'),
];