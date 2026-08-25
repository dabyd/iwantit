# Packet Execution Template

**Packet:** `<ID — title>`  
**Risk:** `R1 | R2 | R3`  
**Status:** `READY | IN_PROGRESS | REVIEW | BLOCKED | GREEN`

## A. Intake — complete before code

- Objective:
- Explicit OUT scope:
- GREEN dependencies:
- Registered sources used:
- Entities / Sources of Truth touched:
- Commands / queries / APIs / events touched:
- Capabilities / authority impact:
- Exact ContentVersion / temporal impact:
- Entitlement / rights impact:
- Persistence / migration impact:
- Required acceptance tests:
- Packet-specific prohibited shortcuts:

### Structural unknowns
`NONE` or list. Any unresolved structural unknown → STOP.

## B. Implementation plan

- Files to create:
- Files to modify:
- Schema/migrations:
- Contracts:
- Domain/API:
- UI:
- Tests:
- Docs:
- Rollback/reversibility:

## C. Architecture preflight

Does the plan introduce/alter/duplicate a Source of Truth, authority boundary, lifecycle, canonical state, temporal semantic, rights semantic or domain ownership rule?

`NO | YES | UNCLEAR`

- If YES/UNCLEAR: STOP / ADR candidate:

## D. AMBER decisions

For each: Decision / Reason / Alternatives / Impact / Why frozen contracts remain unchanged / Rollback.

## E. Implementation

Record actual implementation notes only; do not expand packet scope opportunistically.

## F. Verification

| Check | Result | Evidence |
|---|---|---|
| lint | | |
| typecheck | | |
| unit | | |
| integration | | |
| migrations from clean DB | | |
| packet acceptance | | |
| E2E/Playwright | | |
| adversarial/security (R3) | | |
| pnpm verify | | |

## G. Implementation report

Use `packet-report-template.md`.

## H. Review / QA / Governance

Attach independent review verdict, adversarial result where required, remediation evidence, then Governance GREEN/BLOCKED verdict.
