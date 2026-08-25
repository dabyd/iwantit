# D0-001 — Canonical Contracts

## Repository contract

The vNext M0 implementation is greenfield. The existing Laravel/MySQL platform is not modified by this execution path.

## Required engineering baseline

- workspace: TypeScript / pnpm;
- web: Next.js;
- API: NestJS or equivalent modular TypeScript runtime;
- database: PostgreSQL + Prisma migrations;
- async: Redis + BullMQ + transactional-outbox foundation;
- object storage: S3-compatible; MinIO local/test;
- contracts: OpenAPI + typed internal contracts;
- testing: unit + integration + Playwright + space for property/security suites;
- operations: structured logs, correlation IDs, health/readiness;
- CI: lint + typecheck + migrations + tests + `pnpm verify`.

## Cross-cutting implementation rules

- production evolution uses migrations, never schema push;
- environment config validated at startup;
- secrets never committed;
- signed URLs redacted from logs;
- modular-monolith boundaries use explicit contracts;
- create ADR directory before structural deviation;
- code may not introduce an alternate canonical authority for identity, Project access, media, temporal truth, content identity, async work, validation or rights.

## Source-of-Truth awareness

D0-001 does not need to implement all domain entities, but its package/module design must be able to host the frozen canonical authorities without pre-committing contradictory shortcuts. Do not create generic `Project.status`, `User.role`, `currentVideo`, or similar placeholder authorities “for later”.
