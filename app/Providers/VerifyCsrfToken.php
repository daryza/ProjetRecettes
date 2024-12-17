<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

// Exclure api de la vérification csrf

class VerifyCsrfToken extends Middleware
{
    protected $except = [
        'api/*',
    ];
}
