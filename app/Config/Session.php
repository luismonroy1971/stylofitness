<?php
// Configuración de sesiones
return [
    "driver" => "file",
    "lifetime" => 120,
    "path" => __DIR__ . "/../storage/sessions",
    "cookie" => [
        "name" => "stylofitness_session",
        "path" => "/",
        "domain" => null,
        "secure" => false,
        "httponly" => true,
        "samesite" => "strict"
    ]
];
