# Design: WOW MVP frontend

## Context

The project edit screen constructs its parent navigation from `.tab-1` through `.tab-9`. `tab-analysis.blade.php` is one of those parent panes and has access to the current `Project` as `$data`.

The application serves Bootstrap 5 and its backoffice CSS from `public/css/app.css`; it does not currently serve a Tailwind-built analysis surface. The design must therefore use Bootstrap cards, rows, list groups, badges, buttons, alerts, progress bars, and responsive tables, with narrowly scoped CSS only where Bootstrap does not cover the interaction.

The existing backend contract is deliberately small:

```text
Overview endpoint
├── content_intelligence: scenes, elements, appearances, relationships
├── business_opportunities.advertising: high, medium, low
├── business_opportunities.clearance_relevant
└── key_contexts: name, scenes

Advertising endpoint
└── items[]: value_level, scene, elements, contexts, rationale,
            start_ms, end_ms, duration_ms
```

## Surface Model

```text
Analysis parent tab (.tab-N)
│
├── Overview (default subview)
│   ├── Content Intelligence: four KPI cards
│   ├── Business Opportunities: Advertising H/M/L and Clearance total
│   └── Key Contexts: ranked list with derived scene percentage
│
└── Advertising
    ├── All / High / Medium / Low filter controls
    └── Responsive opportunities table
```

The sub-navigation is semantic button-based Bootstrap navigation (`nav nav-tabs` or equivalent), not another global `.tab-N` entry. It will expose `aria-selected`, link each control to its panel with `aria-controls`, and keep the visible panel in the ordinary tab order.

### Overview

Only available facts appear:

- Four responsive `card` KPIs for scenes, identified elements, appearances, and contextual relationships.
- An Advertising card containing the three existing H/M/L counts with Bootstrap status badges.
- A Clearance card containing only `clearance_relevant`; it must not pretend to contain categories or rights status.
- A ranked Key Contexts list. Each row shows its name, scene count, and a percentage derived from `context.scenes / content_intelligence.scenes`. When the total is zero, it shows a neutral zero state rather than dividing by zero.

No Interactive card, analysis-complete status, content-version metadata, or CTA will be fabricated because these are not in the contract.

### Advertising

Advertising shows the existing opportunity-list contract as a responsive Bootstrap table:

- Value level is displayed with a class selected from a hard-coded `high|medium|low` map.
- Scene, element name/type/on-screen time, contexts, opportunity interval, and rationale are shown when supplied.
- `null` scene or time values use a neutral em dash, not invented content.
- The filter starts at **All**. Selecting a level appends the validated query parameter and fetches that level. Selecting All removes it.

The desktop wireframe remains a visual reference for density, hierarchy, status colour, and calm enterprise presentation; its unsupported Advertising Intelligence modules are intentionally absent.

## Loading Lifecycle

```text
page ready
  │
  └─ observe class changes on Analysis parent pane
       │
       ├─ inactive ──────────────── no network request
       │
       └─ becomes active
            └─ load Overview once
                 │
                 ├─ success → render Overview
                 └─ failure → Overview-only error + retry

user selects Advertising
  └─ load current filter independently
       ├─ success → render table
       └─ failure → Advertising-only error + retry
```

The component will find its own parent `.tab-N` and observe its `class` attribute. This aligns with the existing global tab script, which owns the addition and removal of `active`, without requiring a global-script rewrite.

Each surface maintains its own status (`idle`, `loading`, `ready`, `error`), response cache, and retry action. Overview completion must never wait on Advertising. Re-selecting a surface reuses the cached response unless the user explicitly changes the Advertising filter or retries after failure.

Requests must validate `response.ok` and JSON content before rendering. Authentication redirects that resolve to HTML must become a local error state, not an uncaught JSON parsing error.

## Safe Rendering

Dynamic API values are untrusted presentation data even when today they originate from a curated CSV. The implementation will:

- create dynamic nodes through `document.createElement`;
- assign API strings only with `textContent`;
- set value-level badge classes exclusively through the fixed level-to-class map;
- use DOM replacement APIs (`replaceChildren`) instead of generated HTML strings.

Static Blade markup may remain declarative. No HTML sanitizer dependency is needed because dynamic HTML is not constructed.

## Accessibility and Responsive Behaviour

- Buttons use `type="button"`; selected filters and subviews expose their current state to assistive technology.
- Each surface has an `aria-live="polite"` status region for loading and error feedback.
- Retry controls are keyboard-operable buttons.
- Table headings use `scope="col"`; the existing Bootstrap `table-responsive` wrapper preserves small-screen usability.
- KPI cards use a responsive Bootstrap grid rather than fixed `col-3` sizing, preventing compressed cards on narrow screens.
- Bootstrap focus styles are preserved; any scoped styling must not remove focus visibility or reduce text contrast.

## Testing and Verification Strategy

- Run the current Laravel feature tests to guard endpoint behaviour.
- Exercise project 12 after importing the WOW demo dataset.
- Verify no network request occurs before selecting Analysis, then verify Overview and Advertising requests independently in browser network tools.
- Exercise All, High, Medium, and Low filters; empty results; a forced request failure; and retry behaviour.
- Verify an API value resembling HTML is rendered literally as text.
- Verify keyboard navigation, small-screen table overflow, and the project tab system still work.

## Risks and Mitigations

| Risk | Mitigation |
|---|---|
| Parent tab changes are owned by legacy global JS | Observe only the component's parent class; do not patch the global system. |
| Reference UI implies unavailable data | Keep the implementation contract-first and omit unsupported modules. |
| Stored/imported text contains markup | Render all API strings with `textContent`. |
| A dependent endpoint fails | Maintain independent surface state and retry controls. |
| Bootstrap and Tailwind guidance conflict | Target the CSS actually served by the app: Bootstrap plus established backoffice conventions. |
