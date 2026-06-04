<?php

use App\Providers\AppServiceProvider;

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    App\Providers\BladeServiceProvider::class,
    \Barryvdh\DomPDF\ServiceProvider::class,
];