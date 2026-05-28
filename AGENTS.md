# AGENTS.md — OpenCode Session Guide

## Critical Setup

- **ffprobe required**: path must be set in `config/app.php` (`ffprobe_path`). Video duration/FPS/resolution won't work without it.
- **SQLite default dev DB**: schema is MySQL-compatible but `database/database.sqlite` is the default. Run `php artisan migrate` after fresh clone.
- **Base URL**: `~/www/uat.i-want-it.local` (not `demo2-iwi`)

## Dev Commands

```bash
composer dev          # PHP server + queue + pail logs + Vite (all concurrent via concurrently)
php artisan serve     # PHP server only
npm run dev           # Vite only
./vendor/bin/pint     # Code style (Laravel Pint)
php artisan test      # Tests
./vendor/bin/phpunit --filter TestName  # Single test
```

## Architecture Gotchas

- **Non-standard FK names**: `users_id` (not `user_id`), `brands_id` (not `brand_id`), `projects_id`/`versions_id` (not `project_id`/`version_id`). The `versions_id` column in `hotpoints` references `projects.id` — never renamed.
- **Composite PK on hotpoints_dates**: `(project_id, product_id, id)` — not auto-incrementing.
- **Blade anonymous components require explicit prop passing**: variables from parent views are NOT inherited. Missing a required prop causes `Undefined variable $X`.
- **Login view assets are hardcoded**: `./assets/index.66764821.js` — not Vite-manifested. Do not rebuild without updating the view.
- **`/datision-parameters` and `/options` routes bypass auth middleware** — intentional for external services and permission checks.

## The `getParams()` Controller Pattern

Every CRUD controller returns `$params['fields']` from `getParams()`. The generic layout components (`resources/views/components/layouts/`) auto-build tables, create/edit forms, and show views from this descriptor array. Do not look for per-resource view logic — it doesn't exist.

## Datision AI Pipeline

- Path domain is rewritten before sending to AI: `uat.i-want-it.local` → `uat.i-want-it.es`
- AI service endpoint: `datision_parameters.machine_url:5018`
- `project.ai_task_id` tracks the active Celery task; index page polls all active tasks in parallel via `Http::pool()`
- Detection grouping uses `DatisionParameterController::getValue('x1')` / `getValue('y1')` for XY tolerance

## Video Upload Naming

When uploading a new video, the old file is deleted and the new one stored as `time().'.'.ext` in `public/uploads/`.

## Project Permissions

The project access control system uses `ProjectPermissionHelper`:

| Access Level | Can View | Can Edit (readonly fields) | Can Do Everything |
|---|---|---|---|
| `none` | No | — | — |
| `read` | Yes | Fields disabled/readonly | Hotpoints only |
| `write` | Yes | Fields editable | + AI tabs |
| `create` | Yes | Fields editable | + delete projects |

- `ProjectController::edit()` checks `read` permission — aborts 403 if none
- `ProjectController::update()` checks `write` permission — aborts 403 if none
- Edit view propagates `readonly` prop: fields become `readonly`/`disabled`, submit replaced by "Read-only mode" message, tabs (dashboard, objects, keylist, permissions, datision) are hidden

### `ProjectPermissionHelper` methods

- `canAccess($user, $project, $level)` — checks level against user's permission row
- `canView()` / `canEdit()` / `canCreate()` — shorthand for each level
- `getAccessLevel($user, $project)` — returns `none|read|write|create` or null
- `setAccessLevel()` / `removeAccess()` — manage permissions

### Delete button visibility

Only users with `create` access (or Admin/Supervisor role) see the delete button in project listings.

## Route-based Screen Permission

`x-layouts.app` determines the screen permission from the **route name** (not the page title). For route `users.create`, permission is `users-screen`. For `projects.edit`, permission is `projects-screen`.

## Role Dropdown

Role selects (create and edit) use `$role->name` (Editor, Supervisor, Admin), not `$role->description`.

## Navigation Menu Structure

```
Projects | Products | Brands
--------------------------------
Users | Roles | Permissions
--------------------------------
Hotpoints | Tags | Territories | Security items | AI Machine CFG
--------------------------------
Logout
```

Producer, Platforms, and Player links were removed.