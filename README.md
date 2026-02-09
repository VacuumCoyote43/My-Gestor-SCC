## My Gestor SCC

Aplicacion web basada en Laravel 12 con Inertia + Vue 3 y Vite.

## Requisitos

- PHP 8.2+
- Composer
- Node.js 18+ (recomendado 20+) y npm
- Base de datos (MySQL o MariaDB)

## Instalacion rapida

1) Instala dependencias PHP:

```bash
composer install
```

2) Crea el archivo de entorno:

```bash
cp .env.example .env
```

3) Configura la base de datos en `.env`:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mi_base
DB_USERNAME=usuario
DB_PASSWORD=clave
```

4) Genera la key y ejecuta migraciones:

```bash
php artisan key:generate
php artisan migrate
```

5) Instala dependencias frontend y compila:

```bash
npm install
npm run build
```

6) Levanta la app:

```bash
php artisan serve
```

## Desarrollo

Opcion recomendada (todo en una sola linea):

```bash
composer run dev
```

Incluye servidor, cola, logs (Pail) y Vite.

Si prefieres separarlo:

```bash
php artisan serve
php artisan queue:listen --tries=1
php artisan pail --timeout=0
npm run dev
```

## Produccion

```bash
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm install
npm run build
```

Configura el servidor web para apuntar al directorio `public/`.

## Tests

```bash
composer test
```

## Notas utiles

- Si necesitas borrar caches:

```bash
php artisan optimize:clear
```

- Si el storage es requerido por la app:

```bash
php artisan storage:link
```

## Licencia

Proyecto interno. Revisar con el propietario del repositorio.
