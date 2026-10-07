# Recetario Casero

Aplicación web en Laravel para que cada usuario guarde, consulte, edite y elimine sus propias recetas de cocina. Todo el contenido visible está en español.

## Funcionalidades

- Registro e inicio de sesión de usuarios.
- Cada usuario solo puede ver y gestionar sus propias recetas.
- Crear, consultar, editar y eliminar recetas.
- Búsqueda por título y filtro por categoría, combinables.
- Validación de formularios con mensajes en español junto a cada campo.
- Confirmación antes de eliminar una receta y mensajes de éxito tras crear, actualizar y eliminar.
- Recetario vacío cuando el usuario todavía no tiene recetas.

## Datos de una receta

| Campo | Regla |
| --- | --- |
| Título | Obligatorio |
| Categoría | Obligatoria: desayuno, almuerzo, cena, postre o bebida |
| Tiempo en minutos | Mayor a 0 |
| Dificultad | Fácil, media o difícil |
| Ingredientes | Texto, uno por línea |
| Pasos de preparación | Texto, uno por línea |
| Nota personal | Opcional |

## Requisitos

- PHP >= 8.3
- Composer
- Node.js y npm
- SQLite (incluido en la instalación base de Laravel)

## Instalación

```bash
# 1. Instalar dependencias de PHP
composer install

# 2. Crear el archivo de entorno y generar la clave de la aplicación
cp .env.example .env
php artisan key:generate

# 3. Instalar dependencias de JavaScript y compilar los estilos (Tailwind)
npm install
npm run build

# 4. Ejecutar el servidor local
php artisan serve
```

Abre en el navegador la dirección que muestre el comando `php artisan serve` (por defecto `http://localhost:8000`).

## Crear la base de datos

El proyecto utiliza SQLite. La base de datos es el archivo `database/database.sqlite`, que ya se crea automáticamente durante la instalación del proyecto.

Ejecuta las migraciones (crea las tablas `users`, `cache`, `jobs` y `recetas`):

```bash
php artisan migrate
```

Si necesitas reconstruir la base de datos desde cero:

```bash
php artisan migrate:fresh
```

## Cómo probar cada fase

La comprobación final del proyecto cubre las fases descritas en la especificación. Puedes probarlas manualmente en el navegador o con las pruebas automatizadas.

### Pruebas automatizadas

```bash
php artisan test
```

Las pruebas están en `tests/Feature`:

- `AuthControllerTest`: registro, inicio de sesión, cierre de sesión y redirecciones.
- `RecetaControllerTest`: CRUD de recetas, validaciones, propiedad por usuario, búsqueda y filtros.

### Probando en el navegador

| # | Prueba | Resultado esperado |
| --- | --- | --- |
| 1 | Registrarse con un usuario nuevo | Entra a un recetario vacío propio |
| 2 | Crear una receta válida | La receta aparece en el listado |
| 3 | Crear una receta con datos inválidos | Los errores aparecen junto a cada campo correspondiente |
| 4 | Abrir el detalle de una receta | Se muestran ingredientes y pasos como listas |
| 5 | Editar una receta y guardar | Los cambios quedan reflejados |
| 6 | Eliminar una receta | Se solicita confirmación, la receta desaparece y se muestra un mensaje de éxito |
| 7 | Buscar por título | Solo aparecen las recetas que coinciden |
| 8 | Filtrar por categoría | Solo aparecen las recetas de la categoría seleccionada |
| 9 | Combinar búsqueda y categoría | Solo aparecen las recetas que cumplen ambos criterios |
| 10 | Buscar o filtrar sin resultados | Se muestra un mensaje indicando que no hay resultados |
| 11 | Entrar con otro usuario distinto | No puede ver las recetas del primer usuario |

## Estructura

- `app/Http/Controllers/AuthController.php` — registro, inicio de sesión y cierre de sesión.
- `app/Http/Controllers/RecetaController.php` — controlador de recurso para las recetas.
- `app/Models/Receta.php` — modelo con las categorías y dificultades permitidas.
- `database/migrations/*_create_recetas_table.php` — tabla de recetas.
- `database/factories/RecetaFactory.php` — datos de prueba para recetas.
- `routes/web.php` — rutas públicas y protegidas.
- `resources/views/` — vistas (layout, autenticación y recetas).
- `tests/Feature/` — pruebas automatizadas.

## Alcance

Sin roles, permisos, usuarios de distintos tipos, tablas adicionales para ingredientes o pasos, ni funcionalidades fuera de las indicadas.