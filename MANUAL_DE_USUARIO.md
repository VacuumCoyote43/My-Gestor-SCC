# MY GESTOR (MGSCC) - MANUAL DE USUARIO

## Guía de Uso por Rol

Este manual explica cómo usar el sistema según tu rol de usuario.

---

## 🔐 ACCESO AL SISTEMA

### Inicio de Sesión
1. Accede a: `http://localhost:8000`
2. Haz clic en "Log in"
3. Ingresa tu email y contraseña
4. El sistema te redirigirá automáticamente a tu dashboard según tu rol

### Usuarios de Prueba
- **Admin**: admin@mgscc.local / password
- **Proveedor**: proveedor@example.com / password
- **Gestor Club**: gestor@club.com / password
- **Jugador**: jugador@demo.com / password

---

## 👤 ROL: ADMINISTRADOR

### Dashboard
Al iniciar sesión verás:
- Total de usuarios en el sistema
- Total de proveedores
- Total de clubes
- Incidencias abiertas
- Facturas por estado (draft, pendientes, pagadas, etc.)

### Gestión de Usuarios
**Menú**: Admin → Usuarios

#### Crear Usuario
1. Clic en "Nuevo Usuario"
2. Completa el formulario:
   - Nombre
   - Email (único)
   - Contraseña (mínimo 8 caracteres)
   - Confirmar contraseña
   - Seleccionar rol
   - Estado (activo/inactivo)
3. Clic en "Crear Usuario"

#### Editar Usuario
1. En el listado, clic en "Editar"
2. Modifica los campos necesarios
3. Clic en "Actualizar Usuario"

#### Ver Detalle
1. En el listado, clic en "Ver"
2. Verás toda la información del usuario

### Gestión de Clubes
**Menú**: Admin → Clubes

#### Crear Club
1. Clic en "Nuevo Club"
2. Completa:
   - Nombre (obligatorio)
   - CIF (opcional)
   - Email (opcional)
   - Dirección (opcional)
3. Clic en "Crear Club"

#### Editar/Ver Club
- Similar al proceso de usuarios

### Gestión de Proveedores
**Menú**: Admin → Proveedores

#### Crear Proveedor
1. Clic en "Nuevo Proveedor"
2. Completa:
   - Nombre Legal (obligatorio)
   - NIF/CIF (obligatorio, único)
   - Email (obligatorio)
   - Dirección (opcional)
   - Marcar "Es la Liga" si aplica
3. Clic en "Crear Proveedor"

### Gestión de Incidencias
**Menú**: Incidencias

#### Ver Todas las Incidencias
- Puedes ver incidencias de todos los usuarios
- Filtrar por estado y prioridad

#### Gestionar Incidencia
1. Clic en "Ver" en una incidencia
2. Puedes:
   - Cambiar el estado (Abierta/En Progreso/Cerrada)
   - Responder con mensajes públicos
   - Crear mensajes internos (solo visibles para admins)
   - Adjuntar archivos

---

## 📄 ROL: PROVEEDOR

### Dashboard
Verás tu balance mensual:
- Total emitido en el mes
- Total cobrado (pagos validados)
- Pendiente de cobro
- Balance neto

### Crear Factura
**Menú**: Proveedor → Facturas → Nueva Factura

1. **Datos básicos**:
   - Serie (ej: FAC, SER)
   - Fecha de factura (obligatoria)
   - Fecha de vencimiento (opcional)

2. **Receptor**:
   - Tipo: Club o Proveedor
   - Selecciona el receptor específico

3. **Conceptos** (mínimo 1):
   - Descripción
   - Cantidad
   - Precio unitario
   - % Impuesto
   - Total (se calcula automáticamente)
   - Puedes agregar múltiples conceptos

4. **Archivos** (opcional):
   - Adjunta documentos de soporte
   - Máximo 6MB por archivo

5. Clic en "Crear Factura (Borrador)"

### Emitir Factura
1. Ve a "Proveedor → Facturas"
2. Clic en "Ver" en una factura en estado "draft"
3. Verifica que:
   - Tiene fecha de factura
   - Tiene al menos un concepto
4. Clic en "Emitir Factura"
5. Confirma la acción
6. La factura cambia a estado "pending_payment"
7. Se envía notificación por email al receptor

### Validar/Rechazar Pagos
1. Ve a una factura emitida
2. En la sección "Pagos Registrados" verás los pagos que el receptor ha registrado
3. Para cada pago en estado "registrado":
   - **Validar**: Clic en "Validar", opcionalmente agrega un comentario
   - **Rechazar**: Clic en "Rechazar", DEBES agregar un motivo
4. Al validar:
   - El pago cambia a "validado"
   - Se recalcula el estado de la factura
   - Si total pagado >= total factura → estado "paid"

### Ver Balance
**Menú**: Proveedor → Dashboard

Verás automáticamente tu balance del mes actual.

---

## 🏟️ ROL: GESTOR DE CLUB

### Dashboard
Verás el balance mensual del club:
- Total pagado a proveedores
- Total cargos emitidos a jugadores
- Total cobrado de jugadores
- Pendiente de cobro de jugadores

### Ver Facturas Recibidas
**Menú**: Club → Facturas

- Lista de todas las facturas donde tu club es el receptor
- Filtros por estado y fecha

### Registrar Pago a Proveedor
1. Ve a "Club → Facturas"
2. Clic en "Ver" en una factura
3. Clic en "Registrar Pago"
4. Completa:
   - Importe
   - Fecha de pago
   - Método de pago (transferencia, efectivo, etc.)
   - **Justificante (OBLIGATORIO)**: adjunta comprobante
5. Clic en "Registrar Pago"
6. El pago queda en estado "registrado"
7. El proveedor debe validarlo

### Crear Cargo a Jugador
**Menú**: Club → Cargos → Nuevo Cargo

1. **Datos básicos**:
   - Selecciona el club
   - Selecciona el jugador
   - Fecha de emisión
   - Fecha de vencimiento (opcional)

2. **Conceptos** (mínimo 1):
   - Descripción (ej: Ficha federativa, Seguro, Cuota mensual)
   - Importe
   - Puedes agregar múltiples conceptos

3. Clic en "Crear Cargo (Borrador)"

### Emitir Cargo
1. Ve a "Club → Cargos"
2. Clic en "Ver" en un cargo en estado "draft"
3. Verifica que tiene al menos un concepto
4. Clic en "Emitir Cargo"
5. Confirma la acción
6. El cargo cambia a "pending_payment"
7. Se envía notificación por email al jugador

### Validar/Rechazar Pagos de Jugadores
1. Ve a un cargo emitido
2. En "Pagos Registrados por el Jugador" verás los pagos
3. Para cada pago en estado "registrado":
   - Puedes ver el justificante adjunto
   - **Validar**: Clic en "Validar"
   - **Rechazar**: Clic en "Rechazar", indica el motivo
4. Al validar:
   - El pago cambia a "validado"
   - Se recalcula el estado del cargo
   - Si total pagado >= total cargo → estado "paid"

---

## ⚽ ROL: JUGADOR

### Dashboard
Verás tu situación económica:
- **Deuda Total**: suma de todos tus cargos pendientes
- **Cantidad de Cargos Pendientes**
- **Detalle de Cargos**: tabla con cada cargo pendiente mostrando:
  - Club
  - Fecha de emisión
  - Total del cargo
  - Total pagado
  - Pendiente de pago
  - Fecha de vencimiento

### Ver Cargos
**Menú**: Jugador → Cargos

- Lista de todos tus cargos
- Filtros por estado
- Clic en "Ver / Pagar" para ver detalle

### Registrar Pago
1. Ve a "Jugador → Cargos"
2. Clic en "Ver / Pagar" en un cargo pendiente
3. Verás el detalle del cargo con todos los conceptos
4. Clic en "Registrar Pago" (si está disponible)
5. Completa:
   - Importe (puedes pagar parcial o total)
   - Fecha de pago
   - Método de pago
   - **Justificante (OBLIGATORIO)**: adjunta comprobante de pago
6. Clic en "Registrar Pago"
7. El pago queda en estado "registrado"
8. El gestor del club debe validarlo

### Ver Historial
En cada cargo puedes ver:
- Todos los pagos que has registrado
- Estado de cada pago (registrado/validado/rechazado)
- Justificantes adjuntos
- Si un pago fue rechazado, puedes registrar uno nuevo

---

## 📋 INCIDENCIAS (TODOS LOS ROLES)

### Crear Incidencia
**Menú**: Incidencias → Nueva Incidencia

1. Completa:
   - Asunto (breve descripción)
   - Categoría (técnica, administrativa, financiera, otra)
   - Prioridad (baja, media, alta, urgente)
2. Clic en "Crear Incidencia"
3. Se notifica automáticamente a todos los administradores

### Ver y Responder Incidencia
1. Ve a "Incidencias"
2. Clic en "Ver" en una incidencia
3. Verás:
   - Detalles de la incidencia
   - Conversación completa
   - Estado actual

4. **Enviar Mensaje**:
   - Escribe tu mensaje
   - Si eres admin, elige tipo (público/interno)
   - Opcionalmente adjunta archivos
   - Clic en "Enviar Mensaje"

5. **Mensajes Internos** (solo admin):
   - Son visibles solo para administradores
   - Útiles para notas internas

### Estados de Incidencia
- **Abierta**: recién creada
- **En Progreso**: admin está trabajando en ella
- **Cerrada**: resuelta

---

## 📎 ARCHIVOS ADJUNTOS

### Subir Archivos
- Máximo 6MB por archivo
- Múltiples archivos permitidos
- Formatos: cualquier tipo

### Descargar Archivos
- Clic en el nombre del archivo
- Se descarga automáticamente

### Dónde se Usan
- **Facturas**: documentos de soporte
- **Pagos**: justificantes (OBLIGATORIO)
- **Incidencias**: adjuntos opcionales en mensajes

---

## 🔍 ESTADOS Y FLUJOS

### Estados de Factura
1. **draft**: borrador, editable
2. **pending_payment**: emitida, esperando pago
3. **payment_registered**: pago registrado, esperando validación
4. **paid**: totalmente pagada
5. **cancelled**: cancelada

### Estados de Cargo
1. **draft**: borrador, editable
2. **pending_payment**: emitido, esperando pago
3. **payment_registered**: pago registrado, esperando validación
4. **paid**: totalmente pagado
5. **cancelled**: cancelado

### Estados de Pago
1. **registrado**: recién registrado, esperando validación
2. **validado**: aceptado, cuenta como pagado
3. **rechazado**: rechazado, no cuenta como pagado

---

## ⚠️ PUNTOS IMPORTANTES

### Pagos
- Los justificantes son **OBLIGATORIOS**
- Los pagos deben ser **validados** para contar
- Se permiten **pagos parciales**
- Si un pago es rechazado, puedes registrar uno nuevo

### Facturas
- La fecha de factura es **OBLIGATORIA**
- Debe tener al menos **1 concepto**
- Una vez emitida, **no se puede editar**
- La numeración es **automática**

### Cargos
- Debe tener al menos **1 concepto**
- Una vez emitido, **no se puede editar**
- Se notifica al jugador por **email**

### Incidencias
- Todos los roles pueden crearlas
- Solo admin puede cambiar el estado
- Los mensajes internos son solo para admins

---

## 🆘 SOLUCIÓN DE PROBLEMAS

### No puedo iniciar sesión
- Verifica que tu usuario esté activo
- Contacta al administrador

### No veo mis facturas/cargos
- Verifica que estés en la sección correcta según tu rol
- Usa los filtros de búsqueda

### No puedo emitir una factura/cargo
- Verifica que tenga fecha (facturas)
- Verifica que tenga al menos 1 concepto
- Verifica que esté en estado "draft"

### Mi pago fue rechazado
- Revisa el motivo del rechazo
- Registra un nuevo pago con el justificante correcto

### No recibo notificaciones por email
- Verifica que Mailpit esté ejecutándose
- Accede a http://localhost:8025 para ver los emails

---

## 📧 SOPORTE

Para dudas o problemas:
1. Crea una incidencia en el sistema
2. Los administradores serán notificados automáticamente
3. Recibirás respuesta en la misma incidencia

---

## 🎯 CONSEJOS DE USO

### Para Proveedores
- Revisa tu balance mensual regularmente
- Valida los pagos rápidamente para mantener el flujo
- Adjunta siempre documentos de soporte en las facturas

### Para Gestores de Club
- Emite los cargos a jugadores al inicio del mes
- Valida los pagos de jugadores rápidamente
- Mantén al día los pagos a proveedores

### Para Jugadores
- Revisa tu dashboard regularmente
- Paga antes de la fecha de vencimiento
- Adjunta siempre justificantes claros y legibles

### Para Administradores
- Revisa las incidencias diariamente
- Mantén actualizada la información de usuarios/clubes/proveedores
- Usa mensajes internos para coordinación entre admins

---

## 📊 REPORTES Y CONSULTAS

### Balances Mensuales
- **Proveedores**: Dashboard muestra balance del mes actual
- **Clubes**: Dashboard muestra balance del mes actual
- **Jugadores**: Dashboard muestra deuda total actualizada

### Filtros Disponibles
- **Facturas**: por estado, mes, año
- **Cargos**: por estado
- **Incidencias**: por estado, prioridad
- **Usuarios**: por rol, estado activo

### Paginación
- Todas las listas muestran 15 elementos por página
- Usa los botones de paginación para navegar

---

¡Gracias por usar My Gestor (MGSCC)!
