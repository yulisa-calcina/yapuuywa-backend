<?php

return [
    'paths' => ['api/*'],          // Solo aplica CORS a rutas /api/
    
    'allowed_methods' => ['*'],    // GET, POST, PUT, DELETE, etc.
    
    'allowed_origins' => ['https://yapuuywa.vercel.app', 'http://localhost:5174'],
    
    'allowed_origins_patterns' => [],
    
    'allowed_headers' => ['*'],    // Acepta cualquier header
    
    'exposed_headers' => [],
    
    'max_age' => 0,
    
    'supports_credentials' => false,  // false por ahora, true cuando uses auth
];