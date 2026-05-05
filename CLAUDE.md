# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

---

## Commands

```bash
# Full dev environment (PHP server + queue + Pail logs + Vite, all concurrent)
composer dev

# Individual processes
php artisan serve
npm run dev
npm run build
php artisan queue:listen --tries=1
php artisan pail --timeout=0

# Database
php artisan migrate

# Code style
./vendor/bin/pint

# Tests
php artisan test
./vendor/bin/phpunit --filter TestName

# API docs generation (Scribe)
php artisan scribe:generate
```

---

## What This Application Does

**I Want It** is a shoppable-video platform. It allows editors to place product "hotpoints" on a video (time + XY position). An external video player queries `/api-iwi?action=get&vid=X&time=Y&key=K` and receives the list of products visible at that moment. The player renders clickable overlays; clicks and views are tracked via `ClickStatistic`.

The secondary system, **Datision**, uses an external AI service to automatically detect objects in videos and suggest which registered product/IA-class they might be.

---

## Architecture: The getParams() Controller Pattern

Every CRUD controller implements `getParams(string $data = '')`. The generic Blade layout components read this to auto-build tables, create forms, and edit forms — no per-resource view logic is needed.

```php
$params['view']     // route/view prefix, e.g. 'users'
$params['singular'] // display label singular, e.g. 'user'
$params['plural']   // display label plural, e.g. 'users'
$params['fields']   // array of field descriptors (see below)
```

**All valid field descriptor keys:**

| Key | Purpose |
|---|---|
| `label` | Display name |
| `name` | DB column name |
| `type` | `text` \| `email` \| `textarea` \| `select` \| `file` \| `image` |
| `editable` | `false` = read-only in index, `true` = shown in create/edit forms |
| `orderby` | Column is sortable in the index table |
| `hide_on_index` | Hidden in table but present in create/edit forms |
| `format` | `switch` (enabled/disabled toggle) \| `related` (FK dropdown) |
| `values` | Static `['Film','Serie']` array for select; if absent, uses dynamic `$related` |
| `nbsp` / `force_nbsp` | Prevent collapsing whitespace in index cell |
| `extra_class` | Extra CSS class on the field wrapper |
| `txt_button` | Label for image-change buttons |
| `show_when` | `['field'=>'type','value'=>'Serie']` — JS-driven conditional visibility |

Controllers that implement `getParams()`: `UserController`, `BrandController`, `ProductController`, `ProjectController`, `TagController`, `TerritoryController`, `LanguageController`, `HotpointController`, `OptionController`, `DatisionParameterController`.

Some controllers also implement `getText()` which returns labels for the two-column related-items widget (available / linked).

---

## Generic Layout Components

All resource views are thin wrappers — e.g. `resources/views/users/create.blade.php` is just one line calling a generic component. The real rendering logic lives in `resources/views/components/layouts/`.

### Critical variable-passing rule

In Laravel anonymous Blade components, variables from the parent view are **not inherited automatically**. They must be passed explicitly as attributes. Missing any required variable causes `Undefined variable $X`. This is the most common error in this codebase.

### Component reference

| Component | Required props | Optional props |
|---|---|---|
| `x-layouts.app` | `title` | — |
| `x-layouts.table` | `controller`, `datas` | `related`, `txtrelated`, `urlrelated`, `canEdit`, `canDelete`, `canCreate`, `actions` |
| `x-layouts.create` | `controller` | `related`, `txtrelated` |
| `x-layouts.edit` | `controller`, `data` | `video`, `video_fps`, `video_w`, `video_h`, `hotpointEditor`, `hotpoints`, `productos`, `related`, `txtrelated`, `keylist`, `ubp`, `datision`, `tabs`, `ia_selected_classes`, `ia_available_classes`, `ai_url`, `threshold_secs`, `ia_clases`, `objects` |
| `x-layouts.show` | `controller`, `data` | — |

`x-layouts.edit` conditionally includes tab sub-components based on which optional props are set:
- `$objects` → `tab-dashboard` + `tab-objects`
- `$hotpointEditor` → `tab-hotpoint`
- `$keylist` → `tab-keylist`
- `$ubp` → `tab-permisions`
- `$datision` → `tab-aiobjects`

`TabCounter` (`app/Helpers/TabCounter.php`) is a static counter reset at the top of `edit.blade.php` to give each tab a sequential number.

---

## Database Schema (key tables)

The schema was established before active migrations; only 3 migration files are in `database/migrations/`. Historical DDL is in `database/old_migrations/` for reference.

### Core tables

| Table | Key columns | Notes |
|---|---|---|
| `users` | `id`, `name`, `email`, `password`, `role`, `client_id` | `role`: admin/super/editor; `client_id` = self-referencing supervisor FK |
| `projects` | `id`, `name`, `filename`, `users_id`, `territories_id`, `type`, `season`, `episode`, `ai_task_id` | `filename` = uploaded video; `ai_task_id` = active Celery task ID |
| `projects_users` | `projects_id`, `users_id`, `as_owner` | Pivot; `as_owner='S'` = shared owner, null = editor |
| `brands` | `id`, `name`, `filename`, `url`, `disabled` | Logo stored in `public/uploads/` |
| `products` | `id`, `name`, `description`, `brands_id`, `filename`, `icono`, `url`, `disabled`, `auto_open` | `filename` = product image; `icono` = hotpoint icon overlay |
| `hotpoints` | `versions_id`, `products_id`, `time`, `pos_x`, `pos_y` | Core shoppable-video data; `versions_id` = projects.id (legacy name) |
| `datos_editor_hotpoints` | `versiones_id`, `data` | Raw JSON state from the visual hotpoint editor |
| `hotpoints_dates` | `project_id`, `product_id`, `id`, `date_in`, `date_out`, `price`, `url`, `estado` | Per-project pricing + scheduling for each product; composite PK |
| `tags` | `id`, `name`, `disabled` | |
| `products_tags` | `products_id`, `tags_id`, `disabled` | Disabled tag link hides product from API |
| `versions_tags` | `versions_id`, `tags_id`, `disabled` | Tags linked to a project |
| `territories` | `id`, `name` | |
| `territories_tags` | `territories_id`, `tags_id` | Products whose tags appear here are excluded when `tid` is passed to the API |
| `licenses` | `id`, `versions_id`, `key`, `name`, `disabled` | API access keys per project |
| `options` | `id`, `name`, `type` | Permission definitions (auto-created on first access check) |
| `user_options` | `user_id`, `option_id`, `active` | Grants a non-admin user access to an option |
| `datisions` | `id`, `id_project` | One record per project that has AI results |
| `datision_results` | `id`, `datision_id`, `id_object`, `class` | AI-detected object class per unique object |
| `datision_detections` | `id`, `datision_result_id`, `frame`, `x1`, `y1`, `x2`, `y2`, `width`, `height`, `center_x`, `center_y` | Bounding box per frame |
| `datision_objects_ia_classes` | `id`, `name` | Vocabulary of IA class names |
| `products_datision_objects_ia_classes` | `products_id`, `datision_objects_ia_classes_id` | Links products to the IA classes they represent |
| `datision_parameters` | `id`, `machine_url`, `threshold_sec`, `frames`, `x1`, `y1`, `low_price`, `medium_price`, `high_price`, `extra_price` | Single-row config table |
| `click_statistics` | `type`, `versions_id`, `products_id`, `brands_id`, `video_time`, `ip_address`, `browser`, `os`, `device`, … | Event log; `type`: view / click / view_p |

### Key column naming inconsistency

The codebase uses **non-standard FK names** throughout:
- `projects.users_id` (not `user_id`) — project owner
- `projects_users.projects_id` / `projects_users.users_id` (not `project_id` / `user_id`)
- `hotpoints.versions_id` — references `projects.id` (legacy name, never renamed)
- `products.brands_id` (not `brand_id`)

---

## User Roles & Access Control

| Role | Projects visible | Can edit |
|---|---|---|
| `admin` | All projects | Everything |
| `super` | Own + shared-as-owner + shared-as-editor | Own + shared-as-owner |
| `editor` | Shared-as-owner + shared-as-editor only | Per project |

`ProjectController::getProjects()` builds a UNION query that enforces these rules. It is reused in `DatisionController::index()`.

`OptionHelper::canAccess($option, $type, $user)` delegates to `OptionController::canAccess()`. On first call for an unknown option/type pair, the option is **auto-created** in the `options` table. Admins always pass. Non-admins need a matching `user_options` row with `active=1`. This is called inside `x-layouts.app` to control page-level access.

---

## The IWantIt API (`/api-iwi`)

The core public-facing endpoint, served by `IwantItController`. No auth middleware — uses license key validation instead.

**`action=get`** flow:
1. Validate `key` against `licenses` table for the given `vid` (project ID).
2. Log a `view` event in `click_statistics`.
3. Query `hotpoints` WHERE `versions_id = vid` AND `ROUND(time,4) = ROUND(?,4)`.
4. Join `products` + `brands`, applying filters:
   - `products.disabled = '0'`
   - `brands.disabled = '0'` (if brand exists)
   - No `hotpoints_dates` row with `estado='0'` for this project
   - No disabled tag link on the product
   - Territory exclusion via `territories_tags` if `tid` is passed
5. Return JSON array of product objects; URLs for `imagen`, `url`, `url_marca` are wrapped through `/track/*` for click tracking.

**Other actions**: `load`/`save` (hotpoint editor JSON), `create_keyfile`, `enable_disable_keyfile`, `delete_keyfile`, `download_keyfile`, `download_plain_keyfile`, `update_keyfile_name`, `get_projects`.

### Keyfiles

Each project can have multiple `licenses`. A keyfile is an SRT file generated by `ProjectController::generateFileKey()` and stored in `public/keyfile/`. The SRT contains the project ID + license key repeated every 10 seconds across the video duration. Files are named after the license `name` field (slugified, `*-iwik.srt`). The lighter `getFileKeyList()` returns metadata without regenerating files.

---

## Datision / AI Pipeline

The external AI service runs at `datision_parameters.machine_url:5018`.

**Launch flow** (`POST /ai/launch` → `AiGatewayController::launch`):
1. If `project.ai_task_id` is already set → query `/v1/get_result/{taskId}` and return current state.
2. Otherwise → POST to AI service with `{classes, id_project, path, threshold_sec}`.
3. Save returned `task_id` in `projects.ai_task_id`.
4. Path domain is rewritten: `demo2-iwi.test` → `uat.i-want-it.es` before sending to AI.

**States**: `PENDING` / `PROGRESS` (with percent string) / `SUCCESS` (clears `ai_task_id`).

The index page (`ProjectController::index`) polls all active tasks in parallel using `Http::pool()` to display real-time status without blocking.

**Receiving results** (`POST /datision-upgrade` → `DatisionController::upgrade`):
- Creates/updates `datisions`, `datision_results`, `datision_detections` records.
- One `Datision` per project; one `DatisionResult` per detected object; many `DatisionDetection` per result.

**Detection grouping** (`GET /datision-detections/{project_id}/{object_class}/{distance_frames}`):
- `DatisionController::getObjectDetections()` fetches all detections for a class, groups consecutive frames within `distance_frames` tolerance, and uses `buscaObjeto()` to track objects across frames by XY proximity.
- `DatisionParameterController::getValue('x1')` / `getValue('y1')` provide the XY tolerance from the config table.

**Linking detections to products** (`IaProducts::byIaClass($className)`):
- Looks up products that have the given IA class in the `products_datision_objects_ia_classes` pivot.

---

## File Handling

- **Uploads**: all user-uploaded files go to `public/uploads/` (images, logos, icons, videos).
- **Keyfiles**: SRT license files go to `public/keyfile/`.
- **Video info**: `getVideoInfo($path)` in `VideoHelper.php` calls `ffprobe` (path set in `config/app.php` as `ffprobe_path`). Results are memory-cached per path. Wrapper functions: `getVideoFPS()`, `getVideoResolution()`, `getVideoDuration()`.
- When uploading a new video for a project, the old file is deleted from disk and the new one stored as `time().'.'.ext`.
- Products have two image fields: `filename` (product image) and `icono` (hotpoint overlay icon).

---

## Hotpoint Editor

The visual hotpoint editor lives in the project edit view. It uses `IwantitController::save_hotpoints()` (via `action=save`) to persist a JSON blob to `datos_editor_hotpoints` AND write individual rows to the `hotpoints` table. The JSON is the authoritative editor state; `hotpoints` rows are used by the API.

`Hotpoint::getGroupedHotpoints($versionId)` groups hotpoint rows by `products_id` and then by time-proximity (default 0.5s threshold) to produce segments for display in the "dashboard" tab.

`HotpointsDate` stores per-product, per-project metadata: enabled/disabled status, price-per-second, date window, and a custom URL. Dates accept `dd/mm/yyyy` from the frontend and are stored as `Y-m-d`.

---

## Routing Notes

- `GET /` → redirects to `/projects`
- The login view uses **hardcoded static asset paths** (`./assets/index.66764821.js`) — not Vite-manifested. Do not change these without updating the build.
- `/products` routes are declared individually (not as a `Route::resource`) to allow the extra `/products/{product}/ia-classes` route without conflict.
- `/datision-parameters` is not behind `auth` middleware (intentional — accessible to external services).
- `/options` resource has no `auth` middleware.
- AJAX project-user management endpoints (`/projects/{id}/available-users`, `add-user`, `remove-user`, `update-role`) have no explicit `auth` middleware — they rely on session.

---

## Helpers Summary

| Helper | Location | Purpose |
|---|---|---|
| `getVideoInfo()` / `getVideoFPS()` / `getVideoDuration()` | `VideoHelper.php` | ffprobe wrapper, memory-cached |
| `generateSrtContent()` / `formatSrtTimestamp()` | `VideoHelper.php` | Generate SRT keyfile content |
| `getAbsoluteFileUrl()` | `VideoHelper.php` | Convert relative upload path to absolute URL, with CLI-safe fallback |
| `formatSecondsToTime()` | `TimeHelper.php` | Format seconds as HH:MM:SS |
| `IaProducts::byIaClass($name)` | `IaProducts.php` | Products linked to a given IA class name (exact match) |
| `OptionHelper::canAccess()` | `OptionHelper.php` | Thin wrapper around `OptionController::canAccess()` |
| `TabCounter::incrementAndGet()` / `reset()` | `TabCounter.php` | Sequential tab numbering in the edit layout |

---

## Tech Stack

- **Backend**: Laravel 11, PHP 8.2+, Spatie Laravel Permission, Laravel Sanctum
- **Frontend**: Blade components, TailwindCSS 3, Vite 6, Axios
- **Database**: SQLite (default dev), MySQL-compatible schema
- **Dev tools**: Laravel Debugbar, Telescope, Pint (code style), Scribe (API docs at `/api-docs`)
- **External dependency**: `ffprobe` must be installed and its path set in `config/app.php` (`ffprobe_path`)
