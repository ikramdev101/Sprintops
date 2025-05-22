<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;
use App\Http\Middleware\CheckProjectAccess;

class Kernel extends HttpKernel
{
    protected $middleware = [
        // Global middleware, including CORS
        \Illuminate\Http\Middleware\HandleCors::class,
        // Other middleware...
        'project.access' => \App\Http\Middleware\CheckProjectAccess::class,
    ];

    // Other properties...
}
