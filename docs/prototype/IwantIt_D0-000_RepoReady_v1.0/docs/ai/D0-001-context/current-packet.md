# D0-001 — Current Packet

**Title:** DEV-000 + Environment  
**Risk:** R3  
**Status:** READY / UNLOCKED BY D0-000

## Objective

Bootstrap the actual IwantIt greenfield repository and engineering baseline required by all subsequent D0 work.

## Required outputs

```text
apps/
  web
  api
  worker
packages/
  contracts
  domain
  temporal
  authorization
  config
  observability
  testing
infrastructure/
docs/
```

Baseline:

- TypeScript / pnpm monorepo;
- Next.js web app boots;
- modular TypeScript API boots (NestJS or equivalent modular runtime permitted by frozen DEV-000 wording);
- PostgreSQL + Prisma migration path;
- Redis + BullMQ/worker foundation;
- transactional-outbox foundation/contract boundary;
- S3-compatible storage; MinIO local/test;
- OpenAPI + typed internal contracts;
- unit + integration + Playwright capability;
- structured logs, correlation IDs, health/readiness probes;
- CI: lint + typecheck + migrations + tests + `pnpm verify`.

## Explicit OUT

No product features from D0-002+; no IAM shortcut; no Project/content schema invented for convenience; no generic demo bypass; no legacy-platform modification; no full Operations runtime unless minimally required to establish the frozen foundation.

## Acceptance

- clean clone/install succeeds;
- web/API/worker baseline builds/boots as documented;
- configuration startup validation exists;
- clean PostgreSQL migration succeeds;
- test commands run;
- `pnpm verify` exists and passes on implemented baseline;
- no secrets committed;
- ADR directory exists;
- no production schema-push shortcut;
- architecture-drift baseline checks are wired or documented for immediate CI integration.
