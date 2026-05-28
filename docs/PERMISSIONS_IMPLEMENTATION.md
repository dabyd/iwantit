# Permisos y Roles - Resumen de Implementación

**Última actualización:** Mayo 2026
**Usuario principal:** info@i-want-it.es (Alex, ID:1)

---

## Objetivo
Implementar sistema de roles y permisos usando Spatie Laravel Permission.

## Restricciones
- Paquete Spatie Laravel Permission (no custom)
- Rol único por usuario
- Permisos independientes (sin herencia)
- 4 niveles de acceso a proyectos: none, read, write, create
- Admin bypasses all permission checks
- Users visible based on `can_manage_all_users` and `can_manage_own_users` role flags

---

## Progreso

### ✅ Completado

1. **Instalación y configuración base**
   - Instalado spatie/laravel-permission v6.25.0
   - Migración inicial de roles (Admin, Supervisor, Editor) con campos: description, can_manage_all_users, can_manage_own_users
   - Trait HasRoles añadido al modelo User

2. **Seeders creados**
   - `MenuPermissionsSeeder`: 74 permisos auto-generados para el menú
   - `MigrateUsersToRolesSeeder`: Alex=Admin, XXX=Editor

3. **Controladores y rutas**
   - `Admin/RoleController`: CRUD de roles + asignación de permisos
   - `Admin/PermissionController`: Lista de permisos
   - Rutas con middleware `role` y `permission`

4. **Middleware**
   - `CheckRole.php`: Admin bypass para todos los checks de permisos
   - Middleware aliases registrados en `bootstrap/app.php`

5. **Vistas**
   - `admin/roles/index.blade.php`: Lista de roles
   - `admin/roles/create.blade.php`: Crear rol
   - `admin/roles/edit.blade.php`: Editar rol
   - `admin/roles/permissions.blade.php`: Asignar permisos a rol (checkbox)
   - `users/edit.blade.php`: Selector de rol + permisos de proyecto

6. **Tabla project_user_permissions**
   - Migración para permisos a nivel de proyecto
   - `ProjectPermissionHelper`: helper para obtener nivel de acceso

7. **Layout y navegación**
   - `nav.blade.php`: usa @can para visibilidad del menú
   - `app.blade.php`: usa @can($title . '-screen') para acceso a secciones
   - Corregido: título "Roles" → `roles-screen` (snake_case)

8. **Fixes aplicados**
   - "Class permission does not exist" → registrado PermissionMiddleware en bootstrap/app.php
   - Vistas con `@can` funcionando correctamente

---

### 🔄 En proceso

- Verificación del sistema por el usuario
- Limpieza manual pendiente (borrar OptionController, OptionHelper, Options model, UserOption model, Options views)

---

## Datos importantes

| Usuario | Rol | Permisos |
|---------|-----|----------|
| info@i-want-it.es (Alex) | Admin | 74 |
| XXX | Editor | - |

**7 usuarios** sin rol asignado.

---

## Archivos clave

| Archivo | Descripción |
|---------|-------------|
| `bootstrap/app.php` | Middleware aliases (role, permission) |
| `app/Models/User.php` | Trait HasRoles |
| `app/Http/Middleware/CheckRole.php` | Admin bypass |
| `app/Helpers/ProjectPermissionHelper.php` | Helper de permisos de proyecto |
| `app/Http/Controllers/Admin/RoleController.php` | CRUD roles + permisos |
| `app/Http/Controllers/Admin/PermissionController.php` | Lista permisos |
| `app/Http/Controllers/Admin/UserController.php` | Gestión usuarios |
| `database/migrations/*_create_project_user_permissions_table.php` | Tabla permisos proyecto |
| `database/seeders/MenuPermissionsSeeder.php` | 74 permisos |
| `database/seeders/MigrateUsersToRolesSeeder.php` | Migración inicial |
| `resources/views/components/layouts/app.blade.php` | @can($title . '-screen') |
| `resources/views/components/layouts/nav.blade.php` | @can para menú |
| `resources/views/admin/roles/*` | Vistas de roles |
| `resources/views/users/edit.blade.php` | Rol + permisos proyecto |

---

## Comandos útiles

```bash
# Ver permisos de un usuario
php artisan tinker --execute="
\$user = \App\Models\User::where('email', 'info@i-want-it.es')->first();
echo 'Has role Admin: ' . (\$user->hasRole('Admin') ? 'yes' : 'no') . '\n';
echo 'Can roles-screen: ' . (\$user->can('roles-screen') ? 'yes' : 'no') . '\n';
"

# Limpiar caches
php artisan view:clear && php artisan cache:clear

# Listar rutas admin
php artisan route:list --path=admin

# Ver roles y permisos
php artisan tinker --execute="dd(\App\Models\Role::with('permissions')->get()->toArray())"
```

---

## Permisos del sistema (74 total)

Los permisos siguen el patrón `<seccion>-screen` y `<seccion>.<accion>`:

- `dashboard-screen`
- `users-screen`, `users.create`, `users.edit`, `users.delete`
- `roles-screen`, `roles.create`, `roles.edit`, `roles.delete`, `roles.permissions`
- `permissions-screen`
- `brands-screen`, `brands.create`, `brands.edit`, `brands.delete`
- `projects-screen`, `projects.create`, `projects.edit`, `projects.delete`
- `versions-screen`, `versions.create`, `versions.edit`, `versions.delete`
- `products-screen`, `products.create`, `products.edit`, `products.delete`
- `hotpoints-screen`, `hotpoints.create`, `hotpoints.edit`, `hotpoints.delete`
- `datision-parameters-screen`, `datision-parameters.create`, `datision-parameters.edit`, `datision-parameters.delete`
- `options-screen`, `options.create`, `options.edit`, `options.delete`
- etc.

---

## siguientes pasos

1. ✅ Asignar roles a los 7 usuarios sin rol
2. 🔄 Verificar que todo funciona correctamente
3. 🗑️ Limpiar código legacy (OptionController, OptionHelper, etc.)
4. 📝 Documentar cualquier bug encontrado

---

## Problemas conocidos y soluciones

| Problema | Solución |
|----------|----------|
| "Class permission does not exist" | Registrar PermissionMiddleware en bootstrap/app.php |
| "You don't have access" en /admin/roles | Cambiar title="Role Permissions" a title="Roles" en permissions.blade.php |
| Title no coincide con permission | En app.blade.php usar `strtolower(str_replace(' ', '-', $title)) . '-screen'` |