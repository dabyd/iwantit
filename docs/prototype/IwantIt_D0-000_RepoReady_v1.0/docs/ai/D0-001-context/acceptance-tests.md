# D0-001 — Acceptance Tests

## Packet acceptance

1. Clean checkout + documented dependency install succeeds.
2. `apps/web`, `apps/api`, `apps/worker` and required packages exist.
3. Web app builds/boots.
4. API builds/boots and exposes health/readiness baseline.
5. Worker baseline builds/boots or has an explicit runnable health-safe foundation.
6. Environment configuration fails fast on required invalid/missing values.
7. Empty PostgreSQL database receives all current migrations successfully.
8. No production `db push`/schema-push workflow is required.
9. Redis/MinIO local/test dependencies have documented startup path.
10. OpenAPI/typed-contract baseline exists.
11. Structured logs + correlation ID baseline exists.
12. CI runs lint/typecheck/migration/test baseline.
13. `pnpm verify` runs the repository verification contract and passes.
14. No secret is committed; sample env files contain placeholders only.
15. ADR directory exists.
16. Architecture-drift rules are available to CI/review.

## D0 overlay tests already applicable

- **D0-AT-001 — Clean repository bootstrap — MUST.**
- **D0-AT-002 — Clean database migration — MUST.**

## Not yet executable / later packets

Golden seed, authentication, Project access, media and demo journey tests are not D0-001 acceptance and must not be faked to make the packet look broader.
