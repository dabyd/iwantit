# IwantIt — D0-000 Minimum AI Control Plane v1.0

**Status:** GREEN  
**Date:** 25 August 2026  
**Unlocks:** D0-001 — DEV-000 + Environment

This package closes the first D0 execution packet. It contains the minimum AI-development governance needed to start implementation without reopening frozen product/architecture semantics.

## Required repository files delivered

- `docs/ai/constitution.md`
- `docs/ai/specification-manifest.md` (+ machine-readable YAML)
- `docs/ai/packet-template.md`
- `docs/ai/review-protocol.md`
- `docs/ai/adr-template.md`
- `docs/ai/packet-report-template.md`

## Additional controls delivered

- Context Bundle Specification
- Adversarial QA Protocol
- Architecture Drift Rules
- Wave Report + Governance Verdict templates
- complete ready-to-use D0-001 Context Bundle
- D0 Packet Register v0.2
- machine validation script and results
- D0-000 packet/review/dry-run/closure records

## Usage

1. Copy `docs/ai/` into the greenfield repo during D0-001 bootstrap.
2. Copy/create `docs/adr/` using the ADR template.
3. Keep the hash-pinned manifest synchronized with the controlled specification set.
4. Give implementation agents the current packet Context Bundle first, not chat history.
5. Do not advance a packet with open P0 or architecture drift.

D0 GREEN is D0 execution evidence only. It does not certify any full M0 product packet or Wave.
