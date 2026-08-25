# D0-001 — Compact Constitution

Follow `../constitution.md`. For this packet, especially preserve:

1. Greenfield vNext is separate; existing Laravel/MySQL product remains untouched.
2. TypeScript/pnpm monorepo; Next.js web; modular TypeScript API; PostgreSQL + Prisma migrations; Redis/BullMQ + transactional outbox foundation; S3-compatible/MinIO local/test; OpenAPI/typed contracts; unit/integration/Playwright/property-security capability; structured logs/correlation IDs; health/readiness; CI and `pnpm verify`.
3. No production schema-push shortcut; migrations are the evolution path.
4. Validate environment configuration at startup.
5. Never commit secrets; redact signed URLs from logs.
6. Modular-monolith boundaries use explicit contracts even if no network transport exists.
7. ADR directory must exist before any structural deviation.
8. No D0 implementation may introduce a second Source of Truth, broaden authority, silently change exact ContentVersion binding or bypass entitlement/Audit.
9. D0-001 builds the platform foundation only; it does not implement product-domain shortcuts for later packets.
