# Wave Report Template

```text
WAVE: <id>
Packets complete: PASS | FAIL
Schema integrity: PASS | FAIL
Authorization / need-to-know: PASS | FAIL
Source-of-Truth compliance: PASS | FAIL
State-model compliance: PASS | FAIL
Temporal / ContentVersion compliance: PASS | FAIL
Entitlement / rights compliance: PASS | FAIL | N/A
E2E: PASS | FAIL
Security / performance smoke: PASS | FAIL
Open P0:
Open P1:
Architecture drift: NONE | ...
ADRs open:
GATE: READY | NOT READY
```

Only a green/READY Wave unlocks its normal successor. D0 acceleration may reorder implementation packets only where the active D0 overlay explicitly does so; it never retroactively certifies canonical M0 Waves.
