# Sistema de Torneos con Roles

Aplicación web en Laravel para gestionar torneos. Permite que un **administrador** cree, edite y elimine torneos y gestione sus inscripciones; que un **jugador** consulte torneos, se inscriba, vea sus torneos y cancele su inscripción; y que un **visitante** sin cuenta consulte los torneos disponibles.

Solo existen dos roles: `administrador` y `jugador`.

## Requisitos

- PHP **8.3 o superior** (probado con PHP 8.5)
- Composer
- Node.js y npm (para compilar los estilos con Tailwind)
- SQLite (incluido en PHP) — la base de datos usada por defecto

## Instalación

```bash
# 1. Instalar dependencias de PHP
composer install

# 2. Crear el archivo de entorno y generar la clave de la aplicación
cp .env.example .env
php artisan key:generate

# 3. Instalar dependencias de frontend y compilar (Tailwind CSS)
npm install
npm run build

# 4. Crear y migrar la base de datos, y sembrar la cuenta administrador
php artisan migrate:fresh --seed

# 5. Levantar el servidor de desarrollo
php artisan serve
```

Abre `http://localhost:8000`.

> Para desarrollo con hot reload de estilos: `npm run dev` en lugar de `npm run build`.

## Base de datos

- Se utiliza **SQLite** (archivo `database/database.sqlite`).
- No requiere configuración adicional; las migraciones crean las tablas:

  - `users` — con la columna `rol` (`administrador` | `jugador`).
  - `torneos` — nombre, juego, fecha, cupo, descripción y estado.
  - `inscripciones` — relaciona un torneo con un usuario (`unique` entre ambos para impedir duplicados).
  - La eliminación de un torneo borra sus inscripciones **en cascada**.

Ejecutar migraciones y datos de ejemplo:

```bash
php artisan migrate:fresh --seed
```

## Cuenta de administrador

La cuenta de administrador se crea mediante un **seeder** (`database/seeders/AdminSeeder.php`), documentado en `database/seeders/DatabaseSeeder.php`.

```bash
php artisan db:seed
# o, de forma completa:
php artisan migrate:fresh --seed
```

Credenciales del administrador:

| Campo | Valor |
|---|---|
| Correo | `admin@torneos.test` |
| Contraseña | `password` |

No existe un registro público que permita escoger el rol: el registro siempre crea cuentas con rol `jugador`. Los administradores solo se crean con el seeder.

## Cuentas demo

| Rol | Correo | Contraseña |
|---|---|---|
| Administrador | `admin@torneos.test` | `password` |
| Jugador | `jugador@torneos.test` | `password` |

## Regla de cerrado y reglas de negocio

- Un torneo se considera **cerrado** (y no aparece en el listado de disponibles) si:
  1. el administrador lo marcó como `cerrado`;
  2. la fecha del torneo ya pasó;
  3. el número de inscritos alcanzó el cupo.
- El detalle de un torneo cerrado siempre puede consultarse mediante acceso directo.
- El listado público solo muestra torneos abiertos, con fecha futura y con plazas disponibles, ordenados por la fecha más próxima.
- El administrador **no puede reducir el cupo por debajo** de los jugadores ya inscritos.
- Un jugador no puede inscribirse dos veces al mismo torneo.
- La **cancelación** de una inscripción está permitida únicamente **hasta la fecha del evento** (es decir, mientras la fecha del torneo no haya llegado o pasado). Al cancelar, la plaza queda libre de nuevo.

## Pruebas

### Pruebas automatizadas

La aplicación incluye pruebas funcionales (PHPUnit) que cubren los casos de las secciones 15 a 18 del enunciado:

```bash
php artisan test
```

Cobertura:

- `AutenticacionTest`: registro crea jugador, login (jugador y administrador), logout, validaciones.
- `ControlAccesoTest`: bloqueo de la gestión de administrador para jugador y visitante, acceso del administrador, cancelación ajena.
- `AdminTorneoTest`: creación válida e inválida, fecha futura, límites del cupo (2–100), edición, cupo no reducible, eliminación en cascada.
- `AdminInscripcionTest`: ver inscritos y dar de baja una inscripción.
- `ListadoTorneoTest`: filtros del listado, orden por fecha, mensaje de listado vacío, buscador, detalle y acceso directo a torneos cerrados.
- `InscripcionTest`: inscripción válida, duplicada, torneo lleno, torneo cerrado, fecha pasada, "Mis torneos" solo con inscripciones propias, cancelación que libera plaza y cancelación vencida.

### Pruebas manuales

1. **Autenticación**
   - Registrarse: la cuenta se crea con rol `jugador`.
   - Iniciar sesión con un jugador → accede a sus funcionalidades.
   - Iniciar sesión con el administrador → accede a "Gestión" (torneos e inscripciones).
   - Cerrar sesión → termina la sesión.
2. **Roles / acceso**
   - Intentar abrir `/admin/torneos` como jugador → se redirige con el aviso "Acceso no permitido…".
   - Intentarlo como visitante → se redirige a iniciar sesión.
3. **Torneos (administrador)**
   - Crear un torneo válido → aparece en la gestión.
   - Crear sin nombre/juego, con fecha pasada o con cupo fuera de 2–100 → errores de validación en cada campo.
   - Editar y guardar cambios.
   - Reducir el cupo por debajo de los inscritos → error; igual o mayor → permitido.
   - Eliminar un torneo → desaparece junto con sus inscripciones.
4. **Listado y detalle**
   - Solo aparecen torneos abiertos, futuros y con plazas; los cerrados/llenos solo por acceso directo.
   - El detalle muestra datos y participantes.
   - Sin torneos disponibles → mensaje "No hay torneos disponibles."
5. **Inscripciones (jugador)**
   - Inscribirse en un torneo disponible.
   - Inscribirse de nuevo al mismo torneo → aviso de duplicado.
   - Torneo lleno/cerrado/pasado → aviso del motivo.
   - "Mis torneos" muestra únicamente sus inscripciones.
   - Cancelar antes de la fecha → plaza liberada; después de la fecha → aviso.
6. **Gestión de inscripciones (administrador)**
   - Ver los jugadores inscritos de cada torneo y dar de baja cualquiera (la plaza se libera).

## Estructura del proyecto

```
app/Http/Controllers/
  Auth/                  Registro e inicio/cierre de sesión
  TorneoController       Listado y detalle públicos
  MisTorneosController   Torneos del jugador autenticado
  InscripcionController  Inscribirse y cancelar
  Admin/                 Gestión de torneos e inscripciones
app/Http/Middleware/EnsureUserIsAdmin.php   Protege rutas de administrador
app/Http/Requests/Admin/                    Validaciones del CRUD (mensajes en español)
app/Models/                                 User, Torneo e Inscripcion
resources/views/                            Vistas Blade + Tailwind CSS (interfaz en español)
database/seeders/AdminSeeder.php            Cuenta administrador y cuenta demo
```