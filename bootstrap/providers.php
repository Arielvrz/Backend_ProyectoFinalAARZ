<?php

use App\Providers\AppServiceProvider;
use L5Swagger\L5SwaggerServiceProvider; // AGREGA ESTA LÍNEA

return [
    AppServiceProvider::class,
    L5SwaggerServiceProvider::class, // AHORA QUEDA ASÍ, MÁS LIMPIO
];