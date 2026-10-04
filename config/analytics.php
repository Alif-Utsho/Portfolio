<?php

return [
    'geoip_database' => env('MAXMIND_GEOIP_DATABASE', storage_path('app/geoip/GeoLite2-City.mmdb')),
];
