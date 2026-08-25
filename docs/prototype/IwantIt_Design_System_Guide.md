# IwantIt — Reference UI Design System & Style Guide
*Derived from "IWI_Product_2.0_Wireframes" (Reference UI v1.0 FROZEN, 64 screens)*

This document distills the visual language, layout patterns, and component conventions used across the 64 wireframes so that a designer or an AI can regenerate screens that look and feel consistent with the existing product. It is organized as: (1) design tokens, (2) page anatomy, (3) reusable components, (4) content/interaction patterns, and (5) a prompt template for generating new screens.

---

## 1. Brand & Design Tokens

### 1.1 Color palette

| Token | Hex | Usage |
|---|---|---|
| **Navy** (`--navy`) | `#1B365D` | Left sidebar background, primary headings accents, dark UI chrome, "num"/index badges |
| **Blue** (`--blue`) | `#147BD1` | Primary actions, links, active nav item, focus states, primary buttons, active tab underline |
| **Page background** (`--bg`) | `#EEF1F4` | App canvas / body background behind cards |
| **Text** (`--text`) | `#172033` | Primary body/heading text (near-black navy) |
| **Muted text** (`--muted`) | `#667085` | Secondary text, helper copy, timestamps, labels |
| **Line / border** (`--line`) | `#D8DEE6` | Card borders, table dividers, input borders |
| **Card surface** | `#FFFFFF` | All cards, panels, tables, modals |
| **Sidebar hover/active tint** | `#F3F9FF` / light blue | Hover state on nav & index links |

**Status / semantic colors** (used consistently for pills, icons, progress bars, borders):
- **Red / Critical / Error** — destructive states, "Failed", "Critical" severity, blockers
- **Orange / Amber / Warning** — "Needs review", "Needs attention", "High" priority, pending validation
- **Blue** — "In progress", "Running", informational, "Normal" priority
- **Green** — "Completed", "Approved", "Active", success/validated states
- **Gray** — neutral/disabled, "Pending", "Not reviewed", "Hidden"

Status colors are applied as **soft pill backgrounds with matching darker text** (light tint fill + saturated text/icon), never solid saturated fills for large areas — this keeps dense data screens calm and scannable.

### 1.2 Typography
- **Font family:** Arial / Helvetica, sans-serif (system default, no display font) — utilitarian, product-focused, not marketing-styled.
- **Hierarchy:**
  - Page title (H1): ~28–32px, bold, `--text` color, sits directly under the breadcrumb, e.g. "Home", "Editor", "Operations".
  - Page subtitle: ~14px, `--muted`, one short sentence describing the screen's purpose (always present, right under the H1).
  - Section/card titles: ~15–16px, semibold, often paired with a small icon to the left.
  - Body/table text: ~13–14px.
  - Micro text (timestamps, meta): ~11–12px, `--muted`.
- Numbers/metrics inside cards (e.g. progress %, counts) are set slightly bolder/larger to draw the eye.

### 1.3 Shape & elevation
- **Corner radius:** consistently rounded — 8px for buttons/inputs/pills, 12px for cards/panels.
- **Borders:** 1px solid `--line` on cards, tables, inputs (flat design, not heavy shadows).
- **Elevation:** very subtle box-shadow on cards (`0 3px 14px rgba(25,40,60,0.06)`), a slightly stronger shadow on the sticky header. Overall the UI is "flat + bordered" rather than "shadow-heavy".
- **Spacing:** generous internal card padding (~20–24px), consistent ~20–28px gutters between cards, comfortable row height in tables (dense but not cramped).

---

## 2. Page Anatomy (applies to almost every screen)

Every screen follows the same shell:

```
┌────────────────────────────────────────────────────────────┐
│ Top bar: Breadcrumb (left) · Org/Project switcher, bell,    │
│          help "?", user avatar w/ initials (right)          │
├───────────────┬───────────────────────────────────────────┤
│               │ Page title (H1) + one-line description      │
│  Left sidebar │ ── Section-specific toolbar / filter row ── │
│  (navy, fixed)│                                              │
│  - Logo       │  Main content: cards / tables / workspace   │
│  - Main nav   │  (2–3 column responsive grid of cards, or   │
│    icons+text │   a single full-width table/workspace)      │
│  - Section    │                                              │
│    sub-nav    │  Right-hand contextual panel (optional):    │
│    (when in   │  inspector / detail / "about this" info      │
│    a project) │                                              │
│  - Collapse   │                                              │
│    toggle     │                                              │
└───────────────┴───────────────────────────────────────────┘
```

### 2.1 Left sidebar (global navigation)
- Fixed width, navy background (`--navy`), white/light text.
- Top: **"iwantit"** wordmark logo (lowercase, small dot accent) + org context.
- **MAIN NAVIGATION** section (all-caps small label, muted) with icon + label items: Home, Projects, Catalog, Operations, Integrations, Administration.
- Active item: filled blue rounded rectangle behind icon+label.
- When inside a project, the sidebar swaps to a **project-scoped sub-navigation** (e.g. Summary, Content, Editor, Analysis, Passport, Interactive, Clearance, Advertising, Settings), still with icons, plus a project switcher/breadcrumb chip at the top ("PROJECT — Emily in Paris — S...").
- When inside Administration/Settings, similarly shows a scoped list (Team & Access, Roles & Capabilities, Policies, Audit Trail, Settings) under an "ORGANIZATION" label.
- Bottom-left: a "«/» Collapse" affordance to collapse the sidebar to icons only.

### 2.2 Top bar
- White background, thin bottom border.
- **Left:** breadcrumb trail (e.g. `Organization › Effective Access Inspector`), last crumb in blue = current page.
- **Right, grouped:** organization/tenant switcher (pill/dropdown, e.g. "Acme Studios ⌄"), notification bell icon, help "?" icon in a circle, user avatar (colored circle with 2-letter initials) with a chevron for a menu.

### 2.3 Page header block
- Large page title (H1).
- One muted subtitle sentence explaining the screen's purpose — **always present**, written in plain language (e.g. "Track and manage background processes across your organization.").
- Optional right-aligned primary action button(s) or a "Saved Xm ago" status.

### 2.4 Content region patterns
Screens use one of these dominant layouts:
1. **Dashboard / card-grid** (e.g. Home): a responsive grid of independent cards, each with icon+title, optional count badge, "View all →" link, and a list of rows inside.
2. **List/table workspace** (e.g. Clearance Cases, Operations, Catalog): filter/tab bar → data table → pagination, often paired with a right-hand slide-over/detail panel when a row is selected.
3. **Full-view detail / record page** (e.g. Case Full View, Organization Full View): breadcrumb + tab strip (Summary, Assessment, Decision, Conditions, Tasks, Activity) at top of a large content pane, plus summary/metadata cards.
4. **Workspace / editor** (e.g. Editor Timeline, Analysis Workspace): 3-pane layout — left list/inspector rail, center canvas/timeline/preview, right-hand property inspector.
5. **Wizard** (e.g. New Project Wizard): centered, focused single-column flow with numbered/stepped progress.
6. **Inspector / "explain" tool** (e.g. Effective Access Inspector): input selectors at top → result banner (colored, with verdict) → a two-column "factors evaluated" list with a decision trail, each factor numbered and shown with a check icon.

---

## 3. Reusable Components

### 3.1 Status pill / badge
- Small rounded-rectangle label, uppercase or sentence case, colored per §1.1 semantic palette.
- Used for: workflow status ("IN REVIEW", "AWAITING DECISION", "RUNNING", "FAILED", "COMPLETED"), priority ("H"/"M"/"L" or "High/Normal"), severity ("CRITICAL", "HIGH", "NORMAL"), and simple booleans ("Active", "Enabled", "Authorized").

### 3.2 Buttons
- **Primary:** solid blue fill, white text, rounded 8px (e.g. "Continue", "Edit case", "Inspect Access").
- **Secondary/outline:** white background, blue border + blue text, or gray border + dark text for neutral actions ("View details", "Open editor").
- **Icon buttons:** circular/square ghost buttons for bell, help, refresh, overflow "⋮" menus.
- Buttons carrying navigation intent often include a small chevron `>` or external-link icon.

### 3.3 Cards
- White, 1px bordered, 12px radius, subtle shadow.
- Header row: icon (in a soft-colored circular chip) + title, optional numeric count badge (colored circle), optional "View all →" link on the right.
- Body: a compact list of rows, each row typically = thumbnail/avatar + title + meta line + right-aligned status pill and/or action button.

### 3.4 Tables
- Header row in muted gray text, sortable columns indicated by a small up/down caret.
- Rows separated by hairline dividers, generous vertical padding, hover highlight.
- Status/priority/decision columns use colored pills or dot+label combinations.
- Row-level actions: text buttons ("View details", "Open Project ↗") or an overflow "⋮" menu.
- Footer: "Showing X–Y of Z" left, pagination control right.

### 3.5 Filter / tab bar
- A horizontal strip beneath the page header combining: **segmented tabs with count badges** (e.g. "All 12 · Needs attention 3 · In progress 6 · Completed 18") and/or **dropdown filters** (Type, Project, Status, Assignee, Date range) followed by a "Filters" button and sometimes "Clear filters".
- Active tab: blue underline + blue text (or filled pill).

### 3.6 Progress indicators
- Thin horizontal bar (blue fill on light track) paired with a percentage label — used for in-progress operations, upload/export jobs, form completion.
- Numbered **stepper** used for wizards and for decision-trail/evaluation sequences (circled numbers 1–9 with a check icon once resolved).

### 3.7 Avatars & identity
- Circular avatar photo, or colored circle with 2-letter initials when no photo.
- Frequently paired with name + role/email as a two-line identity block (name bold, role/email muted below).

### 3.8 Right-hand inspector / info panel
- A secondary column (roughly 25–30% width) used for: contextual "About this tool" explainer boxes (icon + short paragraph + "Learn more" link), property inspectors (key/value pairs grouped under bold subheadings), or "Other surfaces in this project" quick-access lists.

### 3.9 Breadcrumbs
- Small text trail, `›` separators, all but the last item are muted/clickable links, last item is blue (current page) or plain dark text depending on context.

### 3.10 Empty/attention banners
- Colored, softly-tinted, rounded banner strips with a leading icon, used for system-level messages, e.g. green "✓ All checks passed. Effective Access = Operate" or amber warning banners.

---

## 4. Content & Interaction Conventions
- **Plain-English subtitles** under every H1 — always describe the "why" of the page in one sentence.
- **Counts everywhere**: nav items, tab labels, and card headers surface a live numeric badge (e.g. "Requires Attention 6", "Operations in Progress 2").
- **Timestamps are relative and human**: "36 min ago", "2 hours ago", "Yesterday" rather than raw dates, right-aligned or under the item title.
- **Traceability first**: entities show "Created / Last edited / Migrated from" history blocks; decisions show a numbered "Decision Trail" of the factors that led to a result.
- **Progressive disclosure**: list/quick-inspector → full-view page pattern repeats across modules (Catalog, Clearance, Interactive, Team & Access) — a compact card/slide-over for a quick look, and a dedicated full page (with tab strip) for deep detail.
- **Consistent iconography**: each module has one glyph consistently reused (e.g. shield for access/security, document for content, chart for analysis) inside a soft colored circular chip.
- **Multi-tenant awareness**: org/workspace switcher is always visible top-right; most data is implicitly scoped to "Territory · Platform · Distribution" style facets shown as small label/value pairs.

---

## 5. Prompt Template — "Generate a new IwantIt screen"

Use this as a system/style prompt when asking an AI to draft a new screen so it matches the reference UI:

> Design a web app screen in the "IwantIt" design system: a flat, bordered, card-based enterprise UI.
> - **Palette:** navy `#1B365D` sidebar, blue `#147BD1` primary actions/links, `#EEF1F4` page background, white `#FFFFFF` cards with 1px `#D8DEE6` borders and 12px radius, body text `#172033`, muted text `#667085`. Status pills use soft-tint backgrounds with matching text: red=error/critical, orange=warning/attention, blue=in-progress/info, green=success/complete, gray=neutral/pending.
> - **Typography:** Arial/Helvetica; bold page H1 with a one-line muted subtitle beneath it; semibold card/section titles with a small leading icon.
> - **Layout:** fixed navy left sidebar (logo, MAIN NAVIGATION icon+label list, active item as filled blue rounded rect, collapse toggle at bottom) + top bar (breadcrumb left; org switcher, bell, help, avatar right) + main content area with page header, then either (a) a responsive card grid, (b) a filter/tab bar above a data table with pagination, (c) a tabbed full-view record page, or (d) a 3-pane workspace (list · canvas · inspector).
> - **Components:** rounded status pills, solid-blue primary buttons / outline secondary buttons, cards with icon-chip headers and "View all →" links, tables with muted headers and pill-based status columns, thin blue progress bars with % labels, circular avatars with initials fallback, numbered stepper for sequential/decision flows, softly-tinted banner for system messages.
> - **Tone:** dense but calm enterprise SaaS — utilitarian, high information density, minimal ornamentation, everything traceable (timestamps, "who/when" history, decision trails).
> - Screen to design: **[describe the specific screen, its purpose subtitle, and its primary data/actions]**.

---

## 6. Screen Inventory (for reference)
The source file documents 64 screens across these modules — useful as a checklist of coverage/patterns already established:

- **Home** (3 states)
- **Projects** — list, New Project Wizard (2 states), Project Overview (3 states)
- **Content** — list, Versions, Version Inspector, ContentVersion Full View, Cast & Characters
- **Editor** — Timeline, Appearance Inspector, Element/Track Detail
- **Analysis** — Overview, Workspace, Workspace + Inspector, Extended Element View
- **Passport** — Preview, History
- **Interactive** — Control Overview, Elements Workspace, Element Quick Inspector, Element Full View
- **Clearance** — Overview, Workspace, Quick Inspector, Case Full View
- **Advertising** — Intelligence, Ads on Demand, Ad Break Intelligence, Character Advertising Profile, Direct Brand Opportunity, Product/Campaign/Creative Linking, Brand Relationship
- **Settings** — General, Team & Access, Modules & Licenses, Integrations, Interactive Integration, Policies, Audit Trail
- **Operations** — list, Operation Detail (Running / Failed)
- **Catalog** — list, Canonical Entity Quick Inspector, Canonical Entity Full View
- **Integrations** — list, Overview, Environment & Access, Webhooks & Delivery
- **Administration** — Organizations, Organization Full View, Licensing, Analysis Governance, Security & Support
- **Team & Access (IAM)** — Team & Access, Member Access Detail, Invite Member, Effective Access Inspector
