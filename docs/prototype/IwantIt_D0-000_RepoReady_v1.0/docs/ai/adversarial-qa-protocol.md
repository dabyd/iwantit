# Adversarial QA Protocol v1.0

**Status:** ACTIVE FOR D0  
**Required:** R3 packets; targeted use for R2 when risk warrants

## 1. Goal

Attempt to invalidate the implementation contract using hostile or boundary inputs. Adversarial QA is not feature exploration and may not broaden product semantics.

## 2. Standard attack families

- unauthorized/guessed object IDs;
- stale/expired/revoked membership or Project access;
- Acting Organization switch or mismatch;
- hidden-resource leakage through list/count/search/facet/activity/notification;
- stale revision / double submit / concurrent writes;
- repeated/idempotency-sensitive command;
- wrong ContentVersion ID;
- negative, zero-length, overlapping or out-of-bounds time ranges as applicable;
- VFR/frame mapping misuse;
- expired/revoked entitlement;
- deep-link/return-context manipulation;
- signed-URL reuse/log exposure;
- partial failure/retry/replay;
- seed/import duplicate or referential corruption;
- Evidence source/version/time mismatch;
- fake demo/reset endpoint escaping demo/staging boundary;
- alternate route/query reconstructing gated detail.

## 3. Packet-specific selection

The Context Bundle must mark applicable attack families. R3 Governance cannot be GREEN with the applicable adversarial set omitted.

## 4. Evidence

Record input, expected contract behavior, actual result and relevant logs/test identifier. A security/authority/temporal truth mismatch is P0.
