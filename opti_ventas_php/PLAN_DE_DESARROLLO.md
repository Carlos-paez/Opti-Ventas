# Plan de Desarrollo y Puesta a Punto: Opti-Ventas PHP + MySQL

Este documento detalla el diagnóstico técnico, la arquitectura, los hallazgos y el plan de acción estructurado para dejar la aplicación **Opti-Ventas** al 100% de su capacidad operativa, robusta y alineada a las mejores prácticas de la industria.

---

## 1. Diagnóstico del Entorno y Estado del Proyecto

- **Lenguaje:** PHP 8.3.30 (con extensiones activas `pdo_mysql`, `mysqli`, `mbstring`, `gd`, `curl`, `filter`, `session`).
- **Motor de Base de Datos:** MySQL 8.x en `127.0.0.1:3306`. Base de datos: `opti_ventas_php`.
- **Arquitectura:** Patrón MVC artesanal estructurado:
  - `app/Core/`: Router, Database (PDO Wrapper), Auth, View, Validator.
  - `app/Controllers/`: Auth, Dashboard, Pos, Product, Category, Customer, Sale, Report, Setting, User.
  - `app/Models/`: Modelos con sentencias preparadas en PDO.
  - `views/`: Vistas PHP con layouts, partials y Tailwind CSS.
  - `public/`: Punto de entrada único (`index.php`) con reescritura de URLs mediante `.htaccess`.

---

## 2. Hallazgos Críticos Identificados

### 2.1. Vulnerabilidad de Seguridad en Rutas Administrativas
- **Causa:** En `routes.php`, la variable `$admin` se definió como `$auth + ['admin'];`. En PHP, el operador de unión de arrays (`+`) preserva las claves numéricas existentes; como `$auth` contenía `[0 => 'auth']`, el elemento `'admin'` (también índice `0`) se descartaba silenciosamente.
- **Impacto:** Las rutas de configuración de la empresa, creación/edición de usuarios del sistema y la anulación de ventas quedaban expuestas a cualquier usuario autenticado (incluidos vendedores regulares).

### 2.2. Error en la Eliminación (DELETE) de Productos, Categorías y Clientes
- **Causa:** En `Router.php`, el método `post()` aceptaba indiscriminadamente métodos `POST, PUT, PATCH, DELETE`. En `routes.php`, la ruta de actualización `/products/{id}` se registraba antes de la eliminación `/products/{id}/delete`. En las vistas, los botones de eliminar enviaban un formulario a `/products/{id}` con `_method=DELETE`.
- **Impacto:** Al intentar eliminar cualquier registro, el enrutador ejecutaba el método `update()` en lugar de `destroy()`. La validación de actualización fallaba y la interfaz redirigía a editar el producto, haciendo imposible borrar registros.

### 2.3. Fallo de Integridad en MySQL por Cadenas Vacías en Columnas Únicas Opcionales
- **Causa:** Las columnas `sku` y `barcode` en `products`, y `email` en `customers`, cuentan con índices únicos (`UNIQUE`). En la base de datos MySQL, múltiples filas con valor `NULL` están permitidas en índices únicos, pero múltiples filas con una cadena vacía `""` provocan un error fatal `1062 Duplicate entry '' for key ...`.
- **Impacto:** Si dos o más clientes o productos se registraban sin email, sku o código de barras, la aplicación arrojaba un error 500 irrecuperable.

### 2.4. Métrica de Clientes y Ganancias Incompletas
- **Clientes:** La vista listaba una columna de "Compras", pero el modelo `Customer::all()` no realizaba el conteo de ventas asociadas (`sales_count`).
- **Reportes:** En `Sale::summary()`, el campo `'totalProfit' => 0` estaba hardcodeado, dejando la tarjeta de Ganancias en cero aunque en la base de datos se tiene el `cost` y los precios unitarios de cada producto.

### 2.5. Validación CSRF en Peticiones Asíncronas (POS)
- `csrf_token_valid()` requería lectura estricta de `$_POST` o `$_SERVER['HTTP_X_CSRF_TOKEN']`. Para entornos con proxies inversos o servidores web específicos, es imperativo admitir inspección insensible a mayúsculas/minúsculas y soporte para payload JSON decodificado.

---

## 3. Plan de Acción y Fases de Implementación

### Fase 1: Enrutador y Seguridad del Núcleo
1. **Separación de Verbos HTTP en `Router.php`:**
   - Crear métodos específicos: `get()`, `post()`, `put()`, `delete()`.
   - Evitar que `post()` capture métodos `DELETE` o `PUT` destinados a otras rutas.
2. **Corrección de Middleware en `routes.php`:**
   - Asignar `$admin = ['auth', 'admin'];`.
   - Proteger `/sales/{id}/void` con `['auth', 'admin']`.
   - Agregar rutas explícitas para verbos `DELETE` y rutas POST de respaldo (`/resource/{id}/delete`).

### Fase 2: Controladores y Normalización de Datos en MySQL
1. **Normalización a `NULL` de Campos Opcionales:**
   - En `CustomerController.php` y `ProductController.php`, transformar cadenas vacías a `null` antes de persistir (`sku`, `barcode`, `email`, `phone`, `description`, etc.).
2. **Slugs Únicos para Categorías:**
   - Asegurar que la creación/edición de categorías maneje slugs repetidos sin colisionar con la restricción `UNIQUE`.
3. **Optimización de Consultas en Modelos:**
   - Enriquecer `Customer::all()` con la subconsulta de ventas acumuladas por cliente.
   - Implementar cálculo dinámico de ganancia neta en `Sale::summary()`.

### Fase 3: Robustez de CSRF y Manejo de Entorno
1. **Archivo `.env`:**
   - Crear `.env.example` y `.env` con soporte automático de configuración (DB Host, Port, Name, User, Pass).
   - Integrar un cargador simple en `app/bootstrap.php` que no dependa de Composer.
2. **Helper CSRF:**
   - Actualizar `csrf_token_valid()` para admitir lectura desde encabezados HTTP y payloads JSON.

### Fase 4: Verificación y Pruebas
1. Ejecución de scripts CLI automáticos de prueba para:
   - Permisos y middleware de seguridad.
   - Eliminación efectiva de registros sin errores de enrutamiento.
   - Inserción de múltiples registros con valores nulos (verificando índices UNIQUE en MySQL).
   - Simulación de venta POS completa, afectación de stock, registro de movimiento contable y anulación.
2. Validación de respuesta visual y navegación en el navegador.
