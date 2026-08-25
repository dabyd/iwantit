# D0-001 — Dependencies

## GREEN dependency

### D0-000 — Minimum AI Control Plane
Status: GREEN. Provides:

- AI Constitution v1.0;
- hash-pinned Specification Manifest;
- Context Bundle Specification;
- packet/review/adversarial/ADR/report templates;
- architecture-drift rules;
- this D0-001 Context Bundle.

## Frozen external prerequisites

The frozen specification set is the design prerequisite. No product code dependency exists because D0-001 is the repository bootstrap.

## Not assumed

- legacy Laravel code as greenfield runtime dependency;
- existing database schemas as canonical vNext schema;
- any D0-002+ packet implementation.
