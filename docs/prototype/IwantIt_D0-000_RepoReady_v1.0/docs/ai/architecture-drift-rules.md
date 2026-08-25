# Architecture Drift Rules v1.0

**Status:** RELEASE-BLOCKING FOR D0

Architecture drift is a failure even when functional tests pass.

## 1. Mandatory patterns to detect/review

```text
ProjectMember / Project.members
User.role used as authorization
owner_user_id
currentVideo / current_content_version used as authority
Project.video_url or public workspace media
Project.status / analysis_status / media_status generic competing states
Appearance.is_validated as Validation history
avg_fps used as exact VFR mapping
role-name-based authorization/navigation
AI proposal written directly to accepted Core
UI thumbnail treated as Evidence
module-local permission engine
export/delivery without required UsageRight enforcement
is_demo / skip_auth / skip_entitlement / all_access authority bypass
frontend authoritative demo arrays
legacy Laravel runtime as canonical D0 dependency
```

## 2. Rule

A flagged legitimate exception requires an ADR/owner decision when it is structural. Renaming, wrapping or moving the shortcut does not make it compliant.

## 3. Packet report field

Every R2/R3 packet must state:

`Architecture drift: NONE | <finding list>`

Any unresolved drift → packet BLOCKED.
