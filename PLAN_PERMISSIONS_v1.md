# Plan: Sistema de Roles y Permisos con Spatie Laravel Permission

**Versión:** 1.0
**Fecha:** Mayo 2025
**Proyecto:** Demo2-IWI
**Autor:** Plan de implementación

---

## Resumen Ejecutivo

Sistema de permisos basado en Spatie Laravel Permission con roles jerárquicos (Admin, Supervisor, Editor), permisos por menú, capacidades a nivel de proyecto (4 niveles), y gestión de usuarios "propios" vs "todos".

---

## 1. Análisis del Sistema Actual

| Aspecto | Estado Actual | Problema |
|---------|---------------|----------|
| Roles | `admin`, `super`, `editor` en campo `users.role` | Sin tabla de roles, inconsistente |
| Permisos | Opciones en BD (`options` + `user_options`) | Por usuario, no por rol |
| Menú | Hardcoded en `nav.blade.php` | No hay submenús, sin estructura jerárquica |
| Protección URL | Solo en UI (`OptionHelper`) | No hay middleware |
| Proyectos | Tabla pivote `projects_users` existe | Solo `as_owner`, sin capacidades |

### Archivos clave actuales

| Archivo | Propósito |
|---------|-----------|
| `app/Http/Controllers/OptionController.php` | CRUD de opciones + `canAccess()` |
| `app/Helpers/OptionHelper.php` | Wrapper para permission check |
| `app/Models/Options.php` | Modelo de opción |
| `app/Models/UserOption.php` | Mapa usuario-opción |
| `resources/views/components/layouts/nav.blade.php` | Menú hardcoded |
| `resources/views/components/layouts/option.blade.php` | Item de menú con visibilidad |

---

## 2. Fase 1: Instalar y Configurar Spatie

### 2.1 Instalación

```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

### 2.2 Tablas que creará Spatie

| Tabla | Propósito |
|-------|-----------|
| `roles` | cat: id, name, guard_name, timestamps |
| `permissions` | cat: id, name, guard_name, timestamps |
| `role_has_permissions` | cat: role_id, permission_id |
| `model_has_roles` | cat: model_type, model_id, role_id |
| `model_has_permissions` | cat: model_type, model_id, permission_id |

### 2.3 Configuración

En `app/Models/User.php`:

```php
use Spatie\Permission\Traits\HasRoles;

class User extends Model
{
    use HasRoles;

    // ...existing code
}
```

---

## 3. Fase 2: Migración de Datos

### 3.1 Tabla inicial de roles

Crear migración `create_initial_roles_table`:

| id | name | description |
|----|------|-------------|
| 1 | Admin | Acceso total al sistema |
| 2 | Supervisor | Acceso a proyectos asignados con gestión |
| 3 | Editor | Acceso a proyectos asignados con creación |

### 3.2 Migrar permisos existentes

```php
// En una migración o seeder
$options = DB::table('options')->get();
foreach ($options as $opt) {
    Permission::create(['name' => $opt->name.'-'.$opt->type]);
}
```

### 3.3 Migrar usuarios existentes

```php
// Mapear rol actual → nuevo rol Spatie
// Admin → rol Admin
// Super → rol Supervisor
// Editor → rol Editor
// Client/User → crear rol base "Viewer"
```

---

## 4. Fase 3: Estructura de Permisos basada en Menú

### 4.1 Modificar nav.blade.php para soportar submenús

```php
// resources/views/components/layouts/nav.blade.php
// Estructura propuesta:

[
    'Users' => [
        'icon' => 'users',
        'route' => 'users.index',
        'permissions' => ['users-menu', 'users-screen'],
        'submenu' => [
            'List Users' => ['route' => 'users.index', 'perm' => 'users-list'],
            'Create User' => ['route' => 'users.create', 'perm' => 'users-create'],
        ]
    ],
    'Projects' => [
        'icon' => 'file-video',
        'route' => 'projects.index',
        'permissions' => ['projects-menu', 'projects-screen'],
        'submenu' => [
            'All Projects' => ['route' => 'projects.index', 'perm' => 'projects-list'],
            'Create Project' => ['route' => 'projects.create', 'perm' => 'projects-create'],
        ]
    ],
    'Hotpoints' => [...],
    'Tags' => [...],
    'Territories' => [...],
    'Brands' => [...],
    'Products' => [...],
    'Security Items' => [...],
    'AI Machine CFG' => [...],
]
```

### 4.2 Convención de permisos por tipo

| Sufijo | Propósito |
|--------|----------|
| `-menu` | Visible en sidebar |
| `-screen` | Acceso a la página |
| `-list` | Listar recursos |
| `-create` | Crear nuevos |
| `-edit` | Editar existentes |
| `-delete` | Eliminar |
| `-view` | Ver detalle |

### 4.3 Seeder de permisos desde menú

```bash
php artisan db:seed --class=MenuPermissionsSeeder
```

Este seeder escaneará la estructura del menú y creará automáticamente todos los permisos necesarios.

---

## 5. Fase 4: Permission Middleware

### 5.1 Middleware CheckRole existente

Spatie ya proporciona `role` middleware. Crear versión extendida si es necesario:

```php
// app/Http/Middleware/CheckRole.php
public function handle($request, Closure $next, ...$roles) {
    if (!$request->user()) return redirect('/login');

    // Admin tiene acceso total bypassing normal checks
    if ($request->user()->hasRole('Admin')) {
        return $next($request);
    }

    // Check si tiene alguno de los roles requeridos
    foreach ($roles as $role) {
        if ($request->user()->hasRole($role)) {
            return $next($request);
        }
    }

    abort(403);
}
```

Registrar en `bootstrap/app.php`:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \App\Http\Middleware\CheckRole::class,
    ]);
})
```

### 5.2 Proteger rutas

```php
// routes/web.php

// Rutas protegidas por rol
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->group(function () {
    Route::resource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
});

// Rutas de usuarios por rol
Route::middleware(['auth', 'role:Admin|Supervisor'])->group(function () {
    Route::resource('users', UserController::class);
});

// Rutas de proyectos con permisos granulares
Route::middleware(['auth', 'permission:projects-menu'])->group(function () {
    Route::resource('projects', ProjectController::class)->only(['index', 'show']);
});
Route::middleware(['auth', 'permission:projects-create'])->group(function () {
    Route::resource('projects', ProjectController::class)->only(['create', 'store']);
});
Route::middleware(['auth', 'permission:projects-edit'])->group(function () {
    Route::resource('projects', ProjectController::class)->only(['edit', 'update']);
});
Route::middleware(['auth', 'permission:projects-delete'])->group(function () {
    Route::resource('projects', ProjectController::class)->only(['destroy']);
});
```

### 5.3 Ocultar menú dinámicamente

```php
// resources/views/components/layouts/nav.blade.php

@can('users-menu')
    <x-layouts.option route="users.index" name="Users" icon="users" />
@endcan

@canany(['projects-menu', 'projects-create', 'projects-edit'])
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
            Projects
        </a>
        <ul class="dropdown-menu">
            @can('projects-list')
                <li><a href="{{ route('projects.index') }}">All Projects</a></li>
            @endcan
            @can('projects-create')
                <li><a href="{{ route('projects.create') }}">Create</a></li>
            @endcan
        </ul>
    </li>
@endcanany
```

---

## 6. Fase 5: Panel de Admin para Permisos

### 6.1 Rutas solo para Admin

```php
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->group(function () {
    // Roles
    Route::resource('roles', RoleController::class);
    Route::get('roles/{role}/permissions', [RoleController::class, 'permissions'])
        ->name('roles.permissions');
    Route::put('roles/{role}/permissions', [RoleController::class, 'updatePermissions'])
        ->name('roles.permissions.update');

    // Permissions (solo lectura, auto-generados)
    Route::get('permissions', [PermissionController::class, 'index'])
        ->name('permissions.index');
    Route::get('permissions/{permission}', [PermissionController::class, 'show'])
        ->name('permissions.show');
});
```

### 6.2 RoleController métodos

```php
// app/Http/Controllers/Admin/RoleController.php

public function permissions(Role $role) {
    $permissions = Permission::all()->groupBy(function($p) {
        return explode('-', $p->name)[0]; // Agrupa por recurso
    });
    $rolePermissions = $role->permissions->pluck('id')->toArray();

    return view('admin.roles.permissions', compact('role', 'permissions', 'rolePermissions'));
}

public function updatePermissions(Request $request, Role $role) {
    $role->syncPermissions($request->permissions);

    // Guardar permisos especiales del rol
    $role->can_manage_all_users = $request->has('can_manage_all_users');
    $role->can_manage_own_users = $request->has('can_manage_own_users');
    $role->save();

    return redirect()->route('roles.index')
        ->with('success', 'Permissions updated');
}
```

### 6.3 Vistas

- `resources/views/admin/roles/index.blade.php` - Lista de roles
- `resources/views/admin/roles/edit.blade.php` - Editar rol
- `resources/views/admin/roles/permissions.blade.php` - Asignar permisos (checkboxes por categoría)
- `resources/views/admin/permissions/index.blade.php` - Lista de permisos (solo lectura)

### 6.4 Permisos especiales en rol

```php
// Agregar a tabla roles:
$table->boolean('can_manage_all_users')->default(false);
$table->boolean('can_manage_own_users')->default(true);
```

| Campo | Descripción |
|-------|-------------|
| `can_manage_all_users` | Puede ver/editar TODOS los usuarios del sistema |
| `can_manage_own_users` | Puede ver/editar solo usuarios "suyos" (client_id) |

---

## 7. Fase 6: Permisos de Proyecto (4 niveles)

### 7.1 Nueva tabla project_user_permissions

```php
// database/migrations/xxxx_create_project_user_permissions_table.php

Schema::create('project_user_permissions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->foreignId('project_id')->constrained()->cascadeOnDelete();
    $table->enum('access_level', ['none', 'read', 'write', 'create'])->default('none');
    $table->timestamps();

    $table->unique(['user_id', 'project_id']);
});
```

### 7.2 Niveles de acceso

| Level | Capacidad | Incluye |
|-------|-----------|---------|
| `none` | Sin acceso | - |
| `read` | Solo lectura | Ver proyecto, hotpoints, estadísticas |
| `write` | Lectura + Escritura | + Editar hotpoints, editar productos |
| `create` | Administrador | + Crear/borrar proyectos, gestionar accesos |

### 7.3 Helper de permisos de proyecto

```php
// app/Helpers/ProjectPermissionHelper.php

namespace App\Helpers;

class ProjectPermissionHelper
{
    public static function canAccess($user, $project, string $level): bool
    {
        if ($user->hasRole('Admin')) return true;

        $permission = DB::table('project_user_permissions')
            ->where('user_id', $user->id)
            ->where('project_id', $project->id)
            ->first();

        if (!$permission) return false;

        $levels = ['none' => 0, 'read' => 1, 'write' => 2, 'create' => 3];

        return $levels[$permission->access_level] >= $levels[$level];
    }

    public static function canView($user, $project): bool
    {
        return self::canAccess($user, $project, 'read');
    }

    public static function canEdit($user, $project): bool
    {
        return self::canAccess($user, $project, 'write');
    }

    public static function canCreate($user, $project): bool
    {
        return self::canAccess($user, $project, 'create');
    }

    public static function getAccessLevel($user, $project): ?string
    {
        return DB::table('project_user_permissions')
            ->where('user_id', $user->id)
            ->where('project_id', $project->id)
            ->value('access_level');
    }

    public static function setAccessLevel($user, $project, string $level): void
    {
        DB::table('project_user_permissions')->updateOrInsert(
            ['user_id' => $user->id, 'project_id' => $project->id],
            ['access_level' => $level, 'updated_at' => now()]
        );
    }
}
```

### 7.4 Asignación de proyectos a usuarios

**Opción A: En edición de usuario (Admin)**

```php
// En UserController::edit
$projects = Project::all();
$userProjects = DB::table('project_user_permissions')
    ->where('user_id', $user->id)
    ->pluck('access_level', 'project_id');
```

Vista: Checkboxes por proyecto con selector de nivel.

**Opción B: En edición de proyecto (Admin o Supervisor)**

En `ProjectController::edit`, pestaña "Accesos":
- Lista de usuarios con selector de nivel
- Posibilidad de dar acceso "a todos los proyectos" vía rol global

### 7.5 Proteger ProjectController

```php
// app/Http/Controllers/ProjectController.php

public function show(Project $project) {
    if (!ProjectPermissionHelper::canView(auth()->user(), $project)) {
        return redirect()->route('projects.index')
            ->with('error', 'No tienes acceso a este proyecto');
    }
    // ...
}

public function edit(Project $project) {
    if (!ProjectPermissionHelper::canEdit(auth()->user(), $project)) {
        return redirect()->route('projects.index')
            ->with('error', 'No tienes permisos para editar este proyecto');
    }
    // ...
}

public function destroy(Project $project) {
    if (!ProjectPermissionHelper::canCreate(auth()->user(), $project)) {
        abort(403);
    }
    // ...
}
```

---

## 8. Fase 7: Usuarios "Suyos" (Can manage own users)

### 8.1 Scope en User model

```php
// app/Models/User.php

public function scopeOwnUsers($query, $user)
{
    if ($user->can('manage_all_users')) {
        return $query; // Ve todos
    }

    if ($user->can('manage_own_users')) {
        return $query->where('client_id', $user->id); // Solo los suyos
    }

    return $query->where('id', $user->id); // Solo a sí mismo
}
```

### 8.2 Aplicar en UserController

```php
// app/Http/Controllers/UserController.php

public function index() {
    $users = User::ownUsers(auth()->user())->paginate();
    return view('users.index', compact('users'));
}

public function update(Request $request, User $user) {
    // Verificar que puede editar a este usuario
    if (!auth()->user()->can('manage_all_users')
        && $user->client_id !== auth()->id()
        && $user->id !== auth()->id()) {
        abort(403);
    }

    // ... resto de la lógica
}
```

---

## 9. Fase 8: Cleanup - Eliminar Sistema Antiguo

**ADVERTENCIA: Ejecutar SOLO después de verificar que todo funciona**

### 9.1 Archivos a eliminar

```bash
# Helpers
rm app/Helpers/OptionHelper.php

# Controllers
rm app/Http/Controllers/OptionController.php

# Models
rm app/Models/Options.php
rm app/Models/UserOption.php
rm app/Models/VersionsRole.php  # No usada

# Views (si no hay otras referencias)
rm -rf resources/views/options/
```

### 9.2 Migraciones a eliminar

- Eliminar tablas `options`
- Eliminar tabla `user_options`
- Eliminar tabla `versions_roles`

```php
// Nueva migración
Schema::dropIfExists('options');
Schema::dropIfExists('user_options');
Schema::dropIfExists('versions_roles');
Schema::dropIfExists('projects_users'); // Reemplazada por project_user_permissions
```

### 9.3 Limpiar routes/web.php

Eliminar:
```php
Route::resource('options', OptionController::class)->except(['show']);
Route::resource('datision-parameters', DatisionParameterController::class);
```

### 9.4 Limpiar vistas

Eliminar `OptionHelper` usage de:
- `resources/views/components/layouts/app.blade.php`

Reemplazar con:
```php
@can('screen-name-screen')
    {{ $slot }}
@else
    <h1>You don't have access to this section...</h1>
@endcan
```

### 9.5 Eliminar columna role de users

```php
Schema::table('users', function (Blueprint $table) {
    $table->dropColumn('role');
});
```

---

## 10. Orden de Implementación

| Fase | Descripción | Dependencias |
|------|-------------|--------------|
| 1 | Instalar Spatie + migración | - |
| 2 | Crear RoleController + Vistas admin | 1 |
| 3 | MenuPermissionsSeeder (escanea menú → permisos) | 2 |
| 4 | Implementar middleware y proteger rutas | 1, 3 |
| 5 | Modificar nav.blade.php con @can/@endcan | 3, 4 |
| 6 | Crear project_user_permissions + helper | 1 |
| 7 | Implementar asignación de proyectos | 6 |
| 8 | Implementar "can_manage_own_users" en roles | 2 |
| 9 | Cleanup: eliminar OptionController, OptionHelper, tablas antiguas | 1-8 |

---

## 11. Estructura de Archivos Final

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── RoleController.php
│   │   │   └── PermissionController.php
│   │   └── ... (controladores existentes)
│   └── Middleware/
│       └── CheckRole.php
├── Helpers/
│   ├── ProjectPermissionHelper.php  (nuevo)
│   └── VideoHelper.php (existente)
├── Models/
│   ├── User.php (modificado con HasRoles)
│   └── ... (modelos existentes)
routes/
├── web.php (modificado con middleware)
resources/views/
├── admin/
│   ├── roles/
│   │   ├── index.blade.php
│   │   ├── edit.blade.php
│   │   └── permissions.blade.php
│   └── permissions/
│       └── index.blade.php
├── components/layouts/
│   ├── nav.blade.php (modificado con submenús)
│   └── option.blade.php (podría eliminarse)
database/
├── migrations/
│   ├── xxxx_create_permission_tables.php (Spatie)
│   ├── xxxx_create_project_user_permissions_table.php (nuevo)
│   └── xxxx_add_role_columns_to_roles_table.php (nuevo)
└── seeders/
    └── MenuPermissionsSeeder.php (nuevo)
```

---

## 12. Tiempo Estimado

| Fase | Complejidad | Tiempo |
|------|-------------|--------|
| 1-3 (Setup + Migración + Admin) | Media | 3-4h |
| 4-5 (Middleware + Menú) | Media-Alta | 3-4h |
| 6-8 (Proyectos + Usuarios + Cleanup) | Alta | 5-6h |
| **Total** | | **~11-14h** |

---

## 13. Rollback

Si necesitas hacer rollback:

```bash
git checkout <commit-anterior>
```

O restaurar desde backup de BD:

```bash
php artisan migrate:rollback --step=1  # Por cada migración
```
