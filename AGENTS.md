# AGENTS.md — OpenCode Session Guide

## Critical Setup

- **ffprobe required**: path must be set in `config/app.php` (`ffprobe_path`). Video duration/FPS/resolution won't work without it.
- **SQLite default dev DB**: schema is MySQL-compatible but `database/database.sqlite` is the default. Run `php artisan migrate` after fresh clone.

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

- Path domain is rewritten before sending to AI: `demo2-iwi.test` → `uat.i-want-it.es`
- AI service endpoint: `datision_parameters.machine_url:5018`
- `project.ai_task_id` tracks the active Celery task; index page polls all active tasks in parallel via `Http::pool()`
- Detection grouping uses `DatisionParameterController::getValue('x1')` / `getValue('y1')` for XY tolerance

## Video Upload Naming

When uploading a new video, the old file is deleted and the new one stored as `time().'.'.ext` in `public/uploads/`.
