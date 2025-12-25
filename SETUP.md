# Guía de Instalación - miStarterStack

## Requisitos

- **PHP 8.2+** (incluido en XAMPP/Laragon)
- **Node.js 24+** (o v20+) - [Descargar](https://nodejs.org)
- **MySQL/MariaDB** (incluido en XAMPP/Laragon)
- **Git** - [Descargar](https://git-scm.com)

## Instalación paso a paso

### 1. Clonar el repositorio
```bash
git clone https://github.com/tuusuario/miStarterStack.git
cd miStarterStack
```

### 2. Instalar dependencias PHP
```bash
composer install
```

### 3. Instalar dependencias Node.js
```bash
npm install
```

### 4. Configurar variables de entorno

Copia el archivo de ejemplo:
```bash
cp .env.example .env
```

Abre `.env` con tu editor y configura:
```env
DB_DATABASE=starter          # Nombre de la BD a crear
DB_USERNAME=root             # Usuario MySQL (por defecto en XAMPP/Laragon)
DB_PASSWORD=                 # Contraseña MySQL (vacío si no tienes)
```

### 5. Generar clave de aplicación
```bash
php artisan key:generate
```

### 6. Crear BD y cargar datos de prueba
```bash
php artisan migrate --seed
```

Esto crea:
- Tablas de BD
- 3 roles: Admin, Standard, Viewer
- 3 usuarios de prueba

### 7. Compilar assets (Vite está preconfigurado)

Para **desarrollo** (hot reload):
```bash
npm run dev
```

Para **producción** (compilar una sola vez):
```bash
npm run build
```

### 8. Ejecutar servidor

Abre **DOS terminales distintas**:

**Terminal 1** - Laravel:
```bash
php artisan serve
```

**Terminal 2** - Vite (hot reload):
```bash
npm run dev
```

### 9. Acceder a la aplicación

Abre en tu navegador:
```
http://localhost:8000
```

## Usuarios de prueba

| Email | Rol | Password |
|-------|-----|----------|
| admin@mistarter.local | Admin | 123456789 |
| standard@mistarter.local | Standard | 123456789 |
| viewer@mistarter.local | Viewer | 123456789 |

## Notas importantes

✅ **Vite ya está configurado** - No necesitas hacer nada más
✅ **app.js y vite.config.js están listos** - Incluyen Quasar, Inertia, Ziggy
✅ **Bases de datos automáticas** - Las migraciones y seeders lo hacen todo
✅ **Mantén dos terminales abiertas** - Una para artisan, otra para npm
✅ **Los cambios en Vue se actualizan en tiempo real** con `npm run dev`

## Estructura de carpetas útiles

```
app/Http/Controllers/
├── Admin/
│   ├── DashboardController.php
│   └── UserController.php (REST API)
├── StandardUserController.php
└── ViewerController.php

resources/js/
├── Layouts/
│   ├── AdminLayout.vue
│   ├── StandardLayout.vue
│   └── ViewerLayout.vue
├── Pages/
│   ├── Admin/Dashboard.vue
│   ├── Standard/Dashboard.vue
│   └── Viewer/Dashboard.vue
│
routes/web.php - Todas las rutas
```

## Si algo no funciona

1. **Error "composer not found"**: Instala [Composer](https://getcomposer.org)
2. **Error "npm not found"**: Instala [Node.js](https://nodejs.org) (trae npm)
3. **Error de BD**: Crea una BD llamada `starter` en phpMyAdmin/MySQL
4. **Puerto 8000 ocupado**: Cambia a otro puerto:
   ```bash
   php artisan serve --port=8001
   ```
5. **Vite no compila**: Asegúrate de tener Node.js v24+ o v20+:
   ```bash
   node --version
   ```

¡Listo! Ahora puedes empezar a desarrollar. 🚀
