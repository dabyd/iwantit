# slice-wow-frontend

## Why

The WOW backend already exposes real, authorised project-analysis data, but the current Analysis tab renders the Overview and Advertising list as one basic view. It eagerly requests both endpoints, makes one failed request hide the other surface, and interpolates API content through `innerHTML`.

The reference wireframes establish the product intent, but Advertising Intelligence in the reference requires product concepts that the WOW MVP does not yet expose. Reproducing it literally would present invented values and misrepresent the product.

## What Changes

- Turn the existing `Analysis` tab into two in-tab surfaces: **Overview** and **Advertising**.
- Build both surfaces with the existing Bootstrap 5 backoffice conventions so they fit the `.tab-1` to `.tab-9` system.
- Render only fields returned by the two existing WOW endpoints.
- Defer data loading until the Analysis parent tab is active. Load Overview and Advertising independently; load Advertising only after its sub-navigation item is selected.
- Replace dynamic `innerHTML` rendering with safe DOM construction and `textContent` so imported or stored API strings cannot execute markup.
- Add independent loading, empty, error, and retry states for each surface.

## Non-goals

- No new backend endpoint, migration, model, route, permission, or API-contract change.
- No attempt to reproduce the full `ADV-01` Advertising Intelligence dashboard: no platforms, characters, ad breaks, Ads Data API delivery, Direct Brand Opportunities, or made-up metrics.
- No migration to Tailwind, Vite source restructuring, or global tab-system rewrite.
- No changes outside the frontend scope required for this Analysis tab unless implementation evidence proves one is indispensable.

## Scope

- Primary implementation target: `resources/views/components/layouts/tab-analysis.blade.php`.
- Existing dependencies retained: Bootstrap 5 in `public/css/app.css`, the global `.tab-N` navigation in `public/js/app.js`, and the existing JSON endpoints.

## Success Criteria

- Users with the existing `analysis-screen` capability and project read access see a Bootstrap-native Analysis experience.
- No endpoint is requested while the Analysis parent tab is inactive.
- Overview remains usable if Advertising fails, and vice versa.
- Advertising filters request only the selected `high`, `medium`, or `low` level; the all-level state omits `level`.
- API-provided labels and rationale render as text, never executable HTML.
