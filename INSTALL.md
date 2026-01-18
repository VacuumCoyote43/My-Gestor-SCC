# My Gestor (MGSCC) - Guía de Instalación

## Requisitos Previos

- PHP 8.2 o superior
- Composer
- Node.js 18+ y NPM
- MySQL 8.0+
- Laragon (Windows) o entorno similar
- Mailpit para correo local (opcional pero recomendado)

## Instalación Paso a Paso

### 1. Clonar o ubicar el proyecto

El proyecto ya está en: `C:\laragon\www\my-gestor-scc`

### 2. Configurar el archivo .env

```bash
# Copiar el archivo de ejemplo
cp .env.example .env

# Editar .env y configurar la base de datos
DB_DATABASE=my_gestor
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Crear la base de datos

En MySQL:
```sql
CREATE DATABASE my_gestor CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 4. Instalar dependencias

```bash
# Dependencias PHP
composer install

# Dependencias JavaScript
npm install
```

### 5. Generar key de aplicación

```bash
php artisan key:generate
```

### 6. Ejecutar migraciones y seeders

```bash
php artisan migrate:fresh --seed
```

Esto creará todas las tablas y datos de ejemplo:
- Roles: admin, proveedor, gestor_club, jugador
- Usuarios de ejemplo (ver más abajo)

### 7. Crear enlace simbólico para storage

```bash
php artisan storage:link
```

### 8. Compilar assets

```bash
# Desarrollo
npm run dev

# Producción
npm run build
```

### 9. Iniciar el servidor

```bash
php artisan serve
```

La aplicación estará disponible en: `http://localhost:8000`

## Usuarios de Ejemplo

Después de ejecutar los seeders, tendrás estos usuarios:

| Email | Password | Rol |
|-------|----------|-----|
| admin@mgscc.local | password | admin |
| proveedor@example.com | password | proveedor |
| gestor@club.com | password | gestor_club |
| jugador@demo.com | password | jugador |

## Configuración de Mailpit (Correo Local)

### Instalación de Mailpit

1. Descargar desde: https://github.com/axllent/mailpit/releases
2. Ejecutar el binario: `mailpit.exe`
3. Acceder a la interfaz web: `http://localhost:8025`

### Configuración en .env

Ya está configurado en `.env.example`:

```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@mgscc.local"
MAIL_FROM_NAME="My Gestor MGSCC"
```

## Cola de Trabajos (Queue)

Las notificaciones se envían mediante colas. Para procesarlas:

```bash
php artisan queue:work
```

O en desarrollo, puedes usar `sync` en `.env`:
```env
QUEUE_CONNECTION=sync
```

## Estructura del Proyecto

### Modelos Principales
- `Role`: Roles del sistema
- `User`: Usuarios
- `Club`: Clubes deportivos
- `Proveedor`: Proveedores
- `Factura`: Facturas entre proveedores/clubes
- `FacturaConcepto`: Líneas de factura
- `Pago`: Pagos (facturas o cargos)
- `CargoJugador`: Cargos a jugadores
- `CargoConcepto`: Líneas de cargo
- `Incidencia`: Incidencias/tickets
- `MensajeIncidencia`: Chat de incidencias
- `ArchivoAdjunto`: Archivos adjuntos (morph)
- `Modificacion`: Auditoría (morph)

### Servicios
- `AuditLoggerService`: Registro de auditoría
- `InvoiceNumberService`: Numeración de facturas
- `BalanceService`: Cálculos de balance por rol

### Rutas Principales

#### Admin
- `/admin/dashboard` - Dashboard admin
- `/admin/users` - Gestión de usuarios

#### Proveedor
- `/proveedor/dashboard` - Dashboard con balance mensual
- `/proveedor/facturas` - Gestión de facturas
- `/proveedor/pagos/{pago}/validate` - Validar pago
- `/proveedor/pagos/{pago}/reject` - Rechazar pago

#### Gestor Club
- `/club/dashboard` - Dashboard con balance mensual
- `/club/cargos` - Gestión de cargos a jugadores
- `/club/facturas/{factura}/pagos/create` - Registrar pago a proveedor
- `/club/pagos/{pago}/validate` - Validar pago de jugador

#### Jugador
- `/jugador/dashboard` - Dashboard con deuda total
- `/jugador/cargos` - Ver cargos
- `/jugador/cargos/{cargo}/pagos/create` - Registrar pago

#### Común
- `/incidencias` - Gestión de incidencias (todos los roles)

## Flujos Principales

### Flujo de Facturación (Proveedor → Club)

1. **Proveedor crea factura** (estado: `draft`)
   - Agrega conceptos (mínimo 1)
   - Adjunta documentos (opcional)
   - Fecha de factura OBLIGATORIA

2. **Proveedor emite factura** (estado: `pending_payment`)
   - Se valida que tenga fecha y conceptos
   - Se notifica al receptor

3. **Gestor Club registra pago** (estado: `payment_registered`)
   - Adjunta justificante (obligatorio)
   - Estado del pago: `registrado`

4. **Proveedor valida pago** (estado: `paid` si total pagado >= total)
   - Estado del pago: `validado`
   - Se recalcula estado de la factura
   - Si rechaza, vuelve a `pending_payment`

### Flujo de Cargos a Jugadores

1. **Gestor Club crea cargo** (estado: `draft`)
   - Selecciona jugador y club
   - Agrega conceptos (mínimo 1)

2. **Gestor Club emite cargo** (estado: `pending_payment`)
   - Se notifica al jugador por email

3. **Jugador registra pago** (estado: `payment_registered`)
   - Adjunta justificante (obligatorio)
   - Estado del pago: `registrado`

4. **Gestor Club valida pago** (estado: `paid` si total pagado >= total)
   - Estado del pago: `validado`
   - Se recalcula estado del cargo

### Flujo de Incidencias

1. **Cualquier usuario crea incidencia**
   - Estado: `abierta`
   - Se notifica a todos los admins

2. **Chat de mensajes**
   - Mensajes públicos: visibles para creador y admin
   - Mensajes internos: solo admin
   - Adjuntos permitidos

3. **Admin gestiona estado**
   - `en_progreso` / `cerrada`

## Permisos y Roles

### Admin
- Dashboard: contadores sin importes
- Gestión de usuarios, clubes, proveedores
- Gestión de incidencias (todas)
- NO ve importes económicos

### Proveedor
- Dashboard: balance mensual (emitido vs cobrado)
- Crear/editar/emitir facturas
- Validar/rechazar pagos de sus facturas
- Ver solo sus facturas

### Gestor Club
- Dashboard: balance mensual (pagado a proveedores vs cobrado de jugadores)
- Ver facturas recibidas
- Registrar pagos a proveedores
- Crear/emitir cargos a jugadores
- Validar/rechazar pagos de jugadores

### Jugador
- Dashboard: deuda total y detalle
- Ver sus cargos
- Registrar pagos (con justificante)

## Auditoría

Todas las operaciones importantes se registran en la tabla `modificaciones`:
- Creación/edición/eliminación de registros
- Emisión de facturas/cargos
- Registro/validación/rechazo de pagos
- Cambios de estado

## Archivos Adjuntos

- Tamaño máximo: 6MB por archivo
- Storage: `storage/app/private/`
- Tipos soportados: todos
- Validación en FormRequests

## Comandos Útiles

```bash
# Limpiar caché
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Recrear base de datos
php artisan migrate:fresh --seed

# Ver rutas
php artisan route:list

# Ver trabajos en cola
php artisan queue:work --tries=3

# Ejecutar tests
php artisan test
```

## Troubleshooting

### Error: "Class not found"
```bash
composer dump-autoload
```

### Error: "Storage not found"
```bash
php artisan storage:link
```

### Error de permisos en storage
```bash
# En Windows/Laragon normalmente no es necesario
# En Linux:
chmod -R 775 storage bootstrap/cache
```

### Migraciones fallan
```bash
# Verificar conexión a base de datos
php artisan tinker
>>> DB::connection()->getPdo();

# Recrear desde cero
php artisan migrate:fresh --seed
```

## Notas de Desarrollo

### TODOs en el código

Hay varios TODOs marcados en el código para mejoras futuras:
- Asociación real de usuarios con proveedores/clubes (actualmente usa el primero)
- Paginación completa en vistas Vue
- Filtros avanzados en listados
- Más validaciones de permisos en policies
- Endpoints para descargar/ver archivos con permisos
- Más páginas Vue (create/edit de facturas, cargos, etc.)

### Mejoras Sugeridas

1. **Relación User-Proveedor**: Agregar tabla pivot o campo `proveedor_id` en users
2. **Relación User-Club**: Agregar tabla pivot para gestores con múltiples clubes
3. **Impuestos**: Implementar cálculo automático de IVA/IGIC
4. **Reportes**: Exportar a PDF/Excel
5. **Notificaciones**: Agregar más tipos (pago validado, factura vencida, etc.)
6. **Dashboard**: Gráficos con Chart.js o similar
7. **Búsqueda**: Implementar búsqueda global
8. **API**: Agregar endpoints REST si se necesita integración externa

## Soporte

Para dudas o problemas, revisar:
- Logs: `storage/logs/laravel.log`
- Documentación Laravel 12: https://laravel.com/docs/12.x
- Documentación Inertia.js: https://inertiajs.com
- Documentación Vue 3: https://vuejs.org

## Licencia

Proyecto privado - Todos los derechos reservados
