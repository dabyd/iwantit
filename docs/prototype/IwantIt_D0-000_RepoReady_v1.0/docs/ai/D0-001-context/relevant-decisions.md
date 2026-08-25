# D0-001 — Relevant Frozen Decisions

Only decisions materially constraining the bootstrap are listed.

- **D199 / D201:** managed authentication sits behind a provider-independent IwantIt identity boundary; do not couple repository structure to provider roles/Organizations as product authority.
- **D203:** internal/support access is not implicit superuser authority; bootstrap must not encode an `INTERNAL_ADMIN == all data` assumption.
- **D208:** governed sensitive writes will require optimistic concurrency (`revision` + `expected_revision`); repository/domain conventions must not assume universal last-write-wins.
- **D209:** one taxonomy engine; do not pre-create separate family/context vocabulary engines.
- **D212:** ValidationDecision is immutable/dimensional; do not reserve a flat `is_validated` truth shortcut.
- **D216:** D0/M0 detail access uses real scoped rights, not demo bypass flags.
- **D217:** written screen contracts prevail over incidental raster data.
- **D218:** WorkContext/NFR/traceability are cross-cutting; repository docs/testing/observability must support later evidence.
- **FRZ-16:** structural changes after freeze require ADR/owner approval; implementation agents may not reinterpret frozen invariants locally.
