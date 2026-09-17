# Sistema de Gestión de Ventas (POS) — Opti Ventas

## 1. Visión General

Sistema web de punto de venta (POS) moderno, responsivo y de fácil uso, orientado a la
gestión de ventas de una tienda. Permite administrar productos (con foto y datos),
controlar inventario, registrar ventas, gestionar clientes y generar reportes.

### Objetivos

- Acceso rápido y visual a los productos (tarjetas con foto y datos).
- Interfaz 100 % responsiva (móvil, tablet y escritorio).
- Operación simple para el vendedor: pocos clics para completar una venta.
- Control de stock e inventario en tiempo real.
- Seguridad en el acceso (login seguro, roles, sesiones).

### Stack Tecnológico

- **Backend:** Laravel 13 (PHP 8.3)
- **Frontend:** Blade + Tailwind CSS v4 + Vite
- **Base de datos:** MySQL (el sistema usa MySQL en desarrollo y producción; se gestiona vía migraciones de Laravel y se configura en `.env` con `DB_CONNECTION=mysql`)
- **Autenticación:** Laravel Breeze (o Fortify) con roles y permisos
- **Interactividad:** Alpine.js (opcional, ligero)

---

## 2. Roles y Permisos

| Rol         | Permisos |
|-------------|----------|
| Administrador | Todo el sistema: usuarios, productos, configuraciones, reportes |
| Vendedor     | POS, ventas, consulta de productos e inventario, clientes (sin permisos de administración) |

---

## 3. Módulos a Desarrollar

### M3.1 — Autenticación y Usuarios
- Login seguro (email + contraseña, token CSRF, sesiones).
- Registro de usuarios solo por administrador.
- Recuperación de contraseña (email).
- Gestión de usuarios: crear, editar, desactivar, asignar rol.
- Cierre de sesión y bloqueo de sesión expirada.

### M3.2 — Dashboard (Panel Principal)
- Métricas del día: ventas, ticket promedio, productos vendidos, clientes atendidos.
- Gráficos de ventas por día / semana / mes.
- Alertas de stock bajo.
- Lista de últimos productos vendidos.

### M3.3 — Gestión de Categorías
- CRUD de categorías (nombre, descripción, color/icono).
- Asignación de productos a categorías.

### M3.4 — Gestión de Productos (CRUD completo)
- **Datos del producto:** nombre, descripción, código de barras, SKU, categoría.
- **Precios:** precio de venta, precio de compra (o costo), precio con IVA/incluido.
- **Inventario:** stock, stock mínimo (alerta), ubicación (opcional).
- **Medios:** foto del producto (upload, vista previa), opción de imagen por color (opcional).
- **Estados:** activo / inactivo / agotado.
- Operaciones: **agregar**, **editar**, **eliminar** (baja lógica recomendada) y **buscar/filtrar**.
- Nota: la eliminación de un producto con ventas asociadas debe evitarse o hacerse por baja lógica.

### M3.5 — Inventario
- Movimientos de inventario: entrada (compra/reposición) y salida (venta/ajuste).
- Ajuste manual de stock (usuario administrador).
- Alertas de stock bajo (visual en listados y dashboard).

### M3.6 — Punto de Venta (POS)
- Catálogo visual de productos: **tarjetas con foto, nombre, precio y stock**.
- Búsqueda rápida por nombre o código de barras (opcional lector de código).
- Carrito de compra: agregar, quitar, modificar cantidades.
- Descuentos por ítem o por venta (porcentaje o monto).
- Selección de cliente (opcional; si no, venta "al mostrador").
- **Pago:** efectivo (calcular cambio), tarjeta/otro, notas.
- **Ticket/recibo:** vista imprimible del detalle de venta.
- Finalizar venta: confirma, descuenta stock automáticamente y guarda el registro.

### M3.7 — Gestión de Clientes
- CRUD de clientes (nombre, teléfono, email, dirección).
- Historial de compras por cliente.
- Opción de compra asociada a cliente (para reportes y crédito futuro).

### M3.8 — Ventas e Historial
- Listado de ventas (fecha, vendedor, cliente, total, método de pago).
- Detalle de cada venta (ítems vendidos).
- **Anulación / reembolso** de una venta (devuelve stock, solo administrador).
- Reimprimir ticket de una venta.

### M3.9 — Reportes
- Ventas por rango de fechas, por vendedor, por cliente.
- Productos más vendidos y menos vendidos.
- Resumen de ingresos vs. ganancias (si se registra costo).
- Exportar a CSV/PDF.
- Visualizar con tablas responsivas y gráficos simples.

### M3.10 — Configuración del Sistema
- Datos del negocio (nombre, dirección, teléfono, logo).
- Impuestos (porcentaje de IVA o impuesto por defecto) y moneda (símbolo).
- Ajustes del recibo/ticket (mensaje de pie, condiciones).
- Preferencias: alerta de stock bajo activable/desactivable.

---

## 4. Estructura de Datos (Modelos Principales)

| Modelo     | Campos clave |
|------------|--------------|
| `User`     | name, email, password, role, is_active |
| `Category` | name, slug, description |
| `Product`  | name, sku, barcode, category_id, description, price, cost, stock, stock_min, photo_path, is_active |
| `Customer` | name, phone, email, address |
| `Sale`     | user_id, customer_id (nullable), total, discount, tax, payment_method, status, notes |
| `SaleItem` | sale_id, product_id, quantity, unit_price, line_total |
| `StockMovement` | product_id, type (in/out/adjust), quantity, reason, user_id |

Relaciones clave:

- `Product` → `Category` (pertenece a una categoría)
- `Sale` → `User` (vendedor), `Sale` → `Customer`
- `Sale` → `SaleItem` → `Product`
- `Product` → `StockMovement` (historial de inventario)

---

## 5. Requisitos Funcionales Clave (Resumen)

1. Ver productos como **tarjetas con foto y datos** en el POS.
2. **Agregar, editar y eliminar** productos desde el panel.
3. Sistema **responsivo**: operar correctamente en celular, tablet y PC.
4. **Login seguro** con roles (admin / vendedor).
5. Descuento de stock automático al realizar una venta.
6. Reembolso de producto/devuelta de stock al anular una venta.
7. Alertas de stock bajo.
8. Reportes de ventas y productos.
9. Recibo/ticket imprimible.
10. Interfaz en español, moderna y fácil de usar (clara, con buen contraste,
   soporte de modo claro/oscuro si es viable).

## 6. Requisitos No Funcionales

- **Base de datos:** MySQL como motor único. Configurarla en `.env`
  (`DB_CONNECTION=mysql`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`,
  `DB_PASSWORD`). El esquema se define con migraciones de Laravel. Contraseñas
  de acceso no deben guardarse en el repositorio.
- **Seguridad:** validación de datos (Form Requests), protección CSRF, contraseñas
  hasheadas, no exponer información sensible, permisos por rol.
- **Rendimiento:** eager loading de relaciones, paginación de listados, optimización
  de imágenes de producto (compresión / redimensionado al subir).
- **Mantenibilidad:** controladores delgados, capa de servicios, código formateado
  con Laravel Pint.
- **Testing:** pruebas de los flujos principales (login, CRUD producto, venta).

---

## 7. Entregables / Milestones

1. **Fase 1 — Base:** autenticación + roles, layout responsivo base, dashboard.
2. **Fase 2 — Catálogo:** categorías + CRUD de productos con fotos e inventario.
3. **Fase 3 — Ventas:** POS visual, carrito, cobro, ticket, descuento de stock.
4. **Fase 4 — Historial y clientes:** ventas pasadas, anulación/reembolso, clientes.
5. **Fase 5 — Reportes y configuración:** reportes, exportación, ajustes del negocio.