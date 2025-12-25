# miStarterStack

Starter template profesional para **Laravel 12** con autenticación, RBAC (3 roles) y dashboard moderno listo para usar.

## 🎯 ¿Para quién es esto?

Si usas **XAMPP o Laragon** y quieres un punto de partida profesional sin configurar Vite, app.js o rutas, **este es tu starter**.

> **Lo mejor**: Clona, instala 2 comandos, configura `.env`, y listo. Todo está preconfigurado.

## ⚡ Instalación (5 minutos)

Instrucciones detalladas en [SETUP.md](./SETUP.md).

**Resumen rápido**:

```bash
git clone https://github.com/tuusuario/miStarterStack.git
cd miStarterStack
composer install && npm install
cp .env.example .env
php artisan key:generate
# Edita .env: BD, usuario MySQL
php artisan migrate --seed

# Dos terminales:
php artisan serve          # Terminal 1
npm run dev                # Terminal 2
```

Abre: **http://localhost:8000**

## 👥 Usuarios de prueba

Creados automáticamente:

| Email | Rol | Password |
|-------|-----|----------|
| admin@mistarter.local | Admin | 123456789 |
| standard@mistarter.local | Standard | 123456789 |
| viewer@mistarter.local | Viewer | 123456789 |

## 📦 Stack

- **Backend**: Laravel 12 + Breeze
- **Frontend**: Vue 3 + Inertia.js + Quasar v2
- **Build**: Vite 7.3.0 ✅ **Preconfigurado**
- **BD**: MySQL

## ✅ ¿Qué viene listo?

| Componente | Estado |
|-----------|--------|
| Vite | ✅ Sin tocar |
| app.js | ✅ Con Quasar + Inertia |
| vite.config.js | ✅ Auto-imports habilitados |
| Rutas | ✅ 3 dashboards por rol |
| BD | ✅ Migraciones automáticas |
| Usuarios | ✅ 3 creados con seeder |

## 📁 Estructura

```
app/Http/Controllers/
├── Admin/
│   ├── DashboardController.php
│   └── UserController.php (REST API)
├── StandardUserController.php
└── ViewerController.php

routes/web.php (todo aquí, con middleware de roles)

resources/js/
├── Layouts/ (3 layouts diferentes)
│   ├── AdminLayout.vue
│   ├── StandardLayout.vue
│   └── ViewerLayout.vue
├── Pages/ (dashboards)
│   ├── Admin/Dashboard.vue (CRUD usuarios)
│   ├── Standard/Dashboard.vue
│   └── Viewer/Dashboard.vue
└── app.js (listo, NO modificar)

vite.config.js (listo, NO modificar)
.env.example (copiar a .env)
```

## 🔐 Seguridad

- ✅ Autenticación con contraseñas hasheadas
- ✅ Middleware de roles en todas las rutas
- ✅ CSRF protection automática
- ✅ Email verification
- ✅ Password reset seguro

## 📚 Conceptos implementados

- **Inertia props**: Datos servidor → Vue props
- **REST API**: `/api/users` con CRUD en JSON
- **XSRF tokens**: Seguridad CSRF correcta con fetch()
- **Eager loading**: Relaciones BD optimizadas
- **Middleware custom**: Control de acceso por rol

## 🚀 Usar en nuevos proyectos

```bash
# Clonar (NO hacer fork)
git clone https://github.com/tuusuario/miStarterStack.git mi-nuevo-proyecto
cd mi-nuevo-proyecto

# Instalar
composer install && npm install
cp .env.example .env
php artisan key:generate

# Configurar .env (BD, usuario, contraseña)
# Luego:
php artisan migrate --seed

# Correr
php artisan serve &
npm run dev

## 🛠️ Troubleshooting

- **Error**: `Illuminate\Encryption\MissingAppKeyException`
	- **Causa**: Falta `APP_KEY` en `.env` o caché de config desactualizada.
	- **Solución**:
		```powershell
		php artisan key:generate
		php artisan config:clear
		php artisan cache:clear
		```
```

Ahora personaliza:
- Cambia layouts en `resources/js/Layouts/`
- Crea páginas en `resources/js/Pages/`
- Agrega controladores en `app/Http/Controllers/`

## 📖 Más información

- [Guía completa de instalación](./SETUP.md) con pasos detallados
- [Estructura de carpetas](#estructura)
- [Seguridad](#seguridad)

---

**¿Listo?** Sigue [SETUP.md](./SETUP.md) y tendrás el proyecto corriendo en 5 minutos. 🎉
