# Independent Review Protocol v1.0

**Status:** ACTIVE FOR D0  
**Owner packet:** D0-000

## 1. Independence rule

For R2/R3, review is performed in a fresh review context. The reviewer receives the packet Context Bundle, implementation diff/code, migrations/contracts, executed test outputs and implementation report. The implementer's chain of reasoning is not evidence and should not be supplied as a justification.

## 2. Review order

1. Verify source/version/packet identity.
2. Reconstruct expected behavior from the Context Bundle before reading implementation conclusions.
3. Inspect schema/contracts first, then domain/API, then UI.
4. Compare authorization-before-composition, exact-version and state semantics.
5. Search explicitly for architecture-drift patterns.
6. Verify tests assert the contract rather than current implementation accidents.
7. Verify D0 shortcuts narrow breadth only.
8. Classify findings P0/P1/P2/P3.
9. Issue one verdict only.

## 3. Mandatory negative checklist

- second Source of Truth or local competing state;
- role-name authorization or demo bypass;
- hidden data leaked through counts/search/navigation/activity/deep links;
- implicit Acting Organization;
- wrong/implicit ContentVersion;
- `latest/current video` shortcut;
- temporal clamping or invalid half-open range behavior;
- stale write overwrite / missing expected_revision where required;
- AI proposal → Core direct acceptance;
- UI thumbnail → Evidence;
- provenance treated as validation/quality;
- derived metric persisted/edited as authority;
- scoped relevance silently expanded;
- entitlement bypass/reconstruction path;
- legacy runtime treated as D0 canonical dependency;
- fake demo behavior presented as implemented truth;
- unregistered dependency or source.

## 4. Risk depth

### R1
Code/CI and targeted contract review.

### R2
Independent contract review + targeted integration tests + negative checklist.

### R3
All R2 checks + adversarial/security/architecture testing. No self-certification.

## 5. Verdicts

- `PASS`
- `PASS_WITH_P1`
- `FAIL_P0`
- `FAIL_ARCHITECTURE_DRIFT`
- `ADR_REQUIRED`

A verdict must include evidence references and each finding's severity. `PASS_WITH_P1` does not automatically mean GREEN; Governance applies packet/D0 deferral policy.

## 6. Review record minimum

```text
Packet / commit / diff scope
Sources/bundle version
Risk level
Checks performed
Findings table
Architecture drift result
Acceptance-test evidence reviewed
Verdict
Reviewer role/date
```
