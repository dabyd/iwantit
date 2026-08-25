# D0-001 — Prohibited Shortcuts

Global architecture-drift rules apply. Packet-specific traps:

- modifying or migrating the existing Laravel/MySQL platform instead of creating greenfield vNext;
- using SQLite/MySQL as a temporary canonical database instead of PostgreSQL because it is faster;
- production schema push in place of migrations;
- provider-specific auth roles/Organizations embedded as canonical product authority in bootstrap packages;
- placeholder `User.role`, `ProjectMember`, `Project.status`, `currentVideo`, `isValidated` authorities “until later”;
- public/permanent media URLs as a shortcut;
- secrets/API keys committed to repo or fixtures;
- silently dropping `worker`, contract, authorization, temporal, observability or testing package boundaries required by DEV-000;
- starting D0-002 product features before D0-001 acceptance is GREEN;
- claiming screens/product flows implemented by repository scaffolding alone.

Any structural necessity for a different baseline runtime/database/workspace is RED → STOP/ADR.
