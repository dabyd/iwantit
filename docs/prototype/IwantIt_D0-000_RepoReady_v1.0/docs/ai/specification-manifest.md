# IwantIt — Specification Manifest v1.0

**Status:** ACTIVE / HASH-PINNED FOR D0  
**Packet:** D0-000  
**Date:** 25 August 2026

## 1. Purpose

This registry is the only approved source list for AI packet context assembly during D0. Hash mismatch, unregistered replacement, or ambiguous version → STOP and refresh the manifest under change control.

## 2. Precedence model

1. Guía Maestra v2.22 FROZEN.
2. Architecture Freeze / canonical registries.
3. Greenfield Execution Pack v1.1.
4. M0 Screen Contract Matrix v1.0.
5. Acceptance tests / Wave gates in registered normative artifacts.
6. Correction/Traceability + FRZ-16 as lineage/freeze evidence.
7. Approved Reference UI as visual reference only.
8. D0 overlay artifacts as timebox/scope/demo controls only.
9. Code.

## 3. Registered artifacts

| ID | Artifact | Role | Tier | SHA-256 |
|---|---|---|---:|---|
| `GUIDE` | IwantIt Guía Maestra v2.22 FROZEN (`IwantIt_Guia_Maestra_v2.22_FROZEN(1).pdf`) | Normative product/domain source | 1 | `80fd3eff7d12996a6d747018fc88c12b990be188838afd170dcc7667231dae05` |
| `GREENFIELD` | M0 Greenfield Development Execution Pack v1.1 (`IwantIt_M0_Greenfield_Development_Execution_Pack_v1.1(1).pdf`) | Normative implementation contract; includes Architecture Freeze and canonical registries | 3 | `61cf9e8cc5dd028d303fdf0be1b1f99d9f9497fc1bcbd606f99a2e4f9cc75b43` |
| `SCREEN` | M0 Screen Contract Matrix v1.0 (`IwantIt_M0_Screen_Contract_Matrix_v1.0(1).pdf`) | Normative screen implementation contract | 4 | `43669fe2b3aee2a23fc0464bf873240f9cb0ade599ccfe1569cae381ede15b2e` |
| `TRACE` | M0 Correction Register & Traceability Matrix v1.0 (`IwantIt_M0_Correction_Register_and_Traceability_v1.0(1).pdf`) | Closure evidence / decision and traceability register; use to verify lineage, not to override higher-order semantics | 6 | `a85fb0e13cc34cfdcbbfc93a8a3e755ac1175c691303daf806edf62a489a7d8b` |
| `FRZ16` | FRZ-16 Final External Pre-Freeze Audit v1.0 (`IwantIt_FRZ16_Final_External_PreFreeze_Audit_v1.0(1).pdf`) | Freeze evidence: PASS / P0=0; not a replacement product specification | 6 | `10167556d73de379100aed0b2f14b25686c4559497d71b9807c3e7230b6304b7` |
| `UI` | Approved Wireframe Baseline v1.0 (`IWI_Product_2.0_Wireframes.html`) | Reference UI/composition only; written contracts prevail | 7 | `9aca99f963646adfff65c371879b20bea4f4fe0904ceae954dfb49884de3ad1f` |
| `D0_OVERLAY` | D0 Execution Overlay v0.1 (`D0_EXECUTION_OVERLAY_v0.1.md`) | D0 acceleration/execution overlay; cannot override frozen semantics | 8 | `73a2f463fe81384570eec6248744e23a666d3cebdff623e9b15565615439113f` |
| `D0_SCOPE` | D0 Scope Freeze v0.1 (`D0_SCOPE_FREEZE_v0.1.md`) | D0 timebox and scope control | 8 | `629144211de7612c15d297c88d88ba155834cadb79ddd38a92657ad7124ef9e9` |
| `D0_REGISTER` | D0 Packet Register v0.1 (`D0_PACKET_REGISTER_v0.1.md`) | D0 packet plan and risk classes | 8 | `1c3f76c66337655b2b6ec28054ff72b720eafebc40d545020748e6df00ad08fe` |
| `D0_DATA` | D0 Golden Dataset Contract v0.1 (`D0_GOLDEN_DATASET_CONTRACT_v0.1.md`) | D0 demo dataset contract | 8 | `014b3a3b8b49b358e10821bca7ddadc246555cb4e74b127ed7ab6e7fc1a90ddc` |
| `D0_AI_V01` | AI Agent Constitution v0.1 (`AI_AGENT_CONSTITUTION_v0.1.md`) | Upstream draft superseded by this D0-000 v1.0 control plane | 9 | `67e9351c9e568bc765df8cfad71ff051ef25f81e67410e79bc0d8d920807458d` |
| `D0_TESTS` | D0 Demo Acceptance Tests v0.1 (`D0_DEMO_ACCEPTANCE_TESTS_v0.1.md`) | D0 release acceptance overlay | 8 | `7e78e415f61ee9c140d4c14db48a3feed7a6c3f0a693f6877ae498c1f9d64983` |

## 4. Interpretation rules

- `GUIDE`, Architecture Freeze/canonical registries, `GREENFIELD`, and `SCREEN` contain the implementation semantics.
- `TRACE` and `FRZ16` prove closure/lineage and may be used to locate decisions, but they do not authorize overriding a higher-order contract.
- `UI` controls visual reference/composition only; incidental sample copy/data are not domain truth.
- D0 artifacts may reduce breadth and define demo-only tooling, but they may not relax authority, temporal truth, Core/Evidence/Validation semantics or M0 freeze rules.
- Conversations and model memory are not registered sources.

## 5. Hash validation

Run the D0-000 validation script before assembling a new high-risk Context Bundle when the source files are available. A changed hash requires explicit source/version review before use.
