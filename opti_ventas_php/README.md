# Opti Ventas — Punto de venta en PHP puro (sin frameworks)

Sistema de punto de venta (POS) construido en **PHP 8.2+ puro** — sin frameworks ni librerías PHP externas — sobre **MySQL 8** con PDO. Sigue un patrón MVC artesanal con autoloading **PSR-4**, estilo de código **PSR-12** y `declare(strict_types=1)` en todos los archivos.

La interfaz usa Tailwind CSS vía CDN (no requiere build ni npm).

## Módulos y funcionalidades

| Módulo | Funcionalidad | Acceso |
|--------|---------------|--------|
| **Autenticación** | Inicio de sesión, registro y cierre de sesión | Público |
| **Dashboard** | Métricas del negocio: ventas, ganancias, stock bajo, actividad reciente | Autenticado |
| **POS** | Punto de venta: búsqueda de productos, carrito, descuentos, cobro y recibo | Autenticado |
| **Productos** | CRUD con foto, SKU, código de barras, precio, costo, stock y stock mínimo | Autenticado |
| **Categorías** | CRUD con color e identificación visual | Autenticado |
| **Clientes** | CRUD con historial de compras acumuladas | Autenticado |
| **Ventas** | Listado, detalle y anulación de ventas (la anulación restaura stock) | Autenticado / anulación solo admin |
| **Reportes** | Resumen por rango de fechas, ventas, ganancias y productos más vendidos | Autenticado |
| **Configuración** | Nombre del negocio, tasa de impuesto, moneda y pie del recibo | Solo admin |
| **Usuarios** | CRUD de usuarios del sistema, activar/desactivar y asignación de rol | Solo admin |

### Roles

- **admin** — acceso total: configuración, usuarios y anulación de ventas.
- **seller** — operaciones de venta: POS, productos, categorías, clientes y reportes.

## Requisitos

- PHP 8.2 o superior (extensiones: `pdo_mysql`, `mbstring`)
- MySQL 8.x
- Apache 2.4 con `mod_rewrite` (Laragon lo incluye activado)

## Instalación

### 1. Ubicar el proyecto en Laragon

Coloca la carpeta del proyecto en `C:\laragon\www\`:

```
C:\laragon\www\opti_ventas_php\
```

(En Laragon puedes abrir la terminal en la carpeta con *Clic derecho → Más → Shell*.)

### 2. Configurar el entorno

El proyecto incluye `.env` con valores predeterminados compatibles con Laragon (`root` sin contraseña). Ajústalo si tu MySQL difiere:

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=opti_ventas_php
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Ejecutar el instalador

```bash
php setup.php
```

El instalador crea la base de datos, el esquema (`database/schema.sql`), los datos de ejemplo y el directorio de almacenamiento. Es de uso exclusivo por CLI (el `.htaccess` bloquea su acceso web).

### 4. Abrir en el navegador

```
http://opti_ventas_php.test/
```

> El front controller `index.php` vive en la **raíz del proyecto**, por lo que Laragon sirve la aplicación directamente sin configurar virtual hosts. Si navegas como subdirectorio (`http://localhost/opti_ventas_php/`), las URLs se generan correctamente de forma automática.

### Usuarios de prueba

| Rol      | Correo                   | Contraseña |
|----------|--------------------------|------------|
| Admin    | admin@optiventas.com     | password   |
| Vendedor | vendedor@optiventas.com  | password   |

> Cambia estas contraseñas antes de usar el sistema en producción.

## Servidor embebido de PHP (alternativa sin Apache)

```bash
php -S localhost:8000 index.php
```

## Estructura del proyecto

```
opti_ventas_php/
├── index.php            # Front controller (punto de entrada único)
├── .htaccess            # Reescritura de URLs y protección de rutas
├── .env                 # Configuración de entorno (no versionar)
├── setup.php            # Instalador CLI (base de datos + seed)
├── app/
│   ├── Autoloader.php   # Autoloader PSR-4 (App\ → app/)
│   ├── bootstrap.php    # Arranque: env, helpers, config, errores, sesión
│   ├── helpers/         # Funciones globales agrupadas por dominio
│   │   ├── paths.php    #   base_path(), app_path(), config_path()…
│   │   ├── config.php   #   env(), config()
│   │   ├── output.php   #   e(), redirect(), back(), json_response()
│   │   ├── url.php      #   url(), base_url(), scheme()…
│   │   ├── request.php  #   input(), query(), json_input(), request_path()
│   │   ├── session.php  #   flash(), old(), errors()…
│   │   ├── csrf.php     #   csrf_token(), csrf_field(), csrf_token_valid()
│   │   └── format.php   #   money(), slugify(), setting(), now()
│   ├── Core/            # Router, Database (PDO), Auth, View, Validator, Env
│   ├── Controllers/     # Controladores HTTP (uno por módulo)
│   └── Models/          # Modelos con sentencias preparadas PDO
├── config/
│   ├── app.php          # Nombre, entorno, debug, URL base
│   ├── database.php     # Conexión MySQL (lee de .env)
│   └── routes.php       # Definición de rutas y dispatch
├── database/
│   └── schema.sql       # Esquema de la base de datos
├── storage/             # Archivos subidos (imágenes de productos)
└── views/               # Vistas PHP: layouts, partials y páginas
```

## Variables de entorno

| Variable       | Descripción                                       | Predeterminado     |
|----------------|---------------------------------------------------|--------------------|
| `APP_NAME`     | Nombre de la aplicación                           | `Opti Ventas`      |
| `APP_ENV`      | Entorno (`local`, `production`)                   | `production`       |
| `APP_DEBUG`    | Muestra errores en pantalla                       | `false`            |
| `APP_URL`      | URL base fija (vacía = detección automática)      | (vacía)            |
| `DB_HOST`      | Host de MySQL                                     | `127.0.0.1`        |
| `DB_PORT`      | Puerto de MySQL                                   | `3306`             |
| `DB_DATABASE`  | Nombre de la base de datos                        | `opti_ventas_php`  |
| `DB_USERNAME`  | Usuario de MySQL                                  | `root`             |
| `DB_PASSWORD`  | Contraseña de MySQL                               | (vacía)            |

> Con Laragon deja `APP_URL` vacío: la aplicación detecta automáticamente el host (`http://opti_ventas_php.test/`) e incluso el subdirectorio si se sirve desde uno.

## Rutas principales

| Método | Ruta | Descripción |
|--------|------|-------------|
| GET/POST | `/login`, `/register`, `/logout` | Autenticación |
| GET | `/dashboard` | Panel de métricas |
| GET/POST | `/pos`, `/pos/sale` | Punto de venta y cobro |
| GET/POST | `/products`, `/products/{id}/edit`… | CRUD de productos |
| GET/POST | `/categories`, `/categories/{id}/edit`… | CRUD de categorías |
| GET/POST | `/customers`, `/customers/{id}/edit`… | CRUD de clientes |
| GET | `/sales`, `/sales/{id}` | Ventas y detalle |
| POST | `/sales/{id}/void` | Anular venta (admin) |
| GET | `/reports` | Reportes con filtro `?from=&to=` |
| GET/POST | `/settings` | Configuración del negocio (admin) |
| GET/POST | `/users`, `/users/{id}/edit`… | Gestión de usuarios (admin) |

Las peticiones de modificación aceptan el verbo real (`PUT`/`DELETE`) o el campo `_method` en un POST (para formularios HTML), con rutas de respaldo `POST /recurso/{id}/delete`.

## Seguridad incluida

- **CSRF** obligatorio en toda petición no-GET (campo `_csrf` del formulario, header `X-CSRF-TOKEN` o payload JSON).
- **Sentencias preparadas PDO** en todas las consultas (sin concatenación de SQL).
- **Escape de salida** con `e()` (htmlspecialchars) en las vistas.
- **Middleware por ruta**: `auth` (sesión iniciada) y `admin` (rol administrador).
- **Contraseñas** con `password_hash()` / `password_verify()`.
- **`.htaccess`**: bloquea el acceso web a `app/`, `config/`, `views/`, `database/`, `.git/`, `.kiro/`, `.env` y `setup.php`; deshabilita listado de directorios.
- **Anulación de ventas** transaccional: restaura stock y registra movimientos.
