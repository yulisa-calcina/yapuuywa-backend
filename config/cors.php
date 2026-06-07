<?php

return [
    'paths' => ['api/*'],          // Solo aplica CORS a rutas /api/
    
    'allowed_methods' => ['*'],    // GET, POST, PUT, DELETE, etc.
    
    'allowed_origins' => [
        'http://localhost:5174',   // Tu frontend React
        'http://localhost:5173',   // Por si cambias de puerto
    ],
    
    'allowed_origins_patterns' => [],
    
    'allowed_headers' => ['*'],    // Acepta cualquier header
    
    'exposed_headers' => [],
    
    'max_age' => 0,
    
    'supports_credentials' => false,  // false por ahora, true cuando uses auth
];