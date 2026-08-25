# Packet Completion Report Template

```text
PACKET: <id>
TITLE: <title>
RISK: R1 | R2 | R3
STATUS: PASS | FAIL / GREEN | BLOCKED
SPECIFICATION / CONTEXT BUNDLE VERSION:
COMMIT / PR: <when repo exists>

Dependencies assumed GREEN:
Files changed:
Schema changes:
Contracts / commands / queries / API:
Events:
Authorization rules:
Temporal / ContentVersion impact:
Entitlement / rights impact:
AMBER decisions:

Tests actually executed:
- lint:
- typecheck:
- unit:
- integration:
- migrations:
- E2E:
- security/adversarial:
- packet acceptance:
- pnpm verify:

Known limitations:
Open P0:
Open P1:
Architecture drift: NONE | ...
Architecture decision required: NONE | <ADR candidate>
Independent review verdict:
Adversarial QA verdict (if required):
Governance verdict:
Date:
```

Rules:
- planned tests are not passed tests;
- unresolved P0 or architecture drift = BLOCKED;
- D0 GREEN never implies a full M0 packet is GREEN unless canonical M0 evidence separately passes.
