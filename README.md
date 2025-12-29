# Sistema de Gestión de Talleres de Verano

Sistema construido con **Laravel 12**, **Vue 3** e **Inertia.js** para la gestión de talleres de verano, inscripciones y cursantes.

---

## 📋 Tabla de Contenidos

1. [Requisitos del Sistema](#-requisitos-del-sistema)
2. [Instalación de Laragon 6](#-instalación-de-laragon-6)
3. [Instalación de Node.js](#-instalación-de-nodejs)
4. [Instalación y Configuración de phpMyAdmin](#-instalación-y-configuración-de-phpmyadmin)
5. [Descargar el Proyecto desde GitHub](#-descargar-el-proyecto-desde-github)
6. [Configuración del Proyecto](#-configuración-del-proyecto)
7. [Ejecutar el Proyecto](#-ejecutar-el-proyecto)
8. [Solución de Problemas](#-solución-de-problemas)
9. [Usuarios de Prueba](#-usuarios-de-prueba)

---

## 🖥️ Requisitos del Sistema

Antes de comenzar, asegúrate de que tu computadora cumple con estos requisitos:

- **Sistema Operativo**: Windows 10 o superior (64-bit)
- **Espacio en Disco**: Al menos 2 GB libres
- **RAM**: Mínimo 4 GB (recomendado 8 GB)
- **Conexión a Internet**: Para descargar las dependencias

---

## 📦 Instalación de Laragon 6

Laragon es un entorno de desarrollo local que incluye PHP, MySQL, Apache y otras herramientas necesarias.

### Paso 1: Descargar Laragon 6

1. Ve a la página oficial: [https://laragon.org/download/](https://laragon.org/download/)
2. Descarga **Laragon Full** (incluye PHP 8.2, MySQL, Apache, Node.js)
3. El archivo descargado se llamará algo como: `laragon-wamp.exe`

### Paso 2: Instalar Laragon

1. Ejecuta el archivo `laragon-wamp.exe` descargado
2. Sigue el asistente de instalación:
   - **Ruta de instalación**: Deja la predeterminada `C:\laragon` (recomendado)
   - **Instalación rápida**: Acepta todas las opciones por defecto
3. Al finalizar, marca la opción "Run Laragon" y haz clic en **Finish**

### Paso 3: Configurar Laragon

1. Abre Laragon (si no se abrió automáticamente)
2. Haz clic derecho en el icono de Laragon y selecciona:
   - **MySQL** → **Version** → Asegúrate de que esté en MySQL 8.x
   - **PHP** → **Version** → Debe ser PHP 8.2 o superior
3. Haz clic en **Start All** para iniciar Apache y MySQL
4. Verás indicadores verdes cuando todo esté funcionando

---

## 🟢 Instalación de Node.js

Node.js es necesario para compilar los archivos frontend (Vue.js, CSS).

### Paso 1: Verificar si Node.js ya está instalado

1. Abre Laragon
2. Haz clic en **Terminal** (botón en la interfaz de Laragon)
3. En la terminal, escribe:
   
   node -v

4. Si ves un número de versión (como `v20.x.x`), Node.js ya está instalado. **Salta al siguiente apartado**.
5. Si aparece un error "no se reconoce", continúa con el Paso 2.

### Paso 2: Descargar e Instalar Node.js

1. Ve a: [https://nodejs.org/](https://nodejs.org/)
2. Descarga la versión **LTS** (Long Term Support) - recomendada
3. Ejecuta el instalador descargado (`node-v20.x.x-x64.msi`)
4. Sigue el asistente:
   - Acepta la licencia
   - Deja la ruta de instalación por defecto
   - Marca la opción **"Automatically install the necessary tools"**
   - Haz clic en **Install**
5. Una vez instalado, **cierra y vuelve a abrir** la terminal de Laragon
6. Verifica la instalación escribiendo: `node -v` y `npm -v`

---

## �️ Instalación y Configuración de phpMyAdmin

phpMyAdmin es una herramienta web gratuita para administrar bases de datos MySQL de forma visual. En Laragon 6, necesitas instalarla manualmente.

### Paso 1: Descargar phpMyAdmin

1. Ve a la página oficial: [https://www.phpmyadmin.net/downloads/](https://www.phpmyadmin.net/downloads/)
2. Descarga la versión **más reciente** (busca el botón "Download" en la versión estable)
3. Descarga el archivo **ZIP** (no el instalador)
4. El archivo se llamará algo como: `phpMyAdmin-5.2.1-all-languages.zip`

### Paso 2: Instalar phpMyAdmin en Laragon 6

1. **Localiza el archivo descargado**:
   - Busca el archivo ZIP en tu carpeta de Descargas

2. **Extraer phpMyAdmin**:
   - Haz clic derecho en el archivo ZIP
   - Selecciona **"Extraer todo..."** o usa WinRAR/7-Zip
   - Extrae el contenido

3. **Mover a Laragon**:
   - Abre la carpeta extraída (tendrá un nombre como `phpMyAdmin-5.x.x-all-languages`)
   - **Renombra** la carpeta a simplemente: `phpmyadmin` (todo en minúsculas, sin guiones ni espacios)
   - Copia o mueve esta carpeta `phpmyadmin` a: `laragon\etc\apps\`
   - La ruta final debe ser: `C:\laragon\etc\apps\phpmyadmin\`

### Paso 3: Verificar la Instalación

1. **Asegúrate de que Laragon esté corriendo**:
   - Abre Laragon
   - Haz clic en **Start All** o **"iniciar todo"**
   - Espera a que Apache y MySQL tengan indicadores verdes

2. **Abrir phpMyAdmin**:
   - Abre tu navegador web (Chrome, Firefox, Edge)
   - Ve a: `http://localhost/phpmyadmin`
   - Deberías ver la pantalla de inicio de sesión de phpMyAdmin

3. **Iniciar sesión**:
   - **Usuario**: `root`
   - **Contraseña**: (déjala en blanco, no escribas nada)
   - Haz clic en **"Continuar"** o **"Go"**

4. **Verificar acceso**:
   - Si todo está bien, verás el panel principal de phpMyAdmin
   - En el panel izquierdo verás las bases de datos existentes

### Solución de Problemas de phpMyAdmin

**Problema: Página no encontrada (404) al abrir localhost/phpmyadmin**

**Solución**:
- Verifica que la carpeta esté en: `C:\laragon\etc\apps\phpmyadmin\` (todo en minúsculas)
- Verifica que Apache esté corriendo en Laragon
- Intenta reiniciar Apache: En Laragon → Detener Apache → Iniciar Apache

**Problema: Acceso denegado para el usuario 'root'**

**Solución**: En Laragon 6, por defecto MySQL no tiene contraseña para root. Deja el campo de contraseña vacío.

---

## 📥 Descargar el Proyecto desde GitHub

Ahora que tienes Laragon, Node.js y phpMyAdmin instalados, es momento de descargar el proyecto.

### Opción 1: Clonar con Git (Recomendado)

Si tienes Git instalado (viene incluido con Laragon), esta es la mejor opción:

1. **Abre la terminal de Laragon**:
   - Haz clic derecho en el icono de Laragon
   - Selecciona **Terminal** o **Cmder/Terminal**

2. **Navega a la carpeta www de Laragon**:
   
   cd C:\laragon\www
   

3. **Clona el repositorio**:
   
   git clone https://github.com/gadecima/talleresVerano.git
   
   Verás mensajes indicando que el proyecto se está descargando. Esto puede tardar 1-3 minutos dependiendo de tu conexión a internet.

4. **Verifica que se descargó correctamente**:
   - Deberías ver una nueva carpeta `talleresVerano` en `C:\laragon\www\`
   - La carpeta debe contener archivos como `artisan`, `composer.json`, `package.json`, `.env`  etc.

### Opción 2: Descargar como ZIP

Si prefieres no usar Git:

1. **Ve al repositorio en GitHub**:
   - Abre tu navegador y ve a: [https://github.com/gadecima/talleresVerano](https://github.com/gadecima/talleresVerano)

2. **Descarga el proyecto**:
   - Haz clic en el botón verde **"Code"**
   - Selecciona **"Download ZIP"**
   - Guarda el archivo `talleresVerano-main.zip` en tu computadora

3. **Extrae el proyecto**:
   - Haz clic derecho en el archivo descargado
   - Selecciona **"Extraer todo..."** o **"Extract Here"**
   - Mueve la carpeta extraída a `C:\laragon\www\`
   - **Importante**: Renombra la carpeta de `talleresVerano-main` a `talleresVerano` (sin el `-main`)

### Verificar la descarga

Independientemente del método que uses, verifica que:
- La carpeta esté en: `C:\laragon\www\talleresVerano`
- Dentro de la carpeta veas archivos como: `artisan`, `composer.json`, carpetas `app/`, `resources/`, etc.

---

## ⚙️ Configuración del Proyecto

### Paso 1: Abrir Terminal en el Proyecto

1. En Laragon, haz clic derecho en tu proyecto `talleresVerano`
2. Selecciona **Terminal** (se abrirá una ventana de PowerShell en la carpeta del proyecto)

### Paso 2: Instalar Dependencias de PHP (Composer)

En la terminal que acabas de abrir, ejecuta:

```bash
composer install
```

Esto descargará todas las librerías de PHP necesarias. **Puede tardar 2-5 minutos**.

### Paso 3: Instalar Dependencias de JavaScript (NPM)

En la misma terminal, ejecuta:

```bash
npm install
```

Esto descargará Vue.js, Quasar, Vite y otras herramientas frontend. **Puede tardar 3-7 minutos**.

### Paso 4: Configurar el Archivo de Entorno (.env)

1. En la carpeta del proyecto, busca el archivo `.env.example`
2. **Copia** ese archivo y renómbralo a `.env` (sin el `.example`)
3. Abre el archivo `.env` con un editor de texto (Notepad++, VS Code, o el Bloc de notas)
4. Modifica las siguientes líneas:

env
APP_NAME="Talleres de Verano"
APP_URL=http://talleresverano.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=talleres_verano
DB_USERNAME=root
DB_PASSWORD=


**Importante**:
- Esas líneas estarán comentadas con #, quitarlo. 
- `DB_DATABASE`: Este será el nombre de tu base de datos
- `DB_USERNAME`: Por defecto en Laragon es `root`
- `DB_PASSWORD`: Por defecto en Laragon está **vacío** (déjalo así)

5. **Guarda el archivo** `.env`

### Paso 5: Generar Clave de Aplicación

En la terminal, ejecuta:

```bash
php artisan key:generate
```

Verás un mensaje: "Application key set successfully."

### Paso 6: Crear la Base de Datos

**Opción A: Usando phpMyAdmin (Interfaz Gráfica)**

1. **Iniciar Laragon**: Asegúrate de que Laragon esté ejecutándose (haz clic en **Start All**)

2. **Abrir phpMyAdmin**:
   - Abre tu navegador y ve a: `http://localhost/phpmyadmin`

3. **Iniciar sesión**:
   - Usuario: `root`
   - Contraseña: (déjala en blanco, presiona Enter)

4. **Crear la base de datos**:
   - Haz clic en la pestaña **"Bases de datos"** o **"Databases"** en la parte superior
   - En el campo **"Crear base de datos"**, escribe: `talleres_verano`
   - En el menú desplegable **"Cotejamiento"**, selecciona: `utf8mb4_unicode_ci`
   - Haz clic en el botón **"Crear"**

5. **Verificar**: Deberías ver `talleres_verano` en la lista de bases de datos del panel izquierdo

**Opción B: Usando Terminal**

1. En la terminal de Laragon, ejecuta:
   ```bash
   mysql -u root -e "CREATE DATABASE talleres_verano CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   ```

### Paso 7: Ejecutar Migraciones y Seeders

En la terminal, ejecuta:

```bash
php artisan migrate --seed
```

Este comando:
- Creará todas las tablas en la base de datos (users, roles, talleres, inscripciones, etc.)
- Insertará datos de prueba (usuarios, roles, permisos)

Deberías ver una lista de migraciones ejecutadas exitosamente.

---

## 🚀 Ejecutar el Proyecto

Para que el proyecto funcione correctamente, necesitas **DOS terminales** ejecutándose simultáneamente.

### Terminal 1: Servidor PHP (Backend)

1. Abre una terminal en el proyecto (clic derecho en Laragon → Terminal)
2. Ejecuta:
   
   ```bash
   php artisan serve
   ```

3. Verás un mensaje: `Laravel development server started: http://127.0.0.1:8000`
4. **¡NO CIERRES ESTA TERMINAL!** Debe quedar ejecutándose

### Terminal 2: Compilador Vite (Frontend)

1. Abre una **segunda terminal** en el proyecto:
   - En Laragon, haz clic derecho nuevamente y selecciona **Terminal**
2. Ejecuta:

   ```bash
   npm run dev
   ```
   
3. Verás mensajes de compilación de Vite
4. Al final verás: `VITE ready in xxx ms`
5. **¡NO CIERRES ESTA TERMINAL!** Debe quedar ejecutándose


### Abrir la Aplicación

1. Abre tu navegador web (Chrome, Firefox, Edge)
2. Ve a: **http://localhost:8000**
3. Deberías ver la pantalla de inicio de sesión del sistema

---

## 👥 Usuarios de Prueba

El sistema viene con usuarios de prueba precargados. Puedes usar cualquiera de estos para iniciar sesión:

| Email | Contraseña | Rol | Permisos |
|-------|-----------|-----|----------|
| admin@mistarter.local | 123456789 | Administrador | Acceso total al sistema |
| standard@mistarter.local | 123456789 | Usuario Estándar | Gestión de talleres e inscripciones |
| viewer@mistarter.local | 123456789 | Visualizador | Solo lectura |

---

## 🔧 Solución de Problemas

### Problema: "No such file or directory" al ejecutar comandos

**Solución**: Asegúrate de estar en la carpeta correcta del proyecto.


cd C:\laragon\www\talleresVerano


### Problema: Apache o MySQL no inician en Laragon

**Solución**: 
1. Verifica que no haya otros programas usando los puertos 80 (Apache) o 3306 (MySQL)
2. Cierra Skype, otros servidores web como XAMPP
3. En Laragon, haz clic en **Stop All** y luego **Start All**

### Problema: "SQLSTATE[HY000] [1045] Access denied for user"

**Solución**: Las credenciales de la base de datos son incorrectas.
1. Abre el archivo `.env`
2. Verifica que `DB_USERNAME=root` y `DB_PASSWORD=` (vacío)
3. Guarda y ejecuta: `php artisan config:clear`

### Problema: "Vite manifest not found"

**Solución**: 
1. Asegúrate de que el comando `npm run dev` esté ejecutándose en una terminal
2. Si no funciona, ejecuta: `npm run build` y luego inicia nuevamente

### Problema: Página en blanco o error 500

**Solución**:
1. Verifica los logs en: `storage/logs/laravel.log`
2. Ejecuta estos comandos para limpiar caché:
   
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   php artisan route:clear
   

### Problema: "Node.js no reconocido" después de instalar

**Solución**:
1. Cierra completamente Laragon y todas las terminales
2. Reinicia Laragon
3. Abre una nueva terminal

### Problema: Errores de permisos en carpetas

**Solución**:

php artisan storage:link


---

## 📊 Estructura del Proyecto


talleresVerano/
├── app/                    # Código PHP (Modelos, Controladores)
│   ├── Http/Controllers/   # Lógica de negocio
│   └── Models/            # Modelos de base de datos
├── database/
│   ├── migrations/        # Estructura de tablas
│   └── seeders/          # Datos de prueba
├── resources/
│   ├── js/               # Código Vue.js (Frontend)
│   │   ├── Pages/        # Páginas de la aplicación
│   │   └── Components/   # Componentes reutilizables
│   └── css/              # Estilos CSS
├── routes/               # Rutas de la aplicación
│   └── web.php          # Rutas principales
└── .env                 # Configuración (¡NO compartir!)


---

## 🛠️ Comandos Útiles

### Detener los servidores

- Presiona `Ctrl + C` en cada una de las terminales donde ejecutaste `php artisan serve` y `npm run dev`

### Reiniciar el proyecto


# En una terminal:
php artisan serve

# En otra terminal:
npm run dev


### Ver la base de datos

**Con phpMyAdmin**:
1. En tu navegador, ve a: `http://localhost/phpmyadmin`
2. Inicia sesión con usuario `root` (sin contraseña)
3. En el panel izquierdo, haz clic en `talleres_verano`
4. Selecciona cualquier tabla para ver los datos

**Con Terminal**:
```bash
mysql -u root talleres_verano
```

### Limpiar caché


php artisan optimize:clear


---

## 📚 Tecnologías Utilizadas

- **Backend**: Laravel 12 (PHP 8.2)
- **Frontend**: Vue 3 + Inertia.js + Quasar v2
- **Base de Datos**: MySQL 8
- **Build Tool**: Vite 7
- **Estilos**: Tailwind CSS

---

## 📞 Soporte

Si tienes problemas:

1. Revisa la sección [Solución de Problemas](#-solución-de-problemas)
2. Verifica los logs en: `storage/logs/laravel.log`
3. Asegúrate de que todas las dependencias estén instaladas correctamente

---

## ✅ Checklist de Instalación

Usa esta lista para verificar que completaste todos los pasos:

- [ ] Laragon 6 instalado y funcionando
- [ ] Node.js instalado (verificado con `node -v`)
- [ ] Proyecto descargado/clonado desde GitHub
- [ ] Proyecto ubicado en `C:\laragon\www\talleresVerano`
- [ ] `composer install` ejecutado exitosamente
- [ ] `npm install` ejecutado exitosamente
- [ ] Archivo `.env` creado y configurado
- [ ] `php artisan key:generate` ejecutado
- [ ] Base de datos `talleres_verano` creada
- [ ] `php artisan migrate --seed` ejecutado
- [ ] Servidor PHP corriendo (`php artisan serve`)
- [ ] Vite corriendo (`npm run dev`)
- [ ] Aplicación abierta en http://localhost:8000
- [ ] Inicio de sesión exitoso con usuario de prueba

---

**¡Listo! 🎉** Ahora tienes el sistema de Talleres de Verano funcionando en tu computadora.


- **Eager loading**: Relaciones BD optimizadas
- **Middleware custom**: Control de acceso por rol

## 🚀 Usar en nuevos proyectos


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
		powershell
		php artisan key:generate
		php artisan config:clear
		php artisan cache:clear
		
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
