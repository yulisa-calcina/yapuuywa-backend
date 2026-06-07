# YapuUywa SGA v1.1
## Sistema de Gestión Agropecuaria

> Plataforma web de gestión agropecuaria para el altiplano puneño.
> Desarrollado con React 18 + Laravel 11 + MySQL 8.

---

## Requisitos previos

| Herramienta | Versión mínima |
|-------------|---------------|
| Node.js     | 20+           |
| PHP         | 8.3+          |
| Composer    | 2+            |
| MySQL       | 8.0+          |
| Laragon     | Recomendado   |

---

## Instalación — Backend (Laravel)

```bash
# 1. Entrar a la carpeta del backend
cd yapuuywa-backend

# 2. Instalar dependencias PHP
composer install

# 3. Copiar y configurar variables de entorno
cp .env.example .env

# 4. Editar .env con tu base de datos:
#    DB_DATABASE=yapuuywa
#    DB_USERNAME=root
#    DB_PASSWORD=

# 5. Generar clave de aplicación
php artisan key:generate

# 6. Crear base de datos en MySQL (HeidiSQL o phpMyAdmin):
#    nombre: yapuuywa  |  cotejamiento: utf8mb4_unicode_ci

# 7. Ejecutar migraciones
php artisan migrate

# 8. Crear usuario administrador inicial
php artisan db:seed --class=AdminSeeder

# 9. Instalar Laravel Sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# 10. Levantar el servidor
php artisan serve
# → http://127.0.0.1:8000
```

---

## Instalación — Frontend (React + Vite)

```bash
# 1. Entrar a la carpeta del frontend
cd yapuuywa-frontend

# 2. Instalar dependencias
npm install

# 3. Variables de entorno (ya incluido en .env)
#    VITE_API_URL=http://127.0.0.1:8000/api

# 4. Levantar servidor de desarrollo
npm run dev
# → http://localhost:5174

# 5. Build para producción (Vercel)
npm run build
```

---

## Credenciales iniciales

| Rol            | DNI      | Contraseña |
|----------------|----------|------------|
| Administrador  | 12345678 | Admin@2026 |

> Cambia la contraseña después del primer ingreso.

---

## Estructura del proyecto

```
yapuuywa-frontend/
├── src/
│   ├── api/           # axios.config.js + services.js
│   ├── context/       # AuthContext.jsx (JWT global)
│   ├── hooks/         # useGanado, useDashboard, useAlertas, useToast
│   ├── components/
│   │   ├── layout/    # Topnav, Sidebar, AppLayout
│   │   └── ui/        # Button, Modal, Badge, Toast, Form...
│   └── pages/
│       ├── auth/      # Login.jsx
│       ├── dashboard/ # Dashboard.jsx (RF15, auto-refresh 60s)
│       ├── ganado/    # GanadoList.jsx (RF03, RF14)
│       └── sanitario/ # Sanitario.jsx (RF04, RF05)

yapuuywa-backend/
├── app/
│   ├── Console/Commands/  # GenerarAlertas.php (cron 06:00)
│   ├── Http/Controllers/Api/
│   │   ├── AuthController.php      # RF01 — DNI + bcrypt
│   │   ├── AnimalController.php    # RF03, RF14
│   │   ├── DashboardController.php # RF15
│   │   └── ...
│   └── Models/
├── database/migrations/   # Esquema completo RF01–RF15
└── routes/api.php         # 28 endpoints REST
```

---

## Configuración CORS (bootstrap/app.php)

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->api(prepend: [
        \Illuminate\Http\Middleware\HandleCors::class,
    ]);
})
```

`config/cors.php`:
```php
'allowed_origins' => ['http://localhost:5174'],
```

---

## Cron job de alertas (RF05)

```bash
# Ejecutar manualmente:
php artisan alertas:vacunacion

# Configurar en Task Scheduler (Windows) o crontab (Linux):
# 0 6 * * * php /ruta/yapuuywa-backend/artisan alertas:vacunacion
```

---

## Deploy en Vercel (frontend)

```bash
npm run build
# Subir carpeta dist/ a Vercel
# Variable de entorno en Vercel:
# VITE_API_URL=https://tu-backend.onrender.com/api
```

---

## Módulos implementados

| Módulo           | RF      | Estado       |
|------------------|---------|--------------|
| Autenticación    | RF01    | ✅ Completo   |
| Usuarios / RBAC  | RF02    | ✅ Completo   |
| Inventario ganado| RF03    | ✅ Completo   |
| Historial médico | RF04    | ✅ Completo   |
| Alertas vacunas  | RF05    | ✅ Completo   |
| Parcelas         | RF06    | 🔄 En desarrollo |
| Cultivos         | RF07    | 🔄 En desarrollo |
| Insumos          | RF08    | 🔄 En desarrollo |
| Alertas stock    | RF09    | 🔄 En desarrollo |
| Personal/jornales| RF10    | 🔄 En desarrollo |
| Producción/ventas| RF11    | 🔄 En desarrollo |
| Gastos           | RF12    | 🔄 En desarrollo |
| Reportes PDF     | RF13    | 🔄 En desarrollo |
| Nacimientos/bajas| RF14    | ✅ Completo   |
| Dashboard KPI    | RF15    | ✅ Completo   |

---

*YapuUywa — Sistema de Gestión Agropecuaria · MIT License*
