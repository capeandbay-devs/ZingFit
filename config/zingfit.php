<?php

return [
    'production_url' => 'https://api.zingfitlab.com',
    'sandbox_url' => 'https://api.zingfit.com',
    'client_id' => env('ZINGFIT_CLIENT_ID', '__CLIENT_ID__'),
    'client_secret' => env('ZINGFIT_CLIENT_SECRET', '__SECRET__'),
    'client_tenant_id' => env('ZINGFIT_TENANT_ID', '__TENANT__')
];
