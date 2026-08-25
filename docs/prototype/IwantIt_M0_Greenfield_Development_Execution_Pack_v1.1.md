**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

### **M0 GREENFIELD DEVELOPMENT EXECUTION PACK v1.1 Frozen for Implementation** 

#### **00. Document control & how to use this pack** 

###### Purpose 

This v1.1 supersedes v1.0 for implementation. It preserves the historical v1.0 baseline and appends a normative remediation overlay implementing FRZ-01 through FRZ-15. Where the overlay conflicts with earlier v1.0 packet text, the v1.1 overlay prevails. FRZ-16 returned P0=0 on 24 August 2026; this specification is FROZEN FOR IMPLEMENTATION. 

|**Field**|**Value**|
|---|---|
|Document|IwantIt - M0 Greenfeld Development Execution Pack|
|Version|v1.1|
|Date|24 August 2026|
|Status|FROZEN FOR IMPLEMENTATION - FRZ-16 PASS / P0=0|
|Normative source|IwantIt Guía Maestra v2.22 FROZEN composite|
|Productgate|M0 - Understand the Content|
|Execution model|Wave-based; packet-by-packet; gates stop forward progress on<br>structural failure|
|Primaryimplementer|Engineeringteam / Codex-assisted implementation<br>|
|Repository strategy|Greenfeld vNext; existing Laravel/MySQL platform remains<br>untouched duringM0 build|



##### **Precedence** 

```
Guía Maestra v2.22 Freeze Candidate
  |
M0 Architecture Freeze
  |
Canonical registries
  |
Development Packets
  |
Acceptance tests / Wave gates
  |
Code
```

- If a packet contradicts the Guía Maestra, the Guía prevails. 

- If two packets conflict, the Architecture Freeze and canonical registries prevail. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 1 

###### **IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

- An implementation shortcut may never create a second Source of Truth, broaden authority, silently change exact ContentVersion binding, or bypass entitlement/Audit. 

- Any structural change after freeze requires an explicit ADR and must be evaluated against M0 exit gates. 

##### **Execution protocol** 

1. Read the Architecture Freeze and the current Wave prerequisites. 

2. Implement one packet, including schema, contracts, APIs/commands, authorization, tests and docs. 

3. Run unit, integration and E2E tests relevant to the packet and then pnpm verify. 

4. Produce the mandatory packet report. Do not continue if a P0 or architecture drift is detected. 

5. At Wave completion, run the Wave hardening gate and produce the Wave report. 

6. Only a green Wave may unlock the next Wave. 

#### **01. Normative basis and M0 product contract** 

###### **M0 question** 

Can IwantIt convert audiovisual content into structured and validated Content Intelligence? 

```
Create -> Analyse -> Understand -> Validate -> Monetize -> Serve
```

- M0 stops after validated, operable Content Intelligence and authorized re-entry; M1 monetizes that intelligence through governed Interactive/Advertising delivery. 

- The M0 exit criterion is: an authorized user can create a minimal Project, upload audiovisual, execute/re-enter Initial Analysis, temporally correct Core in Editor, validate knowledge and return from Home/Projects/Overview without loss of scope, version binding or authorization. 

- Manual-first and human-in-the-loop behavior is valid. AI autonomy is not an M0 gate. 

- Catalog and Operations may be implemented as infrastructure before their full M2 user-facing workspaces. 

- Licensing/entitlement, authorization, exact version binding and Audit are architectural gates and may not be bypassed for speed. 

|**Area**|**M0 required**|**Deferred beyond M0**|
|---|---|---|
|IAM / Organization|User, Organization, Membership, Acting<br>Organization, ProjectAccessGrant, standard<br>Role Packages, need-to-know backend|Custom roles, certifcation, full inspector|
|New Project|NPW-01 Quick Create & Analyse; NPW-02<br>Operationprojection|Advanced onboarding|
|Project / Content|Project, Content, CV-001, primary<br>audiovisual Asset/AssetFile, exact version<br>binding|Advanced multiversion UX/reconform|
|Operations|Operation/Step/Attempt, honest progress,<br>waiting/retry, owner-routed result, auth-<br>before-composition|Advanced diagnostics/OPS-03|
|Core Inventory|InventoryItem, Appearance, Evidence basic,<br>Validation, provenance, minimum<br>Type/Family|Advanced tracking/automation<br>|
|Editor|Timeline, inspectors, timing,<br>regroup/reassign/split, Evidence, Validation|Advanced confict/reconform/productivity|
|Analysis|Overview, Workspace, Inspector/Extended,<br>Context Taxonomy, vertical relevance,<br>Business Opportunities|Autonomous AI/advanced exception<br>automation|
|Home/Projects/Overview|Basic authorized surfaces, Attention,<br>Operations and re-entry|Advanced portfolio intelligence|
|Catalog|Minimum canonical<br>Brand/Product/Person/Character and<br>CanonicalLink|Full CAT workfows/reconciliation|



Internal  /  Implementation Contract  /  24 Aug 2026 

Page 2 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

#### **02. Architecture Freeze** 

###### **Constitutional rule** 

No implementation detail may create a second authority for identity, Project access, media source, temporal truth, content identity, asynchronous work, validation or rights. 

|**Concept**|**Canonical Source of Truth**|
|---|---|
|Authenticated identity|User|
|User <-> Organization|OrganizationMembership|
|Active organizational context|ActingOrganization in session/request context|
|Standard capability package|RolePackageVersion + CapabilityRegistry|
|Project owner|Project.owner_organization_id|
|Project-scoped authority|ProjectAccessGrant|
|Collaboration limit|Organization-target ProjectAccessGrant ceiling|
|Editorial content|Content|
|Versioned temporal identity|ContentVersion|
|Logical media|Asset|
|Canonical source bytes|ACTIVE AssetFile<br>|
|Technical media truth|MediaTechnicalProfle|
|Temporal truth|Exact ContentVersion + integer TimecodeMs + [start,end)|
|Project-local content identity|InventoryItem|
|Canonical/global identity|CanonicalEntity|
|Local<->canonical relation|CanonicalLink|
|Occurrence in time|Appearance|
|Narrative/temporal structure|Scene|
|AIproposal|DetectionCandidate|
|Analysis execution|AnalysisRun|
|Consistent analysis result|AnalysisSnapshot|
|Human decision history|ValidationDecision|
|Governed evidence|Evidence|
|Persistentprocess|Operation / OperationStep/ OperationAttempt<br>|
|Contractual usage|License / LicenseTerm / UsageRightGrant / EfectiveEntitlement|



##### **Hard-prohibited implementation shortcuts** 

```
User.role
ProjectMember / Project.members[]
Project.owner_user_id
Project.current_video / Project.video_url
Project.current_content_version_id as temporal authority
Project.analysis_status / media_status / generic status
Appearance.is_validated as audit history
latestVersion() inside temporal mutation commands
avg_fps used as exact VFR frame truth
public workspace media URLs
AI proposals written directly as accepted Core truth
UI thumbnail treated as Evidence
role-name-based navigation or authorization
```

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 3 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

#### **03. Canonical registries** 

##### **03.1 Capability Registry** 

###### **Rule** 

Capabilities are the executable language of authorization. Role names are presets; they are never authorization conditions. 

|**Domain**|**Capability**|**Scope**|**Rule**|
|---|---|---|---|
|Organization / IAM|organization.view|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Organization / IAM|organization.members.view|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Organization / IAM|organization.members.manage|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Organization / IAM|organization.invitations.manage|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Organization / IAM|project.create|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Organization / IAM|project.portfolio.view|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Organization / IAM|project.access.view|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Organization / IAM|project.access.manage|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Organization / IAM|project.assignments.view|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Organization / IAM|project.assignments.manage|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Project / Content|project.view|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Project / Content|project.manage|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Project / Content|project.archive|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Project / Content|content.view|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Project / Content|content.manage|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Project / Content|asset.view|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Project / Content|asset.upload|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Project / Content|asset.manage|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Analysis|analysis.view|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Analysis|analysis.run|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Analysis|analysis.review|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Editor / Core|editor.view|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Editor / Core|inventory.view|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Editor / Core|inventory.create|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Editor / Core|inventory.edit|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Editor / Core|inventory.merge|Project or Organization as|Server-side; delegability declared|
|||<br>registered|<br>in registry|



Internal  /  Implementation Contract  /  24 Aug 2026 

Page 4 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

|**Domain**|**Capability**|**Scope**|**Rule**|
|---|---|---|---|
|Editor / Core|inventory.archive|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Editor / Core|appearance.view|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Editor / Core|appearance.create|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Editor / Core|appearance.edit|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Editor / Core|appearance.archive|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Editor / Core|scene.view|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Editor / Core|scene.create|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Editor / Core|scene.edit|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Editor / Core|scene.split|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Editor / Core|scene.merge|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Validation / Evidence|validation.view|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Validation / Evidence|validation.decide|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Validation / Evidence|evidence.view|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Validation / Evidence|evidence.create|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Catalog|catalog.view|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Catalog|catalog.link|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Operations|operations.view|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Operations|operations.retry|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Operations|operations.cancel|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Settings|project.settings.general.view|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Settings|project.settings.general.manage|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|
|Settings|project.settings.audit.view|Project or Organization as<br>registered|Server-side; delegability declared<br>in registry|



##### **03.2 Standard Role Packages** 

- ORGANIZATION_ADMIN 

- PROJECT_MANAGER 

- CONTENT_EDITOR 

- VALIDATOR 

- ANALYST 

- PP_INTERACTIVE_EDITOR 

- CLEARANCE_REVIEWER 

- CLEARANCE_AUTHORITY 

- ADVERTISING_MANAGER 

- VIEWER 

- INTEGRATION_ADMIN 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 5 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

- INTERNAL_ADMIN 

###### **Versioning** 

Every standard package is immutable once published. A RolePackage v2 does not silently migrate existing memberships. Project authority is always separately delegated. 

##### **03.3 Effective Access** 

```
Membership capability
& Acting Organization
& Organization collaboration ceiling (when collaborator)
& individual Project assignment
& surface/resource state
& Data Visibility
& domain policy
& Effective Entitlement / UsageRight when applicable
= Effective Action/Data Access
```

- Owner ordinary members still require individual Project assignment for ordinary business access. 

- Organization Admin has privileged administrative discovery/recovery, not universal arbitrary business access. 

- Collaboration grant expansion never automatically expands existing individual assignments. 

- Collaboration ceiling reduction immediately reduces effective authority; historical assignment intent is retained. 

- A revoked parent collaboration grant cannot be revived by attaching children to a new grant. 

##### **03.4 Data Visibility & need-to-know** 

|**Classifcation**|**Meaning**|
|---|---|
|global|Reusable identity/taxonomywith no narrower restriction|
|iwantit_internal|Internal operational information|
|organization_private|Visible onlywithin an authorized Organization context|
|project_private|Authorized Project universe only|
|module_private|Private to authorizedproduct/module surface|
|delivery_restricted|Materialgoverned bydelivery/rights constraints|
|personal_restricted|Personal data requiringadditional handling|
|biometric_restricted|Biometric data; not introduced in M0|



- Hidden resources are not serialized into navigation, counts, search, activity, badges or notifications. 

- Locked/upsell is allowed only where commercial discoverability policy explicitly permits it. 

- Guessed deep links and object IDs must reauthorize and use no-existence-safe denial where required. 

##### **03.5 State Model** 

|**Owner**|**Canonical states**|
|---|---|
|Project.lifecycle|DRAFT  /  ACTIVE  /  ARCHIVED|
|Project.condition|NORMAL  /  RESTRICTED  /  SUSPENDED|
|ContentVersion.lifecycle|DRAFT  /  READY_FOR_REVIEW  /  APPROVED  /  REJECTED  /<br>SUPERSEDED  /  ARCHIVED|
|UploadSession|PENDING  /  UPLOADED  /  CLAIMED  /  EXPIRED  /  INVALID|
|AssetFile.lifecycle|ACTIVE  /  SUPERSEDED|
|AssetFile.technical_state|UPLOADED  /  VALIDATING  /  VALID  /  INVALID|
|Operation|PENDING  /  RUNNING  /  WAITING  /  COMPLETED  /  FAILED  /<br>CANCELLED  /  COMPENSATED|
|OperationStep|PENDING  /  RUNNING  /  WAITING  /  COMPLETED  /  FAILED  /<br>SKIPPED  /  COMPENSATED|
|OperationAttempt|RUNNING  /  COMPLETED  /  FAILED  /  TIMED_OUT  /<br>CANCELLED|



Internal  /  Implementation Contract  /  24 Aug 2026 

Page 6 

||**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1**|
|---|---|
|**Owner**|**Canonical states**|
|AnalysisRun|CREATED  /  QUEUED  /  RUNNING  /  RETRYING  /  COMPLETED  /|
||FAILED  /  CANCELLED|
|Playback/Frame derivative|PENDING  /  GENERATING  /  READY  /  FAILED|
|InventoryItem|ACTIVE  /  MERGED  /  ARCHIVED|



###### **Projection only** 

Project Situation, Media Readiness, AnalysisWorkspaceReadiness and Hidden/Read/Operate/Manage are derived projections. They are not persisted as competing lifecycle state. 

##### **03.6 Domain Event Registry** 

**IAM:** InvitationIssued  /  InvitationResent  /  InvitationRevoked  /  InvitationAccepted  /  MembershipCreated  / MembershipRoleChanged  /  MembershipSuspended  /  MembershipReactivated  /  MembershipEnded  /  ProjectAccessGranted  / ProjectAccessChanged  /  ProjectAccessRevoked 

**Project/Content/Media:** ProjectCreated  /  ProjectActivated  /  ProjectArchived  /  ContentCreated  /  ContentVersionCreated  / AssetCreated  /  AssetFileUploaded  /  AssetFileValidationStarted  /  AssetFileValidated  /  AssetFileValidationFailed  / TechnicalAssetFileReplaced 

**Operations:** OperationCreated  /  OperationStarted  /  OperationWaiting  /  OperationCompleted  /  OperationFailed  / OperationCancelled  /  OperationCompensated 

**Analysis:** AnalysisRunCreated  /  AnalysisRunStarted  /  AnalysisRunCompleted  /  AnalysisRunFailed  /  AnalysisSnapshotCreated 

**Core/Editor:** InventoryItemCreated  /  InventoryItemChanged  /  InventoryItemsMerged  /  InventoryItemArchived  / AppearanceCreated  /  AppearanceAdjusted  /  AppearanceArchivedOrSuperseded  /  SceneCreated  /  SceneAdjusted  /  SceneSplit  / SceneMerged  /  DetectionCandidateAccepted  /  DetectionCandidateCorrectedAndAccepted  /  DetectionCandidateRejected 

**Validation/Evidence:** ValidationDecisionRecorded  /  EvidenceCreated 

**Catalog:** CanonicalLinkCreated  /  CanonicalLinkChanged 

```
event_id  /  event_type  /  event_version  /  occurred_at
actor_user_id?  /  acting_organization_id?  /  project_id?  /  content_version_id?
correlation_id  /  causation_id?  /  payload
```

##### **03.7 Error Registry** 

- Families: AUTHENTICATION  /  AUTHORIZATION  /  NOT_FOUND  /  VALIDATION  /  CONFLICT  /  LIFECYCLE  /  MEDIA /  OPERATION  /  ANALYSIS  /  ENTITLEMENT  /  INTERNAL. 

- HTTP baseline: 400 malformed; 401 unauthenticated; 403 safe forbidden; 404 no-existence-safe; 409 concurrency/conflict; 422 invalid domain transition; 429 rate limit; 503 transient dependency. 

```
ACTING_ORGANIZATION_REQUIRED
MEMBERSHIP_NOT_ACTIVE
PROJECT_ASSIGNMENT_REQUIRED
COLLABORATION_GRANT_REQUIRED
COLLABORATION_CEILING_EXCEEDED
CONTENT_VERSION_MISMATCH
CONTENT_VERSION_NOT_TEMPORALLY_READY
UPLOAD_SESSION_EXPIRED
UPLOAD_ORGANIZATION_MISMATCH
UPLOAD_SESSION_ALREADY_CLAIMED
MEDIA_NOT_READY / MEDIA_INVALID / MEDIA_SOURCE_INCONSISTENT / MEDIA_DURATION_INCONSISTENT
INVALID_TIME_RANGE / TIME_RANGE_OUT_OF_BOUNDS / EXACT_FRAME_MAPPING_UNAVAILABLE
OPERATION_NOT_RETRYABLE / OPERATION_NOT_CANCELLABLE
ANALYSIS_NOT_AVAILABLE
ENTITLEMENT_REQUIRED / USAGE_RIGHT_REQUIRED
```

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 7 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

#### **04. Canonical temporal model** 

###### **Temporal constitution** 

Every product-time fact means time on one exact ContentVersion. The canonical unit is integer milliseconds from a normalized 0 ms origin. 

```
TimecodeMs := non-negative safe integer
TimeRange := [start_ms, end_ms)
0 <= start_ms < end_ms <= ContentVersion.duration_ms
```

- No silent clamping; an out-of-bounds mutation is rejected. 

- Frame numbers are derived, zero-based and never the primary temporal key. 

- CFR mapping uses exact rational arithmetic; persisted FPS remains numerator/denominator. 

- VFR timecode remains valid but exact frame-number mapping requires real frame timing data; avg FPS is never exact truth. 

- SMPTE/drop-frame is display/mapping, not canonical storage. 

- Frontend playback uses a shared MediaClock returning normalized integer ms and explicit requested/presented seek results. 

- Automatic reconform across ContentVersions is forbidden in M0. 

#### **05. DEV-000 - Greenfield repository bootstrap** 

###### **Repository contract** 

The vNext M0 implementation is greenfield. The existing Laravel/MySQL platform is not modified as part of this execution pack. 

```
apps/
  web
  api
  worker
packages/
  contracts
  domain
  temporal
  authorization
  config
  observability
  testing
infrastructure/
docs/
```

|**Layer**|**Baseline**|
|---|---|
|Language / workspace|TypeScript  /pnpm monorepo|
|Web|Next.js|
|API|NestJS or equivalent modular TypeScript API runtime|
|Database|PostgreSQL + Prisma migrations|
|Async|Redis + BullMQ+ transactional outbox|
|Object storage|S3-compatible; MinIO local/test|
|Contracts|OpenAPI + typed internal contracts|
|Testing|unit + integration + Playwright +property/securitysuites|
|Operations|structured logs, correlation IDs, health/readinessprobes|
|CI|lint + typecheck + migrations + tests +pnpm verify|



- Use migrations for production evolution; no production schema push shortcut. 

- Validate all environment configuration at startup. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 8 

###### **IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

- Secrets are never committed and signed URLs are redacted from logs. 

- Modular monolith boundaries use explicit contracts even when no network transport exists. 

- ADR directory is created before first structural deviation. 

#### **06. Execution plan - Waves 0 to 11** 

|**Wave**|**Packets**|**Outcome**|**Depends**|
|---|---|---|---|
|0|DEV-000|Repositorybootstrap|None|
|1|M0-01|IAM|Wave 0|
|2|M0-02-001/002/006|Project + Content foundation|Wave 1|
|3|M0-02-003/004 + M0-03-001..003|Upload + source media +<br>temporal + secure source access|Wave 2|
|4|M0-04-001..007|Operations runtime|Wave 3|
|5|M0-03-004..007|Derivatives + readiness|Wave 4|
|6|M0-02-005/007/008/009|Quick Create|Waves 1-5|
|7|M0-05-001..007|Inventory + Catalog minimum +<br>Casting|Wave 6|
|8|M0-06-001..008|Scene + Appearance + Editor|Wave 7 + media readiness|
|9|M0-07-001..010|Analysis|Operations/media; Editor bridge<br>can beported|
|10|M0-08-001..006|Validation + Evidence|Waves 8-9|
|11|M0-09-001..009|Home/Projects/Overview/<br>Settings/Rights/Global<br>hardening|All prior|



###### **Important** 

Packet IDs preserve design lineage; execution order is dependency-driven. Operations is implemented before <u>Quick Create is fully assembled, even though Quick Create was designed earlier.</u> 

#### **07. Development packet index** 

|**Wave**|**Packet**|**Title**|**Depends on**|
|---|---|---|---|
|Wave 1 - IAM|M0-01-001|User / Organization /<br>OrganizationMembership<br>foundation|DEV-000|
|Wave 1 - IAM|M0-01-002|ActingOrganization|M0-01-001|
|Wave 1 - IAM|M0-01-003|Role Packages + CapabilityRegistry<br>|M0-01-001|
|Wave 1 - IAM|M0-01-004|Efective Access Resolver|M0-01-002, M0-01-003|
|Wave 1 - IAM|M0-01-005|Invitation lifecycle|M0-01-003, M0-01-004|
|Wave 1 - IAM|M0-01-006|ProjectAccessGrant foundation|M0-01-004|
|Wave 1 - IAM|M0-01-007|Collaboration Ceiling|M0-01-006|
|Wave 1 - IAM|M0-01-008|Authorized Navigation|M0-01-004, M0-01-007|
|Wave 1 - IAM|M0-01-009|Organization Team & Access read<br>models|M0-01-005, M0-01-008|
|Wave 1 - IAM|M0-01-010|IAM E2E hardening|M0-01-001..009|
|Wave 2 - Project & Content|M0-02-001|Project aggregate|Wave 1|
|Wave 2 - Project & Content|M0-02-002|Content + CV-001 foundation|M0-02-001|
|Wave 2 - Project & Content|M0-02-006|Project access bootstrap & initial<br>team|M0-02-001, M0-01-006/007|
|Wave 3 - Upload & Source Media|M0-02-003|Upload staging/ UploadSession|Wave 2|
|Wave 3 - Upload & Source Media|M0-02-004|Asset + AssetFile source lineage<br>|M0-02-003, M0-02-002|
|Wave 3 - Upload & Source Media|M0-03-001|Media technicalprofle hardening|M0-02-004|
|Wave 3 - Upload & Source Media|M0-03-002|Canonical temporal model|M0-03-001|
|Wave 3 - Upload & Source Media|M0-03-003|Secure source access|M0-03-001, M0-01-004|
|Wave 4 - Operations Runtime|M0-04-001|Operation aggregate|Wave 3|
|Wave 4 - Operations Runtime|M0-04-002|OperationStep/ OperationAttempt|M0-04-001|



Internal  /  Implementation Contract  /  24 Aug 2026 

Page 9 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

|**Wave**|**Packet**|**Title**|**Depends on**|
|---|---|---|---|
|Wave 4 - Operations Runtime<br>Wave 4 - Operations Runtime|M0-04-003<br>M0-04-004|OperationLog/ OperationResult<br>Transactional outbox / queue /<br>dispatch|M0-04-002<br>M0-04-003|
|Wave 4 - Operations Runtime|M0-04-005|Waiting / retry / timeout /<br>cancellation / compensation|M0-04-004|
|Wave 4 - Operations Runtime|M0-04-006|Progress / authorization / read<br>models|M0-04-005, M0-01-008|
|Wave 4 - Operations Runtime|M0-04-007|Operations E2E hardening|M0-04-001..006|
|Wave 5 - Media Derivatives &<br>Readiness|M0-03-004|Playback Proxy Derivative|Wave 4, M0-03-003|
|Wave 5 - Media Derivatives &<br>Readiness|M0-03-005|Frame / Thumbnail Extraction<br>Foundation|M0-03-002, M0-03-004|
|Wave 5 - Media Derivatives &<br>|M0-03-006|Media Readiness Projection|M0-03-004/005|
|Readiness||||
|Wave 5 - Media Derivatives &|M0-03-007|Audiovisual Readiness E2E|M0-03-001..006, Wave 4|
|Readiness||Hardening||
|Wave 6 - Quick Create|M0-02-005|Quick Create orchestration|Waves 1-5|
|Wave 6 - Quick Create|M0-02-007|NPW-01 Quick Create UI|M0-02-003, M0-02-005|
|Wave 6 - Quick Create|M0-02-008|NPW-02 Preparing / Initial Analysis<br>handof|M0-02-005, Wave 5|
|Wave 6 - Quick Create|M0-02-009|Quick Create E2E hardening|M0-02-003..008, Waves 1-5|
|Wave 7 - Inventory, Catalog &|M0-05-001|Inventory Type / Family registry|Wave 6|
|Casting||||
|Wave 7 - Inventory, Catalog &|M0-05-002|InventoryItem aggregate|M0-05-001|
|Casting||||
|Wave 7 - Inventory, Catalog &<br>|M0-05-003|CanonicalEntity / CanonicalLink<br>|M0-05-002|
|Casting||minimum||
|Wave 7 - Inventory, Catalog &<br>|M0-05-004|Person / Character / Cast minimum|M0-05-003|
|Casting||||
|Wave 7 - Inventory, Catalog &<br>Casting|M0-05-005|Inventory merge|M0-05-002|
|Wave 7 - Inventory, Catalog &|M0-05-006|Inventory read models|M0-05-002..005|
|Casting||||
|Wave 7 - Inventory, Catalog &|M0-05-007|Inventory / Catalog / Casting|M0-05-001..006|
|Casting||hardening||
|Wave 8 - Scene, Appearance &<br>|M0-06-001|Appearance aggregate|Wave 7, M0-03-002|
|Editor||||
|Wave 8 - Scene, Appearance &<br>|M0-06-002|Scene / SceneStructure|M0-06-001|
|Editor||||
|Wave 8 - Scene, Appearance &|M0-06-003|Editor Track projection|M0-06-001|
|Editor||||
|Wave 8 - Scene, Appearance &|M0-06-004|Manual authoring|M0-06-001..003, M0-03-006|
|Editor||||
|Wave 8 - Scene, Appearance &<br>Editor|M0-06-005|Split / merge / regroup / reassign|M0-06-004|
|Wave 8 - Scene, Appearance &|M0-06-006|AI candidate bridges|M0-06-004, Wave 9 Analysis|
|Editor|||contracts may initially be<br>stubbed/port-based|
|Wave 8 - Scene, Appearance &|M0-06-007|Editor workspace M0|M0-06-001..006, M0-03-006|
|<br>Editor||||
|Wave 8 - Scene, Appearance &|M0-06-008|Editor E2E hardening|M0-06-001..007|
|Editor||||
|Wave 9 - Analysis|M0-07-001|AnalysisRun aggregate|Waves 4-6|
|Wave 9 - Analysis|M0-07-002|DetectionCandidate normalization|M0-07-001, M0-03-002|
|Wave 9 - Analysis|M0-07-003|Candidate clustering / grouping<br>proposals|<br>M0-07-002|
|Wave 9 - Analysis|M0-07-004|Context Taxonomy|M0-07-002, M0-06-002|
|Wave 9 - Analysis|M0-07-005|Vertical Relevance|M0-07-004|
|Wave 9 - Analysis|M0-07-006|AnalysisSnapshot|M0-07-002..005|
|Wave 9 - Analysis|M0-07-007|Business Opportunities / Key|M0-07-005/006|
|||<br>Contexts||
|Wave 9 - Analsis|M0-07-008|Customer teaser vs authorized|M0-07-006/007 M0-09-005|
|y||<br>workspace|,<br>contract maybeport initially|



Internal  /  Implementation Contract  /  24 Aug 2026 

Page 10 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

|**Wave**|**Packet**|**Title**|**Depends on**|
|---|---|---|---|
|Wave 9 - Analysis|M0-07-009|Analysis UIprojections|M0-07-006..008, Wave 8|
|Wave 9 - Analysis|M0-07-010|Analysis E2E hardening|M0-07-001..009|
|Wave 10 - Validation & Evidence|M0-08-001|ValidationDecision|Wave 8/9|
|Wave 10 - Validation & Evidence|M0-08-002|Validation stateprojection|M0-08-001|
|Wave 10 - Validation & Evidence|M0-08-003|Evidence aggregate|M0-08-001, M0-03-005|
|Wave 10 - Validation & Evidence|M0-08-004|Evidence artifactgeneration|M0-08-003, M0-03-002|
|Wave 10 - Validation & Evidence|M0-08-005|Review integration|M0-08-001..004|
|Wave 10 - Validation & Evidence|M0-08-006|Validation / Evidence hardening|M0-08-001..005|
|Wave 11 - Product Integration|M0-09-001|Home M0|Waves 1-10|
|Wave 11 - Product Integration|M0-09-002|Projects / PRJ-01|M0-09-001|
|Wave 11 - Product Integration|M0-09-003|Project Overview / PRO-01|M0-09-001/002|
|Wave 11 - Product Integration|M0-09-004|Project Settings SET-01 / SET-02 /<br>SET-07 basic|M0-09-003, Wave 4|
|Wave 11 - Product Integration|M0-09-005|Entitlement model + M0guards|Wave 1, Analysis teaser/workspace|
|Wave 11 - Product Integration|M0-09-006|Publication / Export minimum<br>contracts|M0-09-005|
|Wave 11 - Product Integration|M0-09-007|Security / observability / recovery<br>baseline|All prior waves|
|Wave 11 - Product Integration|M0-09-008|Deployment & environment<br>contract|DEV-000, M0-09-007|
|Wave 11 - Product Integration|M0-09-009|Global M0 E2E freeze|Allpriorpackets|



#### **08. Wave 1 - IAM** 

|**Packet**|**Outcome**|
|---|---|
|M0-01-001|User / Organization / OrganizationMembershipfoundation|
|M0-01-002|ActingOrganization|
|M0-01-003|Role Packages + CapabilityRegistry<br>|
|M0-01-004|Efective Access Resolver|
|M0-01-005|Invitation lifecycle|
|M0-01-006|ProjectAccessGrant foundation|
|M0-01-007|Collaboration Ceiling|
|M0-01-008|Authorized Navigation|
|M0-01-009|Organization Team & Access read models|
|M0-01-010|IAM E2E hardening|



##### **M0-01-001 - User / Organization / OrganizationMembership foundation** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 1 - IAM|
|Gate|M0|
|Depends on|DEV-000|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Establish global identity, organizations and organization-scoped membership without placing organization, role or project authority directly on User. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 11 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Implementation contract** 

- Create User as global identity and Organization as tenant/business actor. 

- Create OrganizationMembership as the only User<->Organization relationship that can carry organization-level role package version and membership lifecycle. 

- Normalize/uniquely constrain identity fields; membership lifecycle must support ACTIVE/SUSPENDED/ENDED and temporal validity. 

- Do not create User.current_organization_id, User.role or implicit project access. 

###### **Canonical model / invariants** 

```
User
Organization
```

```
OrganizationMembership(user_id, organization_id, role_package_version_id, lifecycle, valid_from,
valid_until?)
```

###### **Commands, queries, APIs and events** 

- Queries for current User memberships; membership mutations remain administrative commands. 

- Emit MembershipCreated/Changed/Suspended/Reactivated/Ended. 

###### **Authorization and visibility** 

- Membership administration requires organization.members.manage; read requires organization.members.view. 

###### **Mandatory verification** 

- One User may have multiple independent memberships. 

- Suspended/expired membership provides no active authority. 

- No organization data is discoverable through a membership in another Organization. 

###### **Definition of Done** 

- Schema/migrations and invariants implemented. 

- Repository seeds include representative multi-org users. 

- No role or current organization field exists on User. 

- pnpm verify passes. 

###### **Codex execution prompt** 

```
Implement M0-01-001 User / Organization / OrganizationMembership foundation exactly as specified in
the M0 Execution Pack. Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-01-002 - Acting Organization** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 1 - IAM|
|Gate|M0|
|Depends on|M0-01-001|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Make the organizational context explicit per session/request and prevent authority from being silently unioned across memberships. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 12 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Implementation contract** 

- Acting Organization is session/request-scoped; never persisted on User. 

- 0 active memberships -> no context; 1 may auto-select; >1 requires explicit choice. 

- Switching context invalidates organization/project/navigation/count/read-model caches. 

- Deep links never auto-switch organizations. 

###### **Canonical model / invariants** 

```
RequestContext { user_id, acting_organization_id }
acting_organization_id must resolve to an ACTIVE temporally-valid OrganizationMembership
```

###### **Commands, queries, APIs and events** 

- GET /me/context 

- POST /me/acting-organization 

###### **Authorization and visibility** 

- Only organizations with an active membership may be selected. 

###### **Mandatory verification** 

- Multi-org user never receives union authority. 

- Switching A->B immediately changes authorized portfolio. 

- Deep link into A while acting as B is denied without auto-switch. 

###### **Definition of Done** 

- Context middleware implemented. 

- All later authorization depends on context. 

- pnpm verify passes. 

###### **Codex execution prompt** 

```
Implement M0-01-002 Acting Organization exactly as specified in the M0 Execution Pack. Preserve all
architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-01-003 - Role Packages + Capability Registry** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 1 - IAM|
|Gate|M0|
|Depends on|M0-01-001|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Implement immutable standard role package versions as capability presets while keeping capability codes as the executable authorization language. 

###### **Implementation contract** 

- Load the canonical Capability Registry from code/config with scope and delegability metadata. 

- Seed standard RolePackageVersion v1 packages. 

- RolePackageVersion is immutable after publication. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 13 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

- Authorization code may not switch on role names. 

###### **Commands, queries, APIs and events** 

- Queries to inspect a membership role package and resolved capabilities. 

- Role package changes are explicit membership changes and audited. 

###### **Authorization and visibility** 

- organization.members.manage required to assign permitted standard packages; INTERNAL_ADMIN remains control-plane governed. 

###### **Mandatory verification** 

- Changing role package changes potential capabilities but not Project assignments. 

- Old memberships remain on old version after a v2 package is introduced. 

- No role-name authorization paths found by drift scan. 

###### **Definition of Done** 

- Registry machine-readable and tested. 

- Standard packages versioned and seeded. 

- Drift test forbids role-name checks. 

###### **Codex execution prompt** 

```
Implement M0-01-003 Role Packages + Capability Registry exactly as specified in the M0 Execution
Pack. Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-01-004 - Effective Access Resolver** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 1 - IAM|
|Gate|M0|
|Depends on|M0-01-002, M0-01-003|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Create one canonical backend resolver that decides discoverability and action authorization by intersection, deny-by-default. 

###### **Implementation contract** 

- Pipeline includes authentication, Acting Organization, active Membership, Role capabilities, ProjectAccessGrant, collaboration ceiling, resource state, Data Visibility, domain policy and entitlement when applicable. 

- Return machine-readable reason codes for internal diagnostics without leaking existence to unauthorized customers. 

- No module creates a competing permission engine. 

###### **Canonical model / invariants** 

```
EffectiveAccessDecision { allowed, effective_capabilities, ux_level?, reasons[], visibility_scope }
```

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 14 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Commands, queries, APIs and events** 

- Authorization service/guard used by APIs, navigation, search, counts and read model composition. 

###### **Authorization and visibility** 

- Resolver itself is infrastructure; results are always resource-specific. 

###### **Mandatory verification** 

- Property tests for intersection/no-union semantics. 

- Denied resources never leak through secondary projections. 

- Direct API bypass receives same result as UI path. 

###### **Definition of Done** 

- All protected endpoints can consume resolver. 

- No role shortcut. 

- Deny-by-default proven. 

###### **Codex execution prompt** 

```
Implement M0-01-004 Effective Access Resolver exactly as specified in the M0 Execution Pack.
Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-01-005 - Invitation lifecycle** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 1 - IAM|
|Gate|M0|
|Depends on|M0-01-003, M0-01-004|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Create secure pre-membership invitations that confer no authority until accepted. 

###### **Implementation contract** 

- Invitation lifecycle: PENDING -> ACCEPTED | EXPIRED | REVOKED. 

- Generate secure random token; persist hash only. 

- Verified authenticated email must match invited normalized email. 

- Acceptance atomically creates OrganizationMembership using the invited RolePackageVersion. 

- Duplicate pending invitation per Organization+email blocked; resend does not silently extend expiry. 

###### **Commands, queries, APIs and events** 

- Issue/resend/revoke/accept invitation commands. 

- Outbox email delivery. 

###### **Authorization and visibility** 

- organization.invitations.manage to issue/resend/revoke. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 15 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Mandatory verification** 

- Token replay fails after acceptance/revoke/expiry. 

- Wrong authenticated email cannot accept. 

- No capability exists before acceptance. 

###### **Definition of Done** 

- Secure token handling. 

- Atomic acceptance. 

- Audit/outbox. 

###### **Codex execution prompt** 

```
Implement M0-01-005 Invitation lifecycle exactly as specified in the M0 Execution Pack. Preserve all
architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-01-006 - ProjectAccessGrant foundation** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 1 - IAM|
|Gate|M0|
|Depends on|M0-01-004|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Establish the sole Project-scoped authority model for owner members and collaborator organizations/memberships. 

###### **Implementation contract** 

- Target is exactly one of Organization or OrganizationMembership, never User. 

- Lifecycle ACTIVE/REVOKED plus temporal validity. 

- Owner Organization does not need an organization collaboration grant, but ordinary owner members need individual assignment. 

- Collaborator ordinary access requires an organization grant and a member assignment. 

- Organization Admin administrative recovery is a policy, not an implicit business grant. 

###### **Canonical model / invariants** 

```
ProjectAccessGrant(project_id, target_organization_id XOR target_membership_id, capability_scope,
lifecycle, valid_from, valid_until?)
```

###### **Commands, queries, APIs and events** 

- Grant/revoke/list Project access commands and queries. 

- Emit ProjectAccessGranted/Changed/Revoked. 

###### **Authorization and visibility** 

- project.access.manage governs collaboration ceiling; project.assignments.manage governs individual assignments under allowed policy. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 16 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Mandatory verification** 

- No ProjectMember entity. 

- Same-org membership alone does not grant project business access. 

- Cross-project/cross-org grants cannot be confused. 

###### **Definition of Done** 

- Single authority model implemented. 

- SET-02/IAM can both project it later. 

###### **Codex execution prompt** 

```
Implement M0-01-006 ProjectAccessGrant foundation exactly as specified in the M0 Execution Pack.
Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-01-007 - Collaboration Ceiling** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 1 - IAM|
|Gate|M0|
|Depends on|M0-01-006|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Make organization collaboration grants capability ceilings and individual member assignments strict subsets. 

###### **Implementation contract** 

- Capability metadata distinguishes PROJECT_COLLABORATION delegable vs NOT_DELEGABLE. 

- Collaborator effective capabilities = role & org ceiling & individual assignment & policies. 

- Ceiling expansion does not expand individual assignment. 

- Ceiling reduction immediately narrows effective access. 

- Revoked parent cannot be bypassed by retaining or reattaching historical child assignments. 

###### **Commands, queries, APIs and events** 

- Commands to grant/change/revoke organization ceiling and collaborator individual assignment. 

###### **Authorization and visibility** 

- Collaborator admins may manage members only within their own organization and within the fixed ceiling when policy grants project.assignments.manage. 

###### **Mandatory verification** 

- Attempted self-elevation above ceiling fails. 

- Historical child scope retained but ineffective after ceiling reduction. 

- New parent grant does not revive old child automatically. 

###### **Definition of Done** 

- Delegability registry active. 

- Ceiling invariants enforced in DB/application. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 17 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

- Property tests green. 

###### **Codex execution prompt** 

```
Implement M0-01-007 Collaboration Ceiling exactly as specified in the M0 Execution Pack. Preserve
all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-01-008 - Authorized Navigation** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 1 - IAM|
|Gate|M0|
|Depends on|M0-01-004, M0-01-007|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Generate navigation server-side from effective access and need-to-know rather than static role-based menus. 

###### **Implementation contract** 

- UX levels Hidden/Read/Operate/Manage are derived projection only. 

- HIDDEN surface is not serialized. 

- M0 global surfaces: Home, Projects; organization Team & Access; project Overview, Content, Editor, Analysis, Settings as authorized. 

- Counts/badges/search/activity reuse the same authorized universe. 

###### **Commands, queries, APIs and events** 

- GET authorized navigation/bootstrap projection. 

###### **Authorization and visibility** 

- Every route still reauthorizes; navigation does not become a permission token. 

###### **Mandatory verification** 

- Clearance-only or vertical-specific users do not discover unrelated modules. 

- Deep link to hidden surface denied no-existence-safely. 

- No static role sidebar switch. 

###### **Definition of Done** 

- Server-authorized nav integrated with web shell. 

- Need-to-know leakage suite green. 

###### **Codex execution prompt** 

```
Implement M0-01-008 Authorized Navigation exactly as specified in the M0 Execution Pack. Preserve
all architecture invariants, add tests, and finish with pnpm verify.
```

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 18 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

##### **M0-01-009 - Organization Team & Access read models** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 1 - IAM|
|Gate|M0|
|Depends on|M0-01-005, M0-01-008|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Build IAM customer read models from Membership, Invitation and ProjectAccessGrant without introducing Client or ProjectMember duplicate entities. 

###### **Implementation contract** 

- IAM-01: Members, Invitations, Client Organizations. 

- IAM-02: member overview, Projects & Access, Activity. 

- IAM-03: invite member. 

- Client Organizations is a projection of Projects owned by other organizations accessed through collaboration grants. 

- Assigned vs effective capabilities must be distinguishable. 

###### **Commands, queries, APIs and events** 

- Organization Team & Access read endpoints; invite UI contracts. 

###### **Authorization and visibility** 

- Authorization-before-composition; no counts from hidden Projects. 

###### **Mandatory verification** 

- Multi-studio agency member correctly shows multiple client organizations without membership in those clients. 

- SET-02 later returns the same underlying grants. 

- Suspension/revoke reflected immediately. 

###### **Definition of Done** 

- No new Client/ProjectMember authority tables. 

- Read models and UI contracts green. 

###### **Codex execution prompt** 

```
Implement M0-01-009 Organization Team & Access read models exactly as specified in the M0 Execution
Pack. Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-01-010 - IAM E2E hardening** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 1 - IAM|
|Gate|M0|
|Depends on|M0-01-001..009|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



Internal  /  Implementation Contract  /  24 Aug 2026 

Page 19 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Objective** 

Prove organizational isolation, least privilege, collaboration ceilings, need-to-know and lifecycle revocation before any business module depends on IAM. 

###### **Implementation contract** 

- Exercise multi-organization, multi-client agency, ceiling reduction/revoke/regrant, suspension, invitation and direct API bypass. 

- Include concurrency/time-validity and property tests. 

- Validate IAM<->future SET consistency contract. 

###### **Commands, queries, APIs and events** 

- Security test suite + Playwright journeys. 

###### **Authorization and visibility** 

- This packet validates all IAM authorization paths. 

###### **Mandatory verification** 

- Gate 1 Organizational isolation PASS. 

- Gate 2 Project least privilege PASS. 

- Gate 3 Collaboration ceiling PASS. 

- Gate 4 Need-to-know PASS. 

- Gate 5 Lifecycle revocation PASS. 

###### **Definition of Done** 

- Zero IAM P0. 

- Architecture drift scan clean. 

- Wave 1 report READY. 

- pnpm verify green. 

###### **Codex execution prompt** 

```
Implement M0-01-010 IAM E2E hardening exactly as specified in the M0 Execution Pack. Preserve all
architecture invariants, add tests, and finish with pnpm verify.
```

#### **09. Wave 2 - Project & Content** 

|**Packet**|**Outcome**|
|---|---|
|M0-02-001|Project aggregate|
|M0-02-002|Content + CV-001 foundation|
|M0-02-006|Project access bootstrap& initial team|



##### **M0-02-001 - Project aggregate** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 2 - Project & Content|
|Gate|M0|
|Depends on|Wave 1|



Internal  /  Implementation Contract  /  24 Aug 2026 

Page 20 

||**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1**|
|---|---|
|**Field**|**Value**|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Create the minimal Project aggregate owned by an Organization and free of derived business/media/licensing state. 

###### **Implementation contract** 

- Fields: id, owner_organization_id, title, lifecycle, condition, creation_mode, created_by_user_id, timestamps. 

- Owner is derived from validated Acting Organization on creation; never trusted from request body. 

- Lifecycle starts DRAFT; condition NORMAL. 

- Archive preserves history and is not delete. 

- Do not add current ContentVersion, license, analysis or media shortcuts. 

###### **Commands, queries, APIs and events** 

- CreateProject, ArchiveProject (sensitive archive ultimately coordinated via Operation where applicable). 

- ProjectCreated/ProjectArchived. 

###### **Authorization and visibility** 

- project.create in Acting Organization; project.view/manage/archive later requires effective Project access/policy. 

###### **Mandatory verification** 

- Owner spoofing request ignored/rejected. 

- Project created in correct Acting Organization. 

- No same-org implicit project access. 

###### **Definition of Done** 

- Minimal schema. 

- Owner invariant. 

- No God Aggregate shortcuts. 

###### **Codex execution prompt** 

```
Implement M0-02-001 Project aggregate exactly as specified in the M0 Execution Pack. Preserve all
architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-02-002 - Content + CV-001 foundation** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 2 - Project & Content|
|Gate|M0|
|Depends on|M0-02-001|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



Internal  /  Implementation Contract  /  24 Aug 2026 

Page 21 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Objective** 

Establish Project->Content->ContentVersion as the only editorial/version chain and create CV-001 deterministically for initial content. 

###### **Implementation contract** 

- M0 supports one primary Content per Project as an explicit release simplification. 

- ContentVersion ordinal 1 and derived label CV-001. 

- Initial ContentVersion lifecycle DRAFT; duration null until media validation. 

- No current_content_version_id authority. 

- All future temporal facts must name exact ContentVersion. 

###### **Canonical model / invariants** 

```
Project 1-1 Content (M0 simplification)
Content 1-N ContentVersion
ContentVersion { ordinal, lifecycle, duration_ms? }
```

###### **Commands, queries, APIs and events** 

- CreateInitialContentVersion internally during Quick Create. 

- ContentCreated/ContentVersionCreated. 

###### **Authorization and visibility** 

- Project effective access + content capabilities for subsequent content commands. 

###### **Mandatory verification** 

- Duplicate CV-001 impossible. 

- Version ID explicit in temporal contracts. 

- No implicit latest resolver inside mutation path. 

###### **Definition of Done** 

- Chain and constraints implemented. 

- Temporal package can bind to ContentVersion. 

###### **Codex execution prompt** 

```
Implement M0-02-002 Content + CV-001 foundation exactly as specified in the M0 Execution Pack.
Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-02-006 - Project access bootstrap & initial team** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 2 - Project & Content|
|Gate|M0|
|Depends on|M0-02-001, M0-01-006/007|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Bootstrap creator Project access using the canonical ProjectAccessGrant model without creator or same-org shortcuts. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 22 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Implementation contract** 

- Creator receives an explicit membership-target ProjectAccessGrant. 

- Assigned scope = current role capabilities & project-delegable baseline. 

- Project Manager baseline can manage own-org project assignments within policy. 

- Organization Admin administrative discovery/recovery does not imply arbitrary business commands. 

###### **Commands, queries, APIs and events** 

- BootstrapProjectAccess internal command. 

###### **Authorization and visibility** 

- All subsequent business access resolves via EffectiveAccessResolver, not created_by. 

###### **Mandatory verification** 

- Removing creator assignment removes ordinary business access. 

- Another owner-org member receives no access until assigned. 

- Project Manager cannot create collaborator ceiling unless capability/policy permits. 

###### **Definition of Done** 

- Creator is historical provenance only. 

- SET-02/IAM projection compatibility maintained. 

###### **Codex execution prompt** 

```
Implement M0-02-006 Project access bootstrap & initial team exactly as specified in the M0 Execution
Pack. Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

#### **10. Wave 3 - Upload & Source Media** 

|**Packet**|**Outcome**|
|---|---|
|M0-02-003|Upload staging/ UploadSession|
|M0-02-004|Asset + AssetFile source lineage<br>|
|M0-03-001|Media technicalprofle hardening|
|M0-03-002|Canonical temporal model|
|M0-03-003|Secure source access|



##### **M0-02-003 - Upload staging / UploadSession** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 3 - Upload & Source Media|
|Gate|M0|
|Depends on|Wave 2|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Support direct private audiovisual upload before Project creation while immutably binding the staged object to the creator and Acting Organization. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 23 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Implementation contract** 

- UploadSession states PENDING/UPLOADED/CLAIMED/EXPIRED/INVALID. 

- Direct-to-private S3-compatible storage; API never proxies video bytes. 

- Support single PUT or multipart; server verifies object before UPLOADED. 

- UploadSession creates no Project/Content/Asset. 

- M0 claim only by creator; organization switch never silently reassigns upload. 

- Cleanup expired/unclaimed objects. 

###### **Commands, queries, APIs and events** 

- CreateUploadSession, Complete/VerifyUploadSession, multipart helpers. 

###### **Authorization and visibility** 

- asset.upload capability in Acting Organization; session bound to current active membership context. 

###### **Mandatory verification** 

- Org A upload cannot be claimed while acting as B. 

- Lost response/retry is idempotent. 

- Expired session cannot claim. 

- Private storage only. 

###### **Definition of Done** 

- Direct upload works. 

- Cleanup job/reconciliation. 

- No media bytes through API. 

###### **Codex execution prompt** 

```
Implement M0-02-003 Upload staging / UploadSession exactly as specified in the M0 Execution Pack.
Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-02-004 - Asset + AssetFile source lineage** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 3 - Upload & Source Media|
|Gate|M0|
|Depends on|M0-02-003, M0-02-002|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Model logical audiovisual Asset separately from immutable physical AssetFile attempts and preserve source lineage through technical replacement. 

###### **Implementation contract** 

- ContentVersion->Asset->AssetFile. 

- M0 primary audiovisual Asset role PRIMARY. 

- AssetFile technical state UPLOADED->VALIDATING->VALID/INVALID. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 24 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

- 

- 

- 

- AssetFile lifecycle ACTIVE/SUPERSEDED; exactly one ACTIVE per source Asset. 

- Source object immutable. 

- Pre-analysis technical correction creates a new AssetFile and supersedes old; never overwrites bytes. 

###### **Commands, queries, APIs and events** 

- AddAsset, Upload/ClaimAssetFile, ValidateAssetFile, ReplaceTechnicalAssetFile. 

- AssetCreated/AssetFileUploaded/Validated/ValidationFailed/TechnicalAssetFileReplaced. 

###### **Authorization and visibility** 

- asset.manage for source replacement; machine validation is system-owned. 

###### **Mandatory verification** 

- Exactly one ACTIVE file. 

- Old bytes/checksum unchanged after replacement. 

- Replacement shortcut prohibited after successful analysis truth. 

###### **Definition of Done** 

- Lineage implemented. 

- Source immutability enforced. 

- No file overwrite path. 

###### **Codex execution prompt** 

```
Implement M0-02-004 Asset + AssetFile source lineage exactly as specified in the M0 Execution Pack.
Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-03-001 - Media technical profile hardening** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 3 - Upload & Source Media|
|Gate|M0|
|Depends on|M0-02-004|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Persist normalized, reproducible technical inspection results separately from AssetFile lifecycle and distinguish invalid media from infrastructure failure. 

###### **Implementation contract** 

- Create 1:1 MediaTechnicalProfile for validated source attempt. 

- Persist duration, container, stream counts, primary video stream, dimensions, rotation, codecs, rational frame/timebase data, source start, VFR, audio and color/HDR metadata, tool/profile version. 

- Separate warnings from validation errors; no-audio and VFR are allowed warnings when otherwise valid. 

- FFprobe adapter normalizes raw provider/tool output; raw JSON is not domain truth. 

- ContentVersion duration update + profile + AssetFile VALID must be atomic. 

###### **Commands, queries, APIs and events** 

- MediaInspectorPort/FFprobe adapter. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 25 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Authorization and visibility** 

- Worker uses machine storage access, not human signed URLs. 

###### **Mandatory verification** 

- Rational 24000/1001 preserved. 

- Infra failure does not mark file INVALID. 

- Material duration inconsistency fails validation explicitly. 

###### **Definition of Done** 

- Normalized profile persisted. 

- Warnings model present. 

- Atomic validation completion. 

###### **Codex execution prompt** 

```
Implement M0-03-001 Media technical profile hardening exactly as specified in the M0 Execution Pack.
Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-03-002 - Canonical temporal model** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 3 - Upload & Source Media|
|Gate|M0|
|Depends on|M0-03-001|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Provide one shared temporal package for all later Analysis/Editor/Evidence code. 

###### **Implementation contract** 

- Branded TimecodeMs/DurationMs non-negative integers. 

- Half-open TimeRange [start,end). 

- TemporalPoint requires 0<=t<duration. 

- No silent clamping. 

- CFR uses rational arithmetic. 

- VFR exact frame number unavailable without real frame timing index. 

- Shared frontend MediaClock returns normalized integer ms and requested/presented seek results. 

###### **Commands, queries, APIs and events** 

- Pure temporal package: validate/contains/overlap/intersection/touches/rational mapping. 

###### **Authorization and visibility** 

- Pure domain package; no authorization. 

###### **Mandatory verification** 

- Boundary/property tests. 

- Touching intervals are not overlap. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 26 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

- VFR avg fps cannot satisfy exact frame lookup. 

###### **Definition of Done** 

- One temporal package consumed by backend/frontend. 

- No float-seconds persistence. 

###### **Codex execution prompt** 

```
Implement M0-03-002 Canonical temporal model exactly as specified in the M0 Execution Pack. Preserve
all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-03-003 - Secure source access** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 3 - Upload & Source Media|
|Gate|M0|
|Depends on|M0-03-001, M0-01-004|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Provide short-lived authorized workspace access to the exact active source without turning a signed storage URL into domain authority. 

###### **Implementation contract** 

- POST /content-versions/:id/media-access resolves exact CV->primary Asset->ACTIVE VALID AssetFile. 

- Require authenticated user, valid Acting Organization, effective Project access, asset.view and Data Visibility. 

- Issue short TTL signed read URL; recommended M0 default 300s and renew before expiry. 

- Do not expose bucket/key/provider or use AssetFile ID possession as authority. 

- Range/CORS/CSP/referrer policies support browser playback and prevent URL leakage. 

- Document bounded residual access: already-issued presigned URL may remain usable until TTL after IAM revocation. 

- Workers use machine credentials, never human URLs. 

###### **Commands, queries, APIs and events** 

- POST /content-versions/:contentVersionId/media-access. 

###### **Authorization and visibility** 

- Reauthorize every issuance/renewal. 

- Ordinary access resolves only current ACTIVE source. 

###### **Mandatory verification** 

- Unauthorized guessed CV does not reveal existence. 

- Revocation blocks renewal immediately. 

- Range seek works; URL absent from logs/analytics. 

###### **Definition of Done** 

- Private source access implemented and security-tested. 

- Workspace source access kept separate from future serving plane. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 27 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Codex execution prompt** 

```
Implement M0-03-003 Secure source access exactly as specified in the M0 Execution Pack. Preserve all
architecture invariants, add tests, and finish with pnpm verify.
```

#### **11. Wave 4 - Operations Runtime** 

|**Packet**|**Outcome**|
|---|---|
|M0-04-001|Operation aggregate|
|M0-04-002|OperationStep/ OperationAttempt|
|M0-04-003|OperationLog/ OperationResult|
|M0-04-004|Transactional outbox /queue / dispatch|
|M0-04-005|Waiting/ retry/ timeout / cancellation / compensation|
|M0-04-006|Progress / authorization / read models|
|M0-04-007|Operations E2E hardening|



##### **M0-04-001 - Operation aggregate** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 4 - Operations Runtime|
|Gate|M0|
|Depends on|Wave 3|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Implement persistent Operation as the durable coordinator for long-running or multi-aggregate work, separate from domain result state. 

###### **Implementation contract** 

- Operation canonical states: PENDING/RUNNING/WAITING/COMPLETED/FAILED/CANCELLED/COMPENSATED. 

- Persist owner_organization_id, optional project/content-version references, requested_by_user_id provenance, type, correlation and timestamps. 

- Operation target references use registered types only. 

- Terminal completion does not become mutable back to running. 

- Initial OperationTypes include ASSET_VALIDATION, PLAYBACK_PROXY_GENERATION, INITIAL_ANALYSIS. 

###### **Canonical model / invariants** 

```
Operation { id, type, owner_organization_id, project_id?, content_version_id?, target_type?,
target_id?, state, requested_by_user_id?, correlation_id, timestamps }
```

###### **Commands, queries, APIs and events** 

- Create/start/wait/complete/fail/cancel/compensate application contracts. 

- OperationCreated/Started/Waiting/Completed/Failed/Cancelled/Compensated. 

###### **Authorization and visibility** 

- Operation visibility later depends on current target authorization, never requester identity. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 28 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Mandatory verification** 

- Owner organization/target consistency. 

- Terminal-state invariants. 

- Operation and domain result can diverge safely. 

###### **Definition of Done** 

- Schema and lifecycle state machine. 

- No Project.processing_status. 

###### **Codex execution prompt** 

```
Implement M0-04-001 Operation aggregate exactly as specified in the M0 Execution Pack. Preserve all
architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-04-002 - OperationStep / OperationAttempt** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 4 - Operations Runtime|
|Gate|M0|
|Depends on|M0-04-001|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Represent logical workflow steps separately from execution attempts so retry never erases operational history. 

###### **Implementation contract** 

- OperationStep defines ordinal/code/state plus precondition, action contract, expected result, retry policy reference, compensation policy and timeout. 

- OperationAttempt is append-only per attempt_number and records worker/execution outcome. 

- Attempt failure does not fail Operation while policy permits retry. 

- Unique operation+ordinal and step+attempt_number. 

###### **Canonical model / invariants** 

```
Operation
 └─ OperationStep
     └─ OperationAttempt
```

###### **Commands, queries, APIs and events** 

- Worker attempt start/finish contracts. 

###### **Authorization and visibility** 

- Internal execution; customer read model is filtered later. 

###### **Mandatory verification** 

- Retry creates new Attempt. 

- Step completion idempotent. 

- Worker crash recovery does not duplicate logical effect. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 29 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Definition of Done** 

- Persistent step/attempt history. 

- Retry semantics tested. 

###### **Codex execution prompt** 

```
Implement M0-04-002 OperationStep / OperationAttempt exactly as specified in the M0 Execution Pack.
Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-04-003 - OperationLog / OperationResult** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 4 - Operations Runtime|
|Gate|M0|
|Depends on|M0-04-002|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Complete the normative Operation structure with safe observability logs and owner-routed result references without duplicating business aggregates. 

###### **Implementation contract** 

- OperationLog stores structured execution observations safe for diagnostics; secrets/raw signed URLs prohibited. 

- OperationResult stores typed references/outcome metadata, not entire Analysis/Asset domain payloads. 

- Customer-safe and internal-diagnostic projections are distinct. 

- Result ownership follows domain owner; Operation never becomes a generic JSON database. 

###### **Commands, queries, APIs and events** 

- AppendOperationLog internal port; SetOperationResult. 

###### **Authorization and visibility** 

- Full logs are internal/capability-restricted; customer projection exposes only safe failure/progress. 

###### **Mandatory verification** 

- Sensitive URL/secrets redaction. 

- Result reference resolves exact domain aggregate. 

- No domain state reconstructed from arbitrary operation JSON. 

###### **Definition of Done** 

- Normative five-part Operation structure complete. 

- Safe log policy enforced. 

###### **Codex execution prompt** 

```
Implement M0-04-003 OperationLog / OperationResult exactly as specified in the M0 Execution Pack.
Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 30 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

**M0-04-004 - Transactional outbox / queue / dispatch** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 4 - Operations Runtime|
|Gate|M0|
|Depends on|M0-04-003|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Guarantee durable work dispatch under at-least-once transport semantics. 

###### **Implementation contract** 

- DB mutation and OutboxMessage commit in one transaction. 

- Dispatcher publishes after commit to BullMQ/Redis. 

- Workers receive IDs/correlation and reload authoritative state from DB. 

- Reconciliation can re-enqueue persistent pending work after queue loss. 

- Business effects must be idempotent; exactly-once transport is not assumed. 

###### **Commands, queries, APIs and events** 

- Outbox dispatcher, worker envelope, reconciliation job. 

###### **Authorization and visibility** 

- Infrastructure only; no human queue payload authority. 

###### **Mandatory verification** 

- DB commit + Redis loss recovers. 

- Duplicate queue delivery produces one business effect. 

- API/worker/Redis restarts safe. 

###### **Definition of Done** 

- At-least-once semantics documented and tested. 

- Outbox/reconciliation operational. 

###### **Codex execution prompt** 

```
Implement M0-04-004 Transactional outbox / queue / dispatch exactly as specified in the M0 Execution
Pack. Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-04-005 - Waiting / retry / timeout / cancellation / compensation** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 4 - Operations Runtime|
|Gate|M0|
|Depends on|M0-04-004|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



Internal  /  Implementation Contract  /  24 Aug 2026 

Page 31 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Objective** 

Implement the complete process-control semantics required by the Guía without confusing infrastructure failures with domain invalidity. 

###### **Implementation contract** 

- Failure classes TRANSIENT/PERMANENT/BUSINESS. 

- Versioned RetryPolicy per Operation/Step type; max attempts, backoff and timeout. 

- WAITING represents a genuine external/precondition wait, not a fake progress state. 

- Cancellation records reached point; does not delete. 

- Compensation runs explicit compensating actions where transactional rollback is impossible. 

- A timeout ends an Attempt; policy decides retry/final failure. 

###### **Commands, queries, APIs and events** 

- RetryOperation/Step, CancelOperation, compensation coordinator. 

###### **Authorization and visibility** 

- operations.retry/cancel plus target-domain authority/policy. 

###### **Mandatory verification** 

- Transient retry succeeds. 

- Retry exhaustion fails correctly. 

- Cancel/compensation preserves history. 

- Worker timeout never marks media invalid by itself. 

###### **Definition of Done** 

- All normative operation states reachable through governed transitions. 

- No retry erases history. 

###### **Codex execution prompt** 

```
Implement M0-04-005 Waiting / retry / timeout / cancellation / compensation exactly as specified in
the M0 Execution Pack. Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-04-006 - Progress / authorization / read models** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 4 - Operations Runtime|
|Gate|M0|
|Depends on|M0-04-005, M0-01-008|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Expose honest progress and owner-routed operation awareness only inside the actor’s currently authorized universe. 

###### **Implementation contract** 

- Progress derives from real completed/total/current steps; percent only when meaningful/verified. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 32 

###### **IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

- Home operation projection: reference/type, optional Project, canonical state, current step, verified progress, timestamps, authorized route. 

- 

- 

- Full logs/history/retry/cancel remain Operations-level/internal as authorized. 

- Losing Project access removes the Operation from customer projections while work continues. 

###### **Commands, queries, APIs and events** 

- OperationSummary queries; target-relative authorization adapter. 

###### **Authorization and visibility** 

- Authorization-before-composition; no hidden product/module Operation leakage. 

###### **Mandatory verification** 

- Creator loses access: operation continues but disappears. 

- Hidden vertical operation does not affect counts. 

- No fake percentage. 

###### **Definition of Done** 

- Safe read models reusable by NPW/Home/Overview. 

- Need-to-know tests green. 

###### **Codex execution prompt** 

```
Implement M0-04-006 Progress / authorization / read models exactly as specified in the M0 Execution
Pack. Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-04-007 - Operations E2E hardening** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 4 - Operations Runtime|
|Gate|M0|
|Depends on|M0-04-001..006|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Prove durable, idempotent, domain-safe, authorization-safe and observable asynchronous execution before media/analysis rely on it. 

###### **Implementation contract** 

- Test queue loss after commit, duplicate delivery, worker crash, retry, exhaustion, timeout, waiting, cancellation, compensation, creator access loss and target archive/suspension races. 

- Run restart/reconciliation scenarios and hidden-operation leakage suite. 

###### **Mandatory verification** 

- OPS Gate 1 Durable PASS. 

- OPS Gate 2 Idempotent PASS. 

- OPS Gate 3 Domain-safe PASS. 

- OPS Gate 4 Authorization-safe PASS. 

- OPS Gate 5 Observable PASS. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 33 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

**Definition of Done** 

- Zero Operations P0. 

- Wave report READY. 

- pnpm verify green. 

###### **Codex execution prompt** 

```
Implement M0-04-007 Operations E2E hardening exactly as specified in the M0 Execution Pack. Preserve
all architecture invariants, add tests, and finish with pnpm verify.
```

#### **12. Wave 5 - Media Derivatives & Readiness** 

|**Packet**|**Outcome**|
|---|---|
|M0-03-004|Playback ProxyDerivative|
|M0-03-005|Frame / Thumbnail Extraction Foundation|
|M0-03-006|Media Readiness Projection|
|M0-03-007|Audiovisual Readiness E2E Hardening|



##### **M0-03-004 - Playback Proxy Derivative** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 5 - Media Derivatives & Readiness|
|Gate|M0|
|Depends on|Wave 4, M0-03-003|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Generate immutable browser-friendly playback media from the exact source while leaving source bytes and canonical temporal truth untouched. 

###### **Implementation contract** 

- Create DerivedAssetFile for PLAYBACK_PROXY with exact source AssetFile + source checksum + profile/version. 

- BROWSER_V1: private MP4/H.264/AAC-or-no-audio, faststart, no upscale, max 1080p, correct orientation, browsercompatible HDR policy. 

- Generate automatically after source validation via persistent PLAYBACK_PROXY_GENERATION Operation. 

- Validate derivative output and temporal equivalence before READY; proxy metadata never overwrites ContentVersion duration/source profile. 

- Project ACTIVE and Initial Analysis do not wait for proxy. 

- Ordinary media-access server-selects READY proxy; source fallback only when compatibility policy permits. 

- Superseded source immediately invalidates derivatives for ordinary current selection. 

###### **Commands, queries, APIs and events** 

- DerivedAssetFile model; MediaDerivativeGeneratorPort; WorkspaceMediaSelectionPolicy. 

- Existing media-access response kind PROXY|SOURCE. 

###### **Authorization and visibility** 

- Same asset.view/Project/DataVisibility authorization and short-lived private URL as source. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 34 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Mandatory verification** 

- TC-M03004-001..040 including identity, immutability, profile, temporal equivalence, VFR/HDR, operation retry, supersession, selection, security and NPW integration. 

###### **Definition of Done** 

- Proxy is never source/evidence truth. 

- Analysis semantic readiness separated from workspace playback readiness. 

- All 40 tests and pnpm verify green. 

###### **Codex execution prompt** 

```
Implement M0-03-004 Playback Proxy Derivative exactly as specified in the M0 Execution Pack.
Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-03-005 - Frame / Thumbnail Extraction Foundation** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 5 - Media Derivatives & Readiness|
|Gate|M0|
|Depends on|M0-03-002, M0-03-004|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Provide reproducible source-based frame derivatives for UI navigation without turning thumbnails into <u>governed Evidence.</u> 

###### **Implementation contract** 

- FrameDerivative binds exact current source AssetFile/checksum and requested TimecodeMs. 

- Persist requested_at_ms and actual presented_at_ms. 

- Selection strategies NEAREST (default; tie->earlier), AT_OR_BEFORE, AT_OR_AFTER. 

- VFR extraction uses real decoder presentation timestamps, never avg FPS math. 

- FRAME_UI_V1 profile: private UI image, max 640px width, preserve aspect, no upscale, browser-compatible color treatment. 

- Deduplicate exact request identity; generate asynchronously with persisted derivative state + technical job/outbox, not one user-visible Operation per frame. 

- Read signed URL reauthorizes; source replacement makes old derivatives historical only. 

###### **Commands, queries, APIs and events** 

- POST /content-versions/:id/frame-derivatives; GET /frame-derivatives/:id; FrameExtractorPort. 

###### **Authorization and visibility** 

- asset.view + effective Project access; worker continuation is organization-owned. 

###### **Mandatory verification** 

- TC-M03005-001..039 including bounds, exact source, strategies, VFR, image profile, dedup, replacement, auth, Evidence separation and failure isolation. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 35 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Definition of Done** 

- Thumbnail never creates Evidence. 

- Ready derivative immutable and checksummed. 

- pnpm verify green. 

###### **Codex execution prompt** 

```
Implement M0-03-005 Frame / Thumbnail Extraction Foundation exactly as specified in the M0 Execution
Pack. Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-03-006 - Media Readiness Projection** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 5 - Media Derivatives & Readiness|
|Gate|M0|
|Depends on|M0-03-004/005|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Compose technical/media signals into independent readiness dimensions without persisting another generic lifecycle. 

###### **Implementation contract** 

- Derive SourceReadiness, ProcessingReadiness, TemporalReadiness, PlaybackReadiness, FrameExtractionReadiness, EditorReadiness, FrameMappingReadiness and AnalysisWorkspaceReadiness. 

- Processing depends on valid source/profile, not proxy. 

- VFR can be temporally/editor ready while frame-number mapping remains unavailable. 

- Editor READY requires temporal + frame extraction + usable playback. 

- Fail closed on multiple ACTIVE files, missing profile for VALID source, duration inconsistency or stale proxy binding. 

- Readiness never grants authorization and is reusable by NPW/Content/Editor/Analysis/Overview. 

###### **Commands, queries, APIs and events** 

- GET /content-versions/:id/media-readiness optional diagnostic query; reusable MediaReadinessPolicy. 

###### **Authorization and visibility** 

- Readiness endpoint itself is authorization-safe/no-existence-safe. 

###### **Mandatory verification** 

- TC-M03006-001..036 plus readiness implication property tests. 

###### **Definition of Done** 

- Zero readiness persistence columns. 

- docs/architecture/media-readiness.md frozen. 

- pnpm verify green. 

###### **Codex execution prompt** 

```
Implement M0-03-006 Media Readiness Projection exactly as specified in the M0 Execution Pack.
Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 36 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

##### **M0-03-007 - Audiovisual Readiness E2E Hardening** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 5 - Media Derivatives & Readiness|
|Gate|M0|
|Depends on|M0-03-001..006, Wave 4|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Prove the complete source->profile->temporal->secure playback->proxy->frame->readiness chain across difficult media and operational failures. 

###### **Implementation contract** 

- Use deterministic fixtures: H.264/AAC, no audio, H.265, ProRes/MOV or MKV, true VFR, 24000/1001 CFR, non-zero start, rotation, HDR, multiple streams and corrupt media. 

- Validate source immutability, temporal normalization, proxy timeline correspondence, frame extraction, source replacement, signed URL renewal/revocation and readiness matrix. 

- Run worker/API/Redis/storage restart and duplicate delivery scenarios. 

###### **Mandatory verification** 

- MEDIA Gate 1 Source Fidelity PASS. 

- MEDIA Gate 2 Temporal Integrity PASS. 

- MEDIA Gate 3 Browser Operability PASS. 

- MEDIA Gate 4 Derivative Isolation PASS. 

- MEDIA Gate 5 Secure Access PASS. 

###### **Definition of Done** 

- Mandatory audiovisual hardening report. 

- Zero media P0. 

- Wave 5 READY. 

###### **Codex execution prompt** 

```
Implement M0-03-007 Audiovisual Readiness E2E Hardening exactly as specified in the M0 Execution
Pack. Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

#### **13. Wave 6 - Quick Create** 

|**Packet**|**Outcome**|
|---|---|
|M0-02-005|Quick Create orchestration|
|M0-02-007|NPW-01Quick Create UI|
|M0-02-008|NPW-02 Preparing/ Initial Analysis handof|
|M0-02-009|Quick Create E2E hardening|



Internal  /  Implementation Contract  /  24 Aug 2026 

Page 37 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

##### **M0-02-005 - Quick Create orchestration** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 6 -Quick Create|
|Gate|M0|
|Depends on|Waves 1-5|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Atomically convert a verified UploadSession into minimal Project foundation and durable processing intent with idempotent retry behavior. 

###### **Implementation contract** 

- POST /projects/quick-create accepts title + upload_session_id and idempotency key; owner is Acting Organization, never request body. 

- One DB transaction creates Project DRAFT, creator grant, Content, CV-001, primary Asset, ACTIVE AssetFile referencing the claimed immutable upload, UploadSession CLAIMED, ASSET_VALIDATION/InitialProjectAnalysis foundation, Audit/outbox/idempotency result. 

- Do not create a separate QuickCreateExecution state machine. 

- Post-commit work is Operation-driven. 

- Project activates when primary media is technically valid; CV remains DRAFT. 

###### **Commands, queries, APIs and events** 

- POST /projects/quick-create; idempotency store/contract; InitialProjectAnalysisOperation reference foundation. 

###### **Authorization and visibility** 

- project.create + asset/upload context; revalidate Acting Organization and session ownership before transaction. 

###### **Mandatory verification** 

- Double click/lost response returns same Project. 

- Same upload concurrent claim produces one winner/no partial project. 

- Transaction fault leaves no partial foundation. 

###### **Definition of Done** 

- Atomic foundation. 

- Idempotent HTTP/business result. 

- Outbox operations committed. 

###### **Codex execution prompt** 

```
Implement M0-02-005 Quick Create orchestration exactly as specified in the M0 Execution Pack.
Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-02-007 - NPW-01 Quick Create UI** 

|**Field**<br>**Value**|
|---|
|Wave<br>Wave 6 -Quick Create|
|Gate<br>M0|



Internal  /  Implementation Contract  /  24 Aug 2026 

Page 38 

||**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1**|
|---|---|
|**Field**|**Value**|
|Depends on|M0-02-003, M0-02-005|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Provide the lowest-friction pre-commit creation experience: project title, one audiovisual uploader and one Analyse content action. 

###### **Implementation contract** 

- No Project is created when a file is merely selected. 

- Display “Creating for {Acting Organization}”; no owner selector. 

- Direct upload progress, replace/remove before submit. 

- Do not ask for modules, licenses, team, territory, content type or unnecessary metadata. 

- One backend Quick Create call; frontend never orchestrates domain steps. 

- Organization mismatch offers explicit switch-back or re-upload, never auto-switch. 

###### **Commands, queries, APIs and events** 

- Web upload/Quick Create integration. 

###### **Authorization and visibility** 

- UI reflects current authorized Acting Organization. 

###### **Mandatory verification** 

- Minimal happy path. 

- Multipart/lost response. 

- Org switch during staging. 

- Accessibility/keyboard/error recovery. 

###### **Definition of Done** 

- NPW-01 usable and minimal. 

- No duplicated backend orchestration in frontend. 

###### **Codex execution prompt** 

```
Implement M0-02-007 NPW-01 Quick Create UI exactly as specified in the M0 Execution Pack. Preserve
all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-02-008 - NPW-02 Preparing / Initial Analysis handoff** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 6 -Quick Create|
|Gate|M0|
|Depends on|M0-02-005, Wave 5|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



Internal  /  Implementation Contract  /  24 Aug 2026 

Page 39 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Objective** 

Turn the Analyse content click into a resumable owner-routed InitialProjectAnalysisOperation and re-entry view, not a second wizard. 

###### **Implementation contract** 

- Route /projects/:id/preparing reconstructs state from backend. 

- Public phases: PREPARING_MEDIA / UNDERSTANDING_CONTENT / READY / NEEDS_ATTENTION (copy may simplify). 

- No second Start Analysis button for QUICK_CREATE creation mode. 

- StartInitialAnalysis always receives exact CV-001; no latest/current lookup. 

- Operation and AnalysisRun remain distinct. 

- Redirect to Analysis only when consistent AnalysisSnapshot exists, playback is usable and actor currently has analysis.view. 

- Pre-analysis invalid-media recovery may replace technical source on same Project/Content/CV/Asset, superseding old AssetFile; post-analysis shortcut forbidden. 

###### **Commands, queries, APIs and events** 

- ProjectPreparationView; ReplaceDraftPrimaryAudiovisual recovery command. 

###### **Authorization and visibility** 

- Re-entry reauthorizes Project and target Analysis surface. 

###### **Mandatory verification** 

- Refresh/navigation survives. 

- Analysis can complete before proxy; workspace waits without rerun. 

- Creator access revoked: process continues but re-entry denied. 

###### **Definition of Done** 

- InitialProjectAnalysisOperation owns durable workflow. 

- No browser-local truth. 

###### **Codex execution prompt** 

```
Implement M0-02-008 NPW-02 Preparing / Initial Analysis handoff exactly as specified in the M0
Execution Pack. Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-02-009 - Quick Create E2E hardening** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 6 -Quick Create|
|Gate|M0|
|Depends on|M0-02-003..008, Waves 1-5|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Prove the complete minimal Project funnel is atomic, durable, exact-media-bound and organizationally isolated. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 40 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Implementation contract** 

- Test full funnel, multipart, lost responses, double-click, same-upload races, transaction injection, outbox/queue recovery, worker restart, invalid media replacement, archive races, expiry cleanup, org switch and creator access revocation. 

###### **Mandatory verification** 

- Gate Minimal Friction PASS. 

- Gate Atomic Foundation PASS. 

- Gate Exact Media Truth PASS. 

- Gate Durable Processing PASS. 

- Gate Context Isolation PASS. 

###### **Definition of Done** 

- Zero Quick Create P0. 

- Wave 6 report READY. 

- pnpm verify green. 

###### **Codex execution prompt** 

```
Implement M0-02-009 Quick Create E2E hardening exactly as specified in the M0 Execution Pack.
Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

#### **14. Wave 7 - Inventory, Catalog & Casting** 

|**Packet**|**Outcome**|
|---|---|
|M0-05-001|InventoryType / Familyregistry|
|M0-05-002|InventoryItem aggregate|
|M0-05-003|CanonicalEntity/ CanonicalLink minimum|
|M0-05-004|Person / Character / Cast minimum|
|M0-05-005|Inventorymerge|
|M0-05-006|Inventoryread models|
|M0-05-007|Inventory/ Catalog/ Castinghardening|



##### **M0-05-001 - Inventory Type / Family registry** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 7 - Inventory, Catalog& Casting|
|Gate|M0|
|Depends on|Wave 6|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Define a minimum extensible vocabulary that separates identity type from functional/commercial family and does not mirror any AI provider taxonomy. 

###### **Implementation contract** 

- InventoryItemType baseline: OBJECT, PRODUCT, BRAND, PERSON, CHARACTER, PLACE, TEXT, OTHER. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 41 

###### **IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

- Family is a separate governed registry for commercial/functional grouping (e.g. Vehicle, Food & Beverage, Fashion, Electronics, Furniture, Location, Person/Character, Visible Text). 

- 

- 

- Provider labels map through adapters; they never become core enums silently. 

- Taxonomy can evolve without rewriting temporal facts. 

###### **Commands, queries, APIs and events** 

- Type/Family registry queries and provider mapping adapter. 

###### **Authorization and visibility** 

- Reference vocabulary is globally readable only if Data Visibility permits; local assignments remain Project-private. 

###### **Mandatory verification** 

- Provider-specific class can map without changing core type. 

- Type and Family remain independently changeable. 

###### **Definition of Done** 

- Registry seeded/versioned. 

- No provider lock-in. 

###### **Codex execution prompt** 

```
Implement M0-05-001 Inventory Type / Family registry exactly as specified in the M0 Execution Pack.
Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-05-002 - InventoryItem aggregate** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 7 - Inventory, Catalog& Casting|
|Gate|M0|
|Depends on|M0-05-001|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Create the Project-local identity for “what exists” independently from when it appears. 

###### **Implementation contract** 

- Fields: id, project_id, canonical_name, item_type, family?, source, lifecycle, provenance, timestamps. 

- Source MANUAL/AI_ASSISTED/IMPORTED. 

- Lifecycle ACTIVE/MERGED/ARCHIVED. 

- No temporal fields. 

- Duplicate display names are allowed when identities differ. 

- Catalog link is optional and cannot be identity authority. 

###### **Commands, queries, APIs and events** 

- Create/Edit/ArchiveInventoryItem. 

- InventoryItemCreated/Changed/Archived. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 42 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Authorization and visibility** 

- inventory.view/create/edit/archive through effective Project access. 

###### **Mandatory verification** 

- Item valid with zero appearances. 

- Two same-name distinct items allowed. 

- Cross-project item reference rejected. 

###### **Definition of Done** 

- Aggregate and commands implemented. 

- No timing fields. 

###### **Codex execution prompt** 

```
Implement M0-05-002 InventoryItem aggregate exactly as specified in the M0 Execution Pack. Preserve
all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-05-003 - CanonicalEntity / CanonicalLink minimum** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 7 - Inventory, Catalog& Casting|
|Gate|M0|
|Depends on|M0-05-002|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Implement the minimum global canonical identity layer for Brand/Product/Person/Character without absorbing Project-local truth. 

###### **Implementation contract** 

- CanonicalEntity types BRAND/PRODUCT/PERSON/CHARACTER; lifecycle ACTIVE/ARCHIVED/MERGED. 

- CanonicalLink relates InventoryItem to canonical identity and has proposal/validation lifecycle; link is optional. 

- External IDs and aliases have provenance. 

- No private Project Evidence copied into Catalog. 

- Catalog identity and taxonomy remain separate. 

###### **Commands, queries, APIs and events** 

- Search canonical identities, link existing, promote/create minimal flow. 

- CanonicalLinkCreated/Changed. 

###### **Authorization and visibility** 

- catalog.view/link plus underlying Project access; aggregate counts only over authorized universe. 

###### **Mandatory verification** 

- InventoryItem without CanonicalLink remains valid. 

- Link does not rewrite InventoryItem. 

- Unauthorized actor cannot infer Project usage from counts. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 43 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Definition of Done** 

- Minimum M0 canonical layer. 

- Full CAT reconciliation deferred. 

###### **Codex execution prompt** 

```
Implement M0-05-003 CanonicalEntity / CanonicalLink minimum exactly as specified in the M0 Execution
Pack. Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-05-004 - Person / Character / Cast minimum** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 7 - Inventory, Catalog& Casting|
|Gate|M0|
|Depends on|M0-05-003|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Support manual Person/Character/CastCredit resolution sufficient for Analysis and Editor without biometrics or full provider reconciliation. 

###### **Implementation contract** 

- Distinguish Person identity from Character identity. 

- CastCredit/CharacterAssignment remain Project/content-local relationships with provenance and external references. 

- AI may detect Person candidate; user may resolve to Character using Project cast. 

- Manual overrides are protected from silent provider overwrite. 

- IMDb/TMDB full automation and biometric recognition are deferred. 

###### **Commands, queries, APIs and events** 

- Manual cast CRUD/resolution; optional provider import adapter boundary. 

###### **Authorization and visibility** 

- content/cast relevant capabilities and Project access; no biometric data in M0. 

###### **Mandatory verification** 

- Person and Character can coexist distinctly. 

- Provider data cannot silently override local accepted assignment. 

###### **Definition of Done** 

- Casting minimum usable in Analysis/Editor. 

- Deferred automation clearly bounded. 

###### **Codex execution prompt** 

```
Implement M0-05-004 Person / Character / Cast minimum exactly as specified in the M0 Execution Pack.
Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 44 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

**M0-05-005 - Inventory merge** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 7 - Inventory, Catalog& Casting|
|Gate|M0|
|Depends on|M0-05-002|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Provide governed identity merge while preserving lineage and keeping appearance operations semantically separate. 

###### **Implementation contract** 

- Merge target remains ACTIVE; sources become MERGED and redirect/historical lineage retained. 

- Appearances are relinked through governed operation/transaction with audit. 

- Never auto-merge by name or AI confidence alone. 

- Inventory merge and Appearance merge use distinct command names/semantics. 

###### **Commands, queries, APIs and events** 

- MergeInventoryItems. 

- InventoryItemsMerged. 

###### **Authorization and visibility** 

- inventory.merge and Project effective access. 

###### **Mandatory verification** 

- Merge preserves IDs/history/provenance. 

- Merged source cannot become unrelated new item. 

- Appearances remain exact-CV-bound. 

###### **Definition of Done** 

- Merge lineage/audit. 

- No destructive delete. 

###### **Codex execution prompt** 

```
Implement M0-05-005 Inventory merge exactly as specified in the M0 Execution Pack. Preserve all
architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-05-006 - Inventory read models** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 7 - Inventory, Catalog& Casting|
|Gate|M0|
|Depends on|M0-05-002..005|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



Internal  /  Implementation Contract  /  24 Aug 2026 

Page 45 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Objective** 

Expose operational Inventory views without duplicating authority or leaking hidden vertical information. 

###### **Implementation contract** 

- Row includes item ID/name/type/family, appearance count, source, authorized tags and optional canonical summary. 

- No Assigned to column. 

- Ads/Interactive/Clearance are not three fixed boolean columns; vertical relevance/tags are separate projections. 

- Search/counts constrained to authorized Project universe. 

###### **Commands, queries, APIs and events** 

- Inventory list/detail/search read models. 

###### **Authorization and visibility** 

- inventory.view + need-to-know for projected vertical tags. 

###### **Mandatory verification** 

- Hidden vertical tag/count omitted. 

- Merged/archived behavior correct. 

- Counts derive from appearances, not stored counter authority. 

###### **Definition of Done** 

- Stable read contracts for Editor/Analysis. 

###### **Codex execution prompt** 

```
Implement M0-05-006 Inventory read models exactly as specified in the M0 Execution Pack. Preserve
all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-05-007 - Inventory / Catalog / Casting hardening** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 7 - Inventory, Catalog& Casting|
|Gate|M0|
|Depends on|M0-05-001..006|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Prove local/global identity boundaries, provenance, merge lineage, casting resolution and authorization before Editor materializes Core truth. 

###### **Implementation contract** 

- Test manual/AI-assisted/imported provenance, optional canonicalization, merge, same-name items, Person>Character resolution, local override protection and cross-project isolation. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 46 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Mandatory verification** 

- InventoryItem without CanonicalLink valid. 

- Catalog never leaks private evidence/project usage. 

- Merge lineage and authorization green. 

- Casting manual flow green. 

###### **Definition of Done** 

- Zero Wave 7 P0. 

- Architecture drift clean. 

- Wave report READY. 

###### **Codex execution prompt** 

```
Implement M0-05-007 Inventory / Catalog / Casting hardening exactly as specified in the M0 Execution
Pack. Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

#### **15. Wave 8 - Scene, Appearance & Editor** 

|**Packet**|**Outcome**|
|---|---|
|M0-06-001|Appearance aggregate|
|M0-06-002|Scene / SceneStructure|
|M0-06-003|Editor Trackprojection|
|M0-06-004|Manual authoring|
|M0-06-005|Split / merge / regroup/ reassign|
|M0-06-006|AI candidate bridges|
|M0-06-007|Editor workspace M0|
|M0-06-008|Editor E2E hardening|



##### **M0-06-001 - Appearance aggregate** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 8 - Scene, Appearance & Editor|
|Gate|M0|
|Depends on|Wave 7, M0-03-002|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Represent when one InventoryItem appears in one exact ContentVersion using canonical temporal ranges and explicit provenance. 

###### **Implementation contract** 

- Fields: id, inventory_item_id, content_version_id, start_ms, end_ms, source, provenance, timestamps. 

- Source MANUAL/AI/IMPORTED. 

- Use canonical half-open TimeRange; no timecode string authority. 

- Project item and ContentVersion must belong to the same Project. 

- Validation is not a boolean field on Appearance. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 47 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Commands, queries, APIs and events** 

- CreateAppearance, AdjustAppearanceTiming, ArchiveOrSupersedeAppearance. 

- AppearanceCreated/Adjusted/ArchivedOrSuperseded. 

###### **Authorization and visibility** 

- appearance.view/create/edit/archive + Editor/domain policy. 

###### **Mandatory verification** 

- Bounds/half-open semantics. 

- Cross-CV/project rejected. 

- VFR timecode remains valid. 

###### **Definition of Done** 

- Appearance core stable. 

- Exact CV mandatory. 

###### **Codex execution prompt** 

```
Implement M0-06-001 Appearance aggregate exactly as specified in the M0 Execution Pack. Preserve all
architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-06-002 - Scene / SceneStructure** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 8 - Scene, Appearance & Editor|
|Gate|M0|
|Depends on|M0-06-001|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Provide minimal authoritative temporal scene structure required by M0 Editor and context analysis. 

###### **Implementation contract** 

- Scene binds exact ContentVersion and [start,end). 

- Fields: id, content_version_id, range, optional label/title, provenance. 

- Commands CreateScene, AdjustScene, SplitScene, MergeScenes. 

- M0 does not attempt sophisticated cinematic ontology. 

- Scene truth is separate from AI scene proposals and from UI track structure. 

###### **Commands, queries, APIs and events** 

- Scene CRUD/split/merge commands; SceneCreated/Adjusted/Split/Merged. 

###### **Authorization and visibility** 

- scene.view/create/edit/split/merge. 

###### **Mandatory verification** 

- Scene split/merge preserves complete non-overwritten history where required. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 48 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

- Cross-CV merge rejected. 

- Temporal bounds enforced. 

###### **Definition of Done** 

- Minimal SceneStructure usable by Analysis/Editor. 

###### **Codex execution prompt** 

```
Implement M0-06-002 Scene / SceneStructure exactly as specified in the M0 Execution Pack. Preserve
all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-06-003 - Editor Track projection** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 8 - Scene, Appearance & Editor|
|Gate|M0|
|Depends on|M0-06-001|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Represent the approved rule “one track = one InventoryItem” as a read projection, not another authoritative entity. 

###### **Implementation contract** 

- EditorTrack = InventoryItem + Appearances for selected exact ContentVersion. 

- No Track table/aggregate unless future semantics require independent identity. 

- Track ordering/UI state remains presentation state. 

- Merged/archived items project consistently. 

###### **Commands, queries, APIs and events** 

- EditorTrack query/bootstrap. 

###### **Authorization and visibility** 

- editor.view + inventory/appearance read capabilities. 

###### **Mandatory verification** 

- One item yields one track for selected CV. 

- Same item appearances group correctly. 

- No stale appearances from other CV. 

###### **Definition of Done** 

- No second track Source of Truth. 

###### **Codex execution prompt** 

```
Implement M0-06-003 Editor Track projection exactly as specified in the M0 Execution Pack. Preserve
all architecture invariants, add tests, and finish with pnpm verify.
```

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 49 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

##### **M0-06-004 - Manual authoring** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 8 - Scene, Appearance & Editor|
|Gate|M0|
|Depends on|M0-06-001..003, M0-03-006|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Allow reliable manual authoring and timing correction using canonical MediaClock/time ranges. 

###### **Implementation contract** 

- Create/adjust/remove/supersede appearances from explicit start_ms/end_ms. 

- Create/edit InventoryItem from Editor where capabilities allow. 

- Player supplies normalized TimecodeMs through shared MediaClock; backend never “reads player”. 

- Unknown duration/temporal-not-ready blocks temporal mutation. 

###### **Commands, queries, APIs and events** 

- Editor mutation API over Inventory/Appearance/Scene commands. 

###### **Authorization and visibility** 

- editor.view plus specific mutation capability; readiness visible but not authority. 

###### **Mandatory verification** 

- Create at playhead then adjust. 

- Permission revoke mid-edit fails server-side. 

- Browser refresh returns current authoritative data. 

###### **Definition of Done** 

- Manual-first M0 editing works. 

###### **Codex execution prompt** 

```
Implement M0-06-004 Manual authoring exactly as specified in the M0 Execution Pack. Preserve all
architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-06-005 - Split / merge / regroup / reassign** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 8 - Scene, Appearance & Editor|
|Gate|M0|
|Depends on|M0-06-004|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Implement practical correction tools while keeping identity operations and temporal operations semantically distinct. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 50 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Implementation contract** 

- Appearance split divides one range into governed child ranges. 

- Appearance merge only for same InventoryItem + exact ContentVersion and compatible ranges/policy. 

- Regroup/reassign moves candidate/appearance association through explicit command; does not silently merge identities. 

- Inventory merge remains M0-05 command; Scene split/merge remains M0-06-002. 

###### **Commands, queries, APIs and events** 

- SplitAppearance, MergeAppearances, ReassignAppearance/RegroupCandidate as explicit commands. 

###### **Authorization and visibility** 

- appearance.edit; inventory.merge separately where identity merge requested. 

###### **Mandatory verification** 

- Adjacent ranges handle half-open semantics. 

- Cross-item/CV merge rejected. 

- Reassign preserves provenance. 

###### **Definition of Done** 

- No ambiguous generic “merge”. 

- Audit/provenance retained. 

###### **Codex execution prompt** 

```
Implement M0-06-005 Split / merge / regroup / reassign exactly as specified in the M0 Execution
Pack. Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-06-006 - AI candidate bridges** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 8 - Scene, Appearance & Editor|
|Gate|M0|
|Depends on|M0-06-004, Wave 9 Analysis contracts may initially be|
||stubbed/port-based|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Create the only <u>governed bridge from Analysis proposals into Core truth.</u> 

###### **Implementation contract** 

- DetectionCandidate is not InventoryItem/Appearance/Scene. 

- AcceptDetectionCandidate can link existing item or create new and materialize Appearance. 

- CorrectAndAccept persists human correction/provenance before materialization. 

- Reject preserves proposal history. 

- No provider/AI can bypass the bridge to create accepted Core truth. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 51 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Commands, queries, APIs and events** 

- AcceptDetectionCandidate, CorrectAndAcceptDetectionCandidate, RejectDetectionCandidate. 

- DetectionCandidateAccepted/CorrectedAndAccepted/Rejected. 

###### **Authorization and visibility** 

- analysis.review and/or appropriate editor/validation policy; exact capability mapping declared. 

###### **Mandatory verification** 

- AI proposal alone never appears as accepted Core. 

- Correction history preserved. 

- Idempotent double-accept blocked. 

###### **Definition of Done** 

- Governed candidate->Core boundary. 

###### **Codex execution prompt** 

```
Implement M0-06-006 AI candidate bridges exactly as specified in the M0 Execution Pack. Preserve all
architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-06-007 - Editor workspace M0** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 8 - Scene, Appearance & Editor|
|Gate|M0|
|Depends on|M0-06-001..006, M0-03-006|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Assemble the M0 Editor surfaces around the authoritative Core model and media readiness without hiding authorized-but-preparing resources. 

###### **Implementation contract** 

- EDT-01 Timeline/player with tracks and scene structure. 

- EDT-02 Appearance Inspector with timing/provenance/validation affordances. 

- EDT-03 Element/Track Detail for InventoryItem-level operations. 

- Support manual creation, timing correction, regroup/reassign/split, Evidence/Validation entry points and AI candidate inspection. 

- If authorized but media PREPARING, surface is visible with preparation state; if not authorized, HIDDEN. 

###### **Commands, queries, APIs and events** 

- Editor bootstrap/read models and mutation integration. 

###### **Authorization and visibility** 

- Server navigation/effective access and individual mutation capabilities. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 52 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Mandatory verification** 

- Direct deep link authorization. 

- VFR editing in ms. 

- No hidden module metadata. 

- Preparation vs hidden distinction. 

###### **Definition of Done** 

- M0 Editor reference behavior complete. 

- No local state as truth. 

###### **Codex execution prompt** 

```
Implement M0-06-007 Editor workspace M0 exactly as specified in the M0 Execution Pack. Preserve all
architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-06-008 - Editor E2E hardening** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 8 - Scene, Appearance & Editor|
|Gate|M0|
|Depends on|M0-06-001..007|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Prove manual and AI-assisted temporal correction, SceneStructure and exact version binding under authorization and media complexity. 

###### **Implementation contract** 

- Test manual appearance, scene authoring, split/merge/reassign, AI candidate acceptance/rejection, item merge impacts, VFR timing, permission revoke, refresh and no stale version mapping. 

###### **Mandatory verification** 

- Exact CV gate PASS. 

- Candidate/Core distinction PASS. 

- Manual authoring PASS. 

- Need-to-know/auth PASS. 

- Scene/Appearance provenance PASS. 

###### **Definition of Done** 

- Zero Editor M0 P0. 

- Wave 8 READY. 

###### **Codex execution prompt** 

```
Implement M0-06-008 Editor E2E hardening exactly as specified in the M0 Execution Pack. Preserve all
architecture invariants, add tests, and finish with pnpm verify.
```

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 53 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

#### **16. Wave 9 - Analysis** 

|**Packet**|**Outcome**|
|---|---|
|M0-07-001|AnalysisRun aggregate|
|M0-07-002|DetectionCandidate normalization|
|M0-07-003|Candidate clustering/grouping proposals|
|M0-07-004|Context Taxonomy|
|M0-07-005|Vertical Relevance|
|M0-07-006|AnalysisSnapshot|
|M0-07-007|Business Opportunities / KeyContexts|
|M0-07-008|Customer teaser vs authorized workspace|
|M0-07-009|Analysis UIprojections|
|M0-07-010|Analysis E2E hardening|



##### **M0-07-001 - AnalysisRun aggregate** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 9 - Analysis|
|Gate|M0|
|Depends on|Waves 4-6|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Represent analysis semantic execution separately from Operation runtime and bind every run to one exact ContentVersion/profile. 

###### **Implementation contract** 

- Fields: id, project_id, content_version_id, analysis_profile/version, state, operation_id, timestamps/failure code. 

- Initial logical run unique per exact CV + profile/version unless explicit rerun. 

- Operation controls durable process; AnalysisRun controls analytical lifecycle/result semantics. 

- No “current analysis” shortcut as temporal authority. 

###### **Commands, queries, APIs and events** 

- Create/Start/Complete/Fail AnalysisRun; AnalysisRunCreated/Started/Completed/Failed. 

###### **Authorization and visibility** 

- analysis.run + Project policy; Quick Create system orchestration may start owner-routed initial run after media processing readiness. 

###### **Mandatory verification** 

- Proxy not required for run. 

- Exact CV always present. 

- Operation retry does not duplicate logical run. 

###### **Definition of Done** 

- Run/Operation separation complete. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 54 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Codex execution prompt** 

```
Implement M0-07-001 AnalysisRun aggregate exactly as specified in the M0 Execution Pack. Preserve
all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-07-002 - DetectionCandidate normalization** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 9 - Analysis|
|Gate|M0|
|Depends on|M0-07-001, M0-03-002|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Normalize provider/AI observations into canonical proposals without granting them Core authority. 

###### **Implementation contract** 

- Candidate fields include exact AnalysisRun/CV, type, proposed identity/name, confidence, canonical start/end and provider provenance. 

- Provider timestamps/classes normalize through adapters before persistence. 

- Candidate may be rejected/accepted later; raw provider result is not Core truth. 

- All temporal ranges use M0 temporal package. 

###### **Commands, queries, APIs and events** 

- Analysis provider adapter + candidate persistence. 

###### **Authorization and visibility** 

- Raw/provider detail restricted to authorized workspace/internal policy. 

###### **Mandatory verification** 

- Provider-specific payload cannot bypass validation. 

- VFR/time-base normalization correct. 

- Out-of-bounds candidates rejected/flagged. 

###### **Definition of Done** 

- Canonical candidate schema provider-agnostic. 

###### **Codex execution prompt** 

```
Implement M0-07-002 DetectionCandidate normalization exactly as specified in the M0 Execution Pack.
Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-07-003 - Candidate clustering / grouping proposals** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 9 - Analysis|
|Gate|M0|
|Depends on|M0-07-002|



Internal  /  Implementation Contract  /  24 Aug 2026 

Page 55 

||**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1**|
|---|---|
|**Field**|**Value**|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Group likely repeated detections as non-authoritative proposals so multiple appearances may later resolve to one InventoryItem. 

###### **Implementation contract** 

- CandidateCluster is an analysis proposal/read structure, not Inventory identity. 

- Clustering preserves member candidates and confidence/provenance. 

- No irreversible automatic merge based only on similarity. 

- Human/system bridge may materialize one InventoryItem with many Appearances. 

###### **Commands, queries, APIs and events** 

- Cluster generation/query contract. 

###### **Authorization and visibility** 

- analysis.view/review. 

###### **Mandatory verification** 

- Multiple detections can cluster without creating Core. 

- Cluster edits/reruns do not mutate accepted Inventory automatically. 

###### **Definition of Done** 

- Grouping supports approved “one item, many appearances” workflow. 

###### **Codex execution prompt** 

```
Implement M0-07-003 Candidate clustering / grouping proposals exactly as specified in the M0
Execution Pack. Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-07-004 - Context Taxonomy** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 9 - Analysis|
|Gate|M0|
|Depends on|M0-07-002, M0-06-002|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Create a versioned minimum context vocabulary and assignments sufficient for M0 commercial interpretation without turning free text or provider labels into uncontrolled truth. 

###### **Implementation contract** 

- ContextTerm + ContextTaxonomyVersion + ContextAssignment. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 56 

###### **IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

- 

- 

- 

- Assignments may bind ContentVersion, Scene or temporal range as defined by type. 

- M0 vocabulary focuses on useful content/commercial context; taxonomy remains extensible. 

- Provider context labels map through adapter/provenance. 

###### **Commands, queries, APIs and events** 

- Context taxonomy/query/assignment services. 

###### **Authorization and visibility** 

- analysis.view; taxonomy visibility depends on classification, assignments remain Project-private. 

###### **Mandatory verification** 

- Versioning does not reinterpret old snapshot. 

- Assignment exact-CV/range integrity. 

###### **Definition of Done** 

- Minimum context vocabulary stable. 

###### **Codex execution prompt** 

```
Implement M0-07-004 Context Taxonomy exactly as specified in the M0 Execution Pack. Preserve all
architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-07-005 - Vertical Relevance** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 9 - Analysis|
|Gate|M0|
|Depends on|M0-07-004|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Represent that the same observation can matter differently to Advertising, Interactive and Clearance without adding fixed boolean columns to Core. 

###### **Implementation contract** 

- VerticalRelevance subject can reference candidate/item/context as policy defines. 

- Verticals M0 interpretation: ADVERTISING, INTERACTIVE, CLEARANCE. 

- Relevance + reason/provenance are analytical/commercial interpretation, not automatic product activation. 

- One element need not map 1:1 to all verticals. 

###### **Commands, queries, APIs and events** 

- Vertical relevance computation/read contracts. 

###### **Authorization and visibility** 

- Need-to-know: only authorized/teaser-safe aggregates can be projected. 

###### **Mandatory verification** 

- Car vs license-plate can have different vertical relevance. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 57 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

- Hidden detailed vertical data omitted while allowed aggregate remains. 

###### **Definition of Done** 

- No Ads/Interactive/Clearance booleans on InventoryItem. 

###### **Codex execution prompt** 

```
Implement M0-07-005 Vertical Relevance exactly as specified in the M0 Execution Pack. Preserve all
architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-07-006 - AnalysisSnapshot** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 9 - Analysis|
|Gate|M0|
|Depends on|M0-07-002..005|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Materialize one immutable, internally consistent view of an Analysis result for a precise run/version without replacing raw candidates or accepted Core. 

###### **Implementation contract** 

- Snapshot binds AnalysisRun, ContentVersion and snapshot version. 

- Contains/references candidate inventory, contexts, vertical relevance and business-opportunity aggregates. 

- Created atomically only when result is consistent. 

- Rerun creates another snapshot; historical snapshot remains immutable. 

- Analysis availability requires consistent snapshot, not only AnalysisRun COMPLETED. 

###### **Commands, queries, APIs and events** 

- BuildAnalysisSnapshot; AnalysisSnapshotCreated. 

###### **Authorization and visibility** 

- analysis.view + entitlement/detail policy on projection. 

###### **Mandatory verification** 

- Completed run without snapshot is unavailable. 

- Snapshot never changes after creation. 

- Stale snapshot cannot silently claim new source/version. 

###### **Definition of Done** 

- Snapshot consistency contract stable. 

###### **Codex execution prompt** 

```
Implement M0-07-006 AnalysisSnapshot exactly as specified in the M0 Execution Pack. Preserve all
architecture invariants, add tests, and finish with pnpm verify.
```

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 58 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

##### **M0-07-007 - Business Opportunities / Key Contexts** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 9 - Analysis|
|Gate|M0|
|Depends on|M0-07-005/006|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Expose the approved commercial value signals from structured analysis while preserving family-level semantics and M0 teaser boundaries. 

###### **Implementation contract** 

- Advertising opportunities aggregate High/Medium/Low by opportunity family. 

- Interactive shows total activation opportunities; opportunity is family-level, not necessarily concrete product-level. 

- Clearance M0 shows total clearance-relevant elements; high-risk evolution later. 

- Key Contexts include only contexts with meaningful commercial/advertising interest. 

- UI order: Business Opportunities before Key Contexts. 

###### **Commands, queries, APIs and events** 

- BusinessOpportunitySummary/KeyContext projections. 

###### **Authorization and visibility** 

- Aggregates may be teaser-safe by policy; detailed backing facts require workspace entitlement/access. 

###### **Mandatory verification** 

- Family aggregation not inflated by repeated appearances. 

- Clearance-relevant aggregate works without full Clearance product. 

- Key contexts filtered for commercial usefulness. 

###### **Definition of Done** 

- Approved Analysis value proposition represented. 

###### **Codex execution prompt** 

```
Implement M0-07-007 Business Opportunities / Key Contexts exactly as specified in the M0 Execution
Pack. Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-07-008 - Customer teaser vs authorized workspace** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 9 - Analysis|
|Gate|M0|
|Depends on|M0-07-006/007, M0-09-005 contract maybeport initially|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



Internal  /  Implementation Contract  /  24 Aug 2026 

Page 59 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Objective** 

Create two policy-governed projections over the same Analysis truth: aggregate value discovery and operational detail. 

###### **Implementation contract** 

- Teaser may show aggregate Content Intelligence, Business Opportunities, Key Contexts and meaningful Clearance signal. 

- Teaser must not expose exploitable item/appearance detail, raw candidate data or API alternatives that reconstruct it. 

- Workspace may show candidate/item detail, appearances, thumbnails, confidence, source and validation actions when authorized. 

- License/entitlement and need-to-know are checked before composition. 

###### **Commands, queries, APIs and events** 

- AnalysisTeaserView and AnalysisWorkspaceView. 

###### **Authorization and visibility** 

- EffectiveAccess + EffectiveEntitlement where required. 

###### **Mandatory verification** 

- No-license user cannot reconstruct dataset through pagination/search/counts. 

- Workspace detail disappears on entitlement/access expiry. 

###### **Definition of Done** 

- Non-bypass teaser/workspace boundary. 

###### **Codex execution prompt** 

```
Implement M0-07-008 Customer teaser vs authorized workspace exactly as specified in the M0 Execution
Pack. Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-07-009 - Analysis UI projections** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 9 - Analysis|
|Gate|M0|
|Depends on|M0-07-006..008, Wave 8|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Assemble Overview, Workspace, Inspector and Extended View using immutable snapshot + current authorized Core/validation projections without creating UI-owned truth. 

###### **Implementation contract** 

- Overview presents Business Opportunities then Key Contexts. 

- Workspace supports operational candidate/Core inspection. 

- Inspector/Extended View use source-based thumbnails and exact timecode. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 60 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

- 

- 

- Open in Editor is navigation contract, not DomainEvent. 

- AnalysisWorkspaceReadiness includes usable playback; AnalysisRun state remains independent. 

###### **Commands, queries, APIs and events** 

- Analysis bootstrap, inspector, extended read models; Open-in-Editor route context. 

###### **Authorization and visibility** 

- analysis.view/review + need-to-know; every related Core mutation uses its own capability. 

###### **Mandatory verification** 

- Analysis complete but proxy preparing behaves correctly. 

- Hidden detailed data absent. 

- Open Editor preserves exact CV/time context. 

###### **Definition of Done** 

- M0 Analysis surfaces contract-complete. 

###### **Codex execution prompt** 

```
Implement M0-07-009 Analysis UI projections exactly as specified in the M0 Execution Pack. Preserve
all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-07-010 - Analysis E2E hardening** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 9 - Analysis|
|Gate|M0|
|Depends on|M0-07-001..009|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Prove Initial Analysis, provider normalization, snapshot consistency, commercial aggregates, teaser/workspace separation and Analysis<->Editor workflow. 

###### **Implementation contract** 

- Test auto-launch, exact CV, VFR candidates, clustering, snapshot atomicity, business opportunities, contexts, nolicense leakage, retry/rerun and stale snapshot behavior. 

###### **Mandatory verification** 

- Analysis semantic integrity PASS. 

- Candidate/Core boundary PASS. 

- Teaser non-bypass PASS. 

- Analysis<->Editor exact-version navigation PASS. 

- Initial Analysis durability PASS. 

###### **Definition of Done** 

- Zero Analysis M0 P0. 

- Wave 9 READY. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 61 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Codex execution prompt** 

```
Implement M0-07-010 Analysis E2E hardening exactly as specified in the M0 Execution Pack. Preserve
all architecture invariants, add tests, and finish with pnpm verify.
```

#### **17. Wave 10 - Validation & Evidence** 

|**Packet**|**Outcome**|
|---|---|
|M0-08-001|ValidationDecision|
|M0-08-002|Validation stateprojection|
|M0-08-003|Evidence aggregate|
|M0-08-004|Evidence artifactgeneration|
|M0-08-005|Review integration|
|M0-08-006|Validation / Evidence hardening|



##### **M0-08-001 - ValidationDecision** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 10 - Validation & Evidence|
|Gate|M0|
|Depends on|Wave 8/9|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Make human/system-governed validation an append-only decision history rather than a mutable boolean. 

###### **Implementation contract** 

- ValidationDecision stores subject type/id, ACCEPT/REJECT/CORRECT, actor_user_id, membership_id, reason/provenance and timestamp. 

- Decision records are immutable. 

- CORRECT preserves proposal/history and points to corrected truth/result as needed. 

- Validation state displayed elsewhere is a projection of decision history. 

###### **Commands, queries, APIs and events** 

- RecordValidationDecision; ValidationDecisionRecorded. 

###### **Authorization and visibility** 

- validation.decide; validation.view for history/projection. 

###### **Mandatory verification** 

- Actor/membership provenance present. 

- No destructive overwrite of prior decision. 

- Permission revoke blocks new decision but preserves history. 

###### **Definition of Done** 

- No isValidated authority. 

- Decision projection deterministic. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 62 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Codex execution prompt** 

```
Implement M0-08-001 ValidationDecision exactly as specified in the M0 Execution Pack. Preserve all
architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-08-002 - Validation state projection** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 10 - Validation & Evidence|
|Gate|M0|
|Depends on|M0-08-001|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Provide operational UNREVIEWED/ACCEPTED/REJECTED/CORRECTED views derived from immutable decisions without collapsing <u>provenance.</u> 

###### **Implementation contract** 

- Projection can attach to candidates, appearances or other governed subjects according to subject registry. 

- Latest/effective view is derived by decision rules; history remains queryable. 

- Corrected Appearance/entity preserves link to original proposal/correction provenance. 

###### **Commands, queries, APIs and events** 

- ValidationState query/projection. 

###### **Authorization and visibility** 

- validation.view plus underlying resource visibility. 

###### **Mandatory verification** 

- Projection changes after new decision without rewriting history. 

- Hidden subject does not leak through validation counts. 

###### **Definition of Done** 

- Reusable validation view for Editor/Analysis. 

###### **Codex execution prompt** 

```
Implement M0-08-002 Validation state projection exactly as specified in the M0 Execution Pack.
Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-08-003 - Evidence aggregate** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 10 - Validation & Evidence|
|Gate|M0|
|Depends on|M0-08-001, M0-03-005|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



Internal  /  Implementation Contract  /  24 Aug 2026 

Page 63 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Objective** 

Create governed, source-bound evidence distinct from UI thumbnails and playback derivatives. 

###### **Implementation contract** 

- M0 EvidenceType FRAME. 

- Bind Project, exact ContentVersion, subject, temporal point/range, source AssetFile/checksum, artifact reference/checksum, creator and timestamp. 

- Evidence artifact is immutable once created. 

- Source lineage and exact version are mandatory. 

- Evidence never uses playback proxy as provenance source. 

###### **Commands, queries, APIs and events** 

- Capture/Link Evidence commands; EvidenceCreated. 

###### **Authorization and visibility** 

- evidence.create/view + subject/Project visibility. 

###### **Mandatory verification** 

- Cross-CV evidence rejected. 

- Source checksum lineage preserved. 

- UI FrameDerivative alone does not create Evidence. 

###### **Definition of Done** 

- Evidence identity/provenance schema complete. 

###### **Codex execution prompt** 

```
Implement M0-08-003 Evidence aggregate exactly as specified in the M0 Execution Pack. Preserve all
architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-08-004 - Evidence artifact generation** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 10 - Validation & Evidence|
|Gate|M0|
|Depends on|M0-08-003, M0-03-002|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Generate evidence-grade frame artifacts from exact source media under a versioned profile and integrity chain. 

###### **Implementation contract** 

- EVIDENCE_FRAME_V1 separate from FRAME_UI_V1. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 64 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

- Persist requested_at_ms, actual presented_at_ms, selection strategy, source AssetFile/SHA-256, artifact SHA-256 and generator profile/version. 

- 

- 

- Use source decoding with canonical normalized time. 

- Artifact output private; signed access remains authorized and short-lived. 

###### **Commands, queries, APIs and events** 

- EvidenceFrameGeneratorPort; evidence storage/access contract. 

###### **Authorization and visibility** 

- Worker uses machine source access; reads reauthorize evidence.view. 

###### **Mandatory verification** 

- Artifact reproducible within profile contract. 

- Checksum tamper/missing object detected. 

- Playback proxy/thumbnail not reused without explicit governed policy. 

###### **Definition of Done** 

- Integrity chain test green. 

###### **Codex execution prompt** 

```
Implement M0-08-004 Evidence artifact generation exactly as specified in the M0 Execution Pack.
Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-08-005 - Review integration** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 10 - Validation & Evidence|
|Gate|M0|
|Depends on|M0-08-001..004|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Integrate Accept/Reject/Correct and Evidence actions into Analysis/Editor while keeping mutation authority capability-specific. 

###### **Implementation contract** 

- Content Editor may edit Core without necessarily having final validation authority. 

- Validator can record governed decisions per capabilities. 

- Evidence create/view actions appear only when authorized. 

- Inspector views show provenance/validation state without conflating proposal and truth. 

###### **Commands, queries, APIs and events** 

- Editor/Analysis review command integration. 

###### **Authorization and visibility** 

- Separate appearance/inventory edit, validation.decide and evidence.create capabilities. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 65 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Mandatory verification** 

- User with edit but no validate cannot final-decide. 

- Validator without edit cannot silently alter timing. 

- Evidence action hidden when not needed/authorized. 

###### **Definition of Done** 

- Least-privilege review workflow. 

###### **Codex execution prompt** 

```
Implement M0-08-005 Review integration exactly as specified in the M0 Execution Pack. Preserve all
architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-08-006 - Validation / Evidence hardening** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 10 - Validation & Evidence|
|Gate|M0|
|Depends on|M0-08-001..005|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Prove governed truth, immutable history, source evidence integrity and least-privilege review before M0 <u>product integration.</u> 

###### **Implementation contract** 

- Test accepted/rejected/corrected AI candidate, decision provenance, source-based evidence, UI thumbnail separation, checksum chain, exact CV, permission revoke and audit. 

###### **Mandatory verification** 

- Validation history PASS. 

- Evidence integrity PASS. 

- Least privilege PASS. 

- No Evidence/validation leakage PASS. 

###### **Definition of Done** 

- Zero Validation/Evidence P0. 

- Wave 10 READY. 

###### **Codex execution prompt** 

```
Implement M0-08-006 Validation / Evidence hardening exactly as specified in the M0 Execution Pack.
Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

#### **18. Wave 11 - Product Integration** 

|**Packet**|**Outcome**|
|---|---|
|M0-09-001|Home M0|
|M0-09-002|Projects / PRJ-01|



Internal  /  Implementation Contract  /  24 Aug 2026 

Page 66 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

|**Packet**|**Outcome**|
|---|---|
|M0-09-003|Project Overview / PRO-01|
|M0-09-004|Project Settings SET-01 / SET-02 / SET-07 basic|
|M0-09-005|Entitlement model + M0guards|
|M0-09-006|Publication / Export minimum contracts|
|M0-09-007|Security/ observability/ recoverybaseline|
|M0-09-008|Deployment & environment contract|
|M0-09-009|Global M0 E2E freeze|



##### **M0-09-001 - Home M0** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 11 - Product Integration|
|Gate|M0|
|Depends on|Waves 1-10|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Implement Home as an authorized operational entry point that answers what needs attention, where to continue and what is still running without creating a universal Project Health score. 

###### **Implementation contract** 

- Consume only authorized Project/Operation/Attention/activity projections. 

- Support First Value/empty/healthy states and Quick Create route. 

- Operations awareness uses reduced owner-routed summary. 

- No customizable widgets, universal health score or hidden-module signals in M0. 

###### **Commands, queries, APIs and events** 

- HOME-01A/B/C read model contracts. 

###### **Authorization and visibility** 

- Authorization-before-composition; Acting Organization visible. 

###### **Mandatory verification** 

- Multi-org switch changes Home universe. 

- Hidden operations/modules do not affect attention/counts. 

- Re-entry route authorized. 

###### **Definition of Done** 

- Basic Home M0 complete. 

###### **Codex execution prompt** 

```
Implement M0-09-001 Home M0 exactly as specified in the M0 Execution Pack. Preserve all architecture
invariants, add tests, and finish with pnpm verify.
```

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 67 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

##### **M0-09-002 - Projects / PRJ-01** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 11 - Product Integration|
|Gate|M0|
|Depends on|M0-09-001|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Provide authorized portfolio search/filter/table over Projects without introducing a second portfolio state model. 

###### **Implementation contract** 

- One row = one Project. 

- Search/filter/facets/counts operate only on authorized universe. 

- Show lifecycle separately from derived Situation/Attention and basic capability summaries. 

- Row routing/new Project. 

- Advanced rich portfolio/bulk UX remains after M0 unless structurally required. 

###### **Commands, queries, APIs and events** 

- PRJ-01 portfolio query/search/facet contracts. 

###### **Authorization and visibility** 

- project.portfolio.view and per-row effective access. 

###### **Mandatory verification** 

- No count/facet leakage. 

- Agency multi-client portfolio correct. 

- Archived/attention situations derive correctly. 

###### **Definition of Done** 

- PRJ-01 M0 usable. 

###### **Codex execution prompt** 

```
Implement M0-09-002 Projects / PRJ-01 exactly as specified in the M0 Execution Pack. Preserve all
architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-09-003 - Project Overview / PRO-01** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 11 - Product Integration|
|Gate|M0|
|Depends on|M0-09-001/002|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



Internal  /  Implementation Contract  /  24 Aug 2026 

Page 68 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Objective** 

Compose Project identity, media, Analysis, Core, Operations, Attention and next authorized actions without owning any of those states. 

###### **Implementation contract** 

- Overview is read-model composition only. 

- No ProjectHealth persisted score. 

- Show media readiness, Analysis availability, Core summary, Operations and Needs Attention as authorized. 

- Routing respects need-to-know and exact version context. 

###### **Commands, queries, APIs and events** 

- PRO-01 ProjectOverviewView. 

###### **Authorization and visibility** 

- project.view plus subprojection-specific need-to-know. 

###### **Mandatory verification** 

- Hidden module excluded. 

- Stale operation/snapshot reflected via owner-domain truth. 

- No overview mutation shortcuts. 

###### **Definition of Done** 

- Overview composition stable. 

###### **Codex execution prompt** 

```
Implement M0-09-003 Project Overview / PRO-01 exactly as specified in the M0 Execution Pack.
Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-09-004 - Project Settings SET-01 / SET-02 / SET-07 basic** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 11 - Product Integration|
|Gate|M0|
|Depends on|M0-09-003, Wave 4|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Deliver the M0 Project control-plane surfaces required by the Guide without duplicating owner-domain state. 

###### **Implementation contract** 

- SET-01 General: Project identity/lifecycle/owner read and governed Archive/Restore/Transfer entry points as allowed; owner Organization is not editable dropdown. 

- SET-02 Team & Access: reciprocal projection of ProjectAccessGrant; Membership!=Project access and Organization grant!=individual assignment. 

- SET-07 Audit: basic sensitive-action history. 

- No Project-wide Scope, SERVE or Settings Health score. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 69 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

- Full modules/licenses/integrations/policies settings remain M1+. 

###### **Commands, queries, APIs and events** 

- Settings read composition; sensitive operations routes; audit query. 

###### **Authorization and visibility** 

- project.settings.general.*; project.access/assignments; project.settings.audit.view. 

###### **Mandatory verification** 

- SET-02 equals IAM grant truth. 

- Owner transfer/archive cannot be simple field update when sensitive Operation contract applies. 

- Audit no-secret policy. 

###### **Definition of Done** 

- SET-01/02/07 basic complete. 

###### **Codex execution prompt** 

```
Implement M0-09-004 Project Settings SET-01 / SET-02 / SET-07 basic exactly as specified in the M0
Execution Pack. Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-09-005 - Entitlement model + M0 guards** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 11 - Product Integration|
|Gate|M0|
|Depends on|Wave 1, Analysis teaser/workspace|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Implement the minimum rights model and non-bypass guards required by M0 while deferring polished licensing administration to M1. 

###### **Implementation contract** 

- Model License, LicenseTerm, UsageRightGrant and EffectiveEntitlement. 

- Canonical rights: VIEW, QUERY, SERVE, EXPORT, DOWNLOAD, RETAIN, REUSE. 

- IAM answers actor/resource authority; entitlement answers contractual exploitation rights. 

- M0 teaser policies may expose aggregates, but no detail/export/query endpoint may reconstruct restricted data without required right. 

- Expiry/revocation cuts new licensed operations; persistent artifacts follow defined retain/reuse rules. 

###### **Commands, queries, APIs and events** 

- EffectiveEntitlement resolver/guard; UsageRightRequirement contract. 

###### **Authorization and visibility** 

- Authorization and entitlement are both required where policy applies; neither broadens the other. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 70 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Mandatory verification** 

- No alternate API bypass. 

- VIEW does not imply EXPORT/DOWNLOAD/SERVE. 

- Expired entitlement blocks new detail/regeneration where required. 

###### **Definition of Done** 

- Model + guards only; billing/admin polish deferred. 

- D197 architectural gate satisfied. 

###### **Codex execution prompt** 

```
Implement M0-09-005 Entitlement model + M0 guards exactly as specified in the M0 Execution Pack.
Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-09-006 - Publication / Export minimum contracts** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 11 - Product Integration|
|Gate|M0|
|Depends on|M0-09-005|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Freeze output/rights contracts so M0 cannot accidentally create downloadable or materialized data paths that bypass entitlement. 

###### **Implementation contract** 

- Define ExportRequest, PublicationReference and UsageRightRequirement minimum contracts. 

- Every future output declares required right, source entities/scope and expiry/reconstruction implications. 

- M0 does not need professional reports or broad Publication console. 

- Non-substitutive artifact policy remains enforceable. 

###### **Commands, queries, APIs and events** 

- Output authorization contract/guard stubs. 

###### **Authorization and visibility** 

- EXPORT/DOWNLOAD/SERVE requirements explicit. 

###### **Mandatory verification** 

- Attempted unguarded export route fails architecture test. 

- Rights requirement cannot be omitted for registered output type. 

###### **Definition of Done** 

- No data-output bypass path in M0. 

###### **Codex execution prompt** 

```
Implement M0-09-006 Publication / Export minimum contracts exactly as specified in the M0 Execution
Pack. Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 71 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

##### **M0-09-007 - Security / observability / recovery baseline** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 11 - Product Integration|
|Gate|M0|
|Depends on|Allprior waves|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Reach production-grade baseline for M0 security, auditability, telemetry and failure recovery. 

###### **Implementation contract** 

- Deny-by-default backend security, private storage, signed URL TTL/redaction, CSP, Referrer-Policy, rate limiting and validated secrets/config. 

- Structured logs with correlation IDs; Audit for IAM/access/Project lifecycle/media replacement/Validation/Evidence/rights-sensitive commands. 

- Metrics for API, queue lag, Operations, media validation, proxy, Analysis, frame derivatives and Quick Create funnel. 

- PostgreSQL backups, tested restore, object durability, outbox/queue reconciliation and restart recovery. 

- Third-party analytics receives no sensitive titles/filenames/signed URLs by default. 

###### **Commands, queries, APIs and events** 

- Audit writer/query, observability packages, runbooks. 

###### **Authorization and visibility** 

- Audit read restricted by underlying resource/sensitivity. 

###### **Mandatory verification** 

- Backup restore drill. 

- Secrets/log redaction scan. 

- Restart/reconciliation suite. 

- No-existence security suite. 

###### **Definition of Done** 

- Security checklist green. 

- Restore proven, not merely configured. 

###### **Codex execution prompt** 

```
Implement M0-09-007 Security / observability / recovery baseline exactly as specified in the M0
Execution Pack. Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-09-008 - Deployment & environment contract** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 11 - Product Integration|
|Gate|M0|
|Depends on|DEV-000,M0-09-007|



Internal  /  Implementation Contract  /  24 Aug 2026 

Page 72 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

|**Field**|**Value**|
|---|---|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Define reproducible local/test/staging/production deployments without environment-specific domain semantics. 

###### **Implementation contract** 

- Environments local/test/staging/production with web/api/worker/Postgres/Redis/S3-compatible storage. 

- MinIO allowed local/test. 

- Forward-reviewed migrations and startup configuration validation. 

- Health/readiness probes for API/workers/dependencies. 

- Feature configuration may vary; authorization/domain semantics may not. 

###### **Commands, queries, APIs and events** 

- CI/CD pipelines, deployment manifests/runbook. 

###### **Authorization and visibility** 

- Production secrets via secret manager; no credentials in repository. 

###### **Mandatory verification** 

- Fresh environment bootstrap. 

- Migration from previous revision. 

- Health probe and worker recovery. 

###### **Definition of Done** 

- Staging deploy green. 

- Reproducible rollback/forward-fix runbook. 

###### **Codex execution prompt** 

```
Implement M0-09-008 Deployment & environment contract exactly as specified in the M0 Execution Pack.
Preserve all architecture invariants, add tests, and finish with pnpm verify.
```

##### **M0-09-009 - Global M0 E2E freeze** 

|**Field**|**Value**|
|---|---|
|Wave|Wave 11 - Product Integration|
|Gate|M0|
|Depends on|Allpriorpackets|
|Owner|Engineering|
|Normative basis|Guía Maestra v2.21 + frozen handof|



###### **Objective** 

Prove the complete M0 product contract and architecture invariants across happy path, agency access, media complexity, revocation, rights and recovery. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 73 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Implementation contract** 

- Run the canonical 12 E2E journeys and global P0 matrix. 

- Run architecture drift detector, source-of-truth audit, state-model audit, IAM/media/reliability/rights hardening. 

- No release success may depend on temporary bypass of licensing/authorization/exact version binding/Audit. 

###### **Mandatory verification** 

- M0 Gate 1 Organizational Isolation PASS. 

- M0 Gate 2 Exact Content & Temporal Integrity PASS. 

- M0 Gate 3 Durable Execution PASS. 

- M0 Gate 4 Content Intelligence Integrity PASS. 

- M0 Gate 5 Validation, Rights & Need-to-Know PASS. 

- M0 Gate 6 End-to-End Product PASS. 

###### **Definition of Done** 

- Zero global P0. 

- Full CI + pnpm verify green. 

- Staging journey green. 

- Backup restore green. 

- M0 implementation candidate for release/freeze. 

###### **Codex execution prompt** 

```
Implement M0-09-009 Global M0 E2E freeze exactly as specified in the M0 Execution Pack. Preserve all
architecture invariants, add tests, and finish with pnpm verify.
```

#### **19. Cross-cutting security baseline** 

- Deny by default and authorize before composing a response, count, search result or navigation tree. 

- No user-supplied organization/project IDs are trusted as authority; every target is resolved against Acting Organization/effective grants. 

- Private object storage only. Browser media/image access uses short-lived signed transport credentials; future external serving is a separate security product. 

- Signed URLs, object keys, credentials, raw provider secrets and access tokens never enter logs, analytics, Audit before/after payloads or client error messages. 

- Use CSP and Referrer-Policy suitable for media/app shell; CORS is narrow and intentional. 

- Rate limiting and abuse controls at authentication, invitation, upload initiation, signed-access issuance and expensive derivative endpoints. 

- No-existence-safe authorization for guessed Project, ContentVersion, Operation, derivative, Evidence and hiddenmodule identifiers. 

- Least-privilege service credentials for API/worker/storage/queue. Human signed URLs are never reused by workers. 

#### **20. Observability, Audit and recovery** 

|**Concern**|**Minimum M0 contract**|
|---|---|
|Structured logs|correlation_id, request/operation IDs, safe codes; no<br>secrets/signed URLs|
|Audit|sensitive commands: IAM/access, Project lifecycle/ownership,<br>media replacement, Validation, Evidence, rights-sensitive actions|
|Metrics|API, queue lag, Operation duration/retry, media validation,<br>proxy, Analysis, frame cache/failure,Quick Create funnel|
|Backups|PostgreSQL scheduled backup+ documented retention|
|Restore|tested restore into clean environment before M0 freeze|



Internal  /  Implementation Contract  /  24 Aug 2026 

Page 74 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

|**Concern**|**Minimum M0 contract**|
|---|---|
|Queue recovery|transactional outbox + reconciliation; at-least-once-safe handlers|
|Object recovery|private immutable sources; derivative orphan/missing-object<br>reconciliationpolicy|
|Runbooks|worker/API/Redis/storage dependencyfailure and recovery|



#### **21. Architecture drift rules** 

###### **CI policy** 

Architecture drift is a release failure even when functional tests pass. Structural shortcuts accumulate faster than UI defects and are harder to reverse. 

```
Search/AST/architecture rules must flag:
- ProjectMember / Project.members / User.role
- owner_user_id
- currentVideo/current_content_version used as authority
- Project.video_url or public workspace media
- generic Project.status / isValidated
- avg_fps exact VFR frame mapping
- role-name based authorization/navigation
- AI -> accepted Core direct write
- UI thumbnail -> Evidence shortcut
- module-local permission engines
- output/export route without UsageRightRequirement
```

- A flagged legitimate exception requires ADR; renaming the shortcut does not make it compliant. 

- Every Wave report includes an architecture-drift result. 

#### **22. Codex execution protocol and reporting** 

##### **Packet execution loop** 

```
READ invariants + prerequisites
-> IMPLEMENT schema/contracts/domain/API/UI as packet requires
-> MIGRATE
-> TEST unit/integration/E2E/security
-> pnpm verify
-> PACKET REPORT
-> only then next packet
```

##### **Mandatory packet report** 

```
PACKET: <id>
STATUS: PASS | FAIL
Files changed:
Schema changes:
Contracts / commands / queries / API:
Events:
Authorization rules:
Tests: unit / integration / E2E / security
pnpm verify: PASS | FAIL
Known limitations:
Open P0:
Open P1:
Architecture decision required: NONE | <ADR candidate>
```

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 75 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

##### **Mandatory Wave report** 

```
WAVE: <n>
Packets complete: PASS | FAIL
Schema integrity: PASS | FAIL
Authorization / need-to-know: PASS | FAIL
Source-of-Truth compliance: PASS | FAIL
State-model compliance: PASS | FAIL
E2E: PASS | FAIL
Security / performance smoke: PASS | FAIL
Open P0:
Open P1:
Architecture drift: NONE | ...
GATE: READY | NOT READY
```

#### **23. Global E2E matrix** 

|**ID**|**Journey**|**Acceptance**|
|---|---|---|
|E2E-01|Studio happy path|Login -> Acting Organization -> Quick Create<br>-> upload -> Analysis -> Inventory -> Editor -><br>Validate -> Evidence -> re-entry.|
|E2E-02|Multi-Organization user|Same User acts in Organizations A/B with<br>independent memberships, assignments<br>andportfolios; no union.|
|E2E-03|Multi-client agency|Agency member works on Projects owned by<br>multiple studios via organization ceilings +<br>individual assignments.|
|E2E-04|Need-to-know|A vertical-limited user cannot discover<br>unrelated module tabs, counts, search,<br>Activityor Operations.|
|E2E-05|Invalid media recovery|Invalid technical media -> permitted pre-<br>analysis replacement -> same<br>Project/Content/CV/Asset -> new source -><br>automatic recovery.|
|E2E-06|Complex/VFR media|MOV/H.265/VFR/non-zero source start -><br>source valid -> proxy -> canonical ms Editor<br>-> source frame extraction.|
|E2E-07|Access revoked during processing|Owner-routed process continues; user loses<br>new access, renewals and discoverability<br>immediately.|
|E2E-08|Infrastructure recovery|Queue/API/worker/Redis restart -><br>outbox/reconciliation -> no lost work or<br>duplicate business efect.|
|E2E-09|AI proposal to Core truth|DetectionCandidate -><br>inspect/correct/accept -><br>InventoryItem/Appearance with provenance;<br>proposal remains history.|
|E2E-10|Evidence integrity|FRAME_UI thumbnail is not Evidence;<br>EVIDENCE_FRAME_V1 preserves exact<br>source/time/checksum chain.|
|E2E-11|Teaser/no entitlement|Aggregate value signals visible where policy<br>allows; exploitable detail/export/query<br>reconstruction blocked.|
|E2E-12|Operational re-entry|Home/Projects/Overview reconstruct project<br>situation, operation and exact-version work<br>without browser-local state.|



Internal  /  Implementation Contract  /  24 Aug 2026 

Page 76 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

#### **24. Global P0 / P1 policy** 

###### **Release rule** 

M0 cannot be declared complete with any global P0. Local module P0s that the Guide places after M0 remain Definition of Complete for that later module, not blockers for the M0 release gate. 

|**Global P0 blockers**|**Examples**|
|---|---|
|Authorization leakage|cross-org/project, same-org implicit project access, collaboration<br>ceilingbypass, hidden-module discovery|
|Creation integrity|partial Quick Create, duplicate Project, upload claimed by wrong<br>context|
|Temporal/media integrity|implicit current version, source overwrite, incorrect VFR<br>mapping, stale derivative/current source confusion<br>|
|Durable execution|lost Operation after restart, duplicate business efect, retry<br>erasinghistory|
|Content intelligence integrity|AI proposal silently becomes accepted Core, snapshot/version<br>mismatch|
|Validation/Evidence|decision without provenance, Evidence without exact<br>source/checksum|
|Rights|teaser/detail/export/serving alternate route bypasses<br>entitlement or UsageRight|



- P1 examples: richer diagnostics, advanced Saved Views, full taxonomy editor, VFR timing index, frame-by-frame controls, waveform, hardware transcoding, full provider cast reconciliation, advanced reports, custom roles, IAM certification, OPS-03. 

- Do not pull Deferred work into M0 merely because infrastructure seems convenient. 

#### **25. Final M0 architecture audit** 

7. User can belong to multiple Organizations without authority union. 

8. Acting Organization is explicit and request/session-scoped. 

9. Role package does not imply Project access. 

10. ProjectAccessGrant remains the sole Project-scoped authority. 

11. Collaborator authority is always an intersection with the organization ceiling. 

12. Need-to-know governs navigation and every secondary discovery surface. 

13. Project owner is always an Organization. 

14. Every temporal truth binds an exact ContentVersion. 

15. No “current video/version” authority exists inside temporal commands. 

16. Source AssetFile bytes are immutable. 

17. Playback proxy, UI FrameDerivative and Evidence remain separate concepts. 

18. Canonical product time is normalized integer milliseconds with half-open ranges. 

19. VFR never uses average FPS as exact frame truth. 

20. Operation is not the business result. 

21. Duplicate asynchronous delivery is business-effect safe. 

22. DetectionCandidate is not accepted Inventory/Appearance truth. 

23. InventoryItem and Appearance remain distinct identity/time concepts. 

24. Validation history is immutable and provenance-preserving. 

25. Technical/workspace readiness is not editorial lifecycle. 

26. Entitlement/UsageRight cannot be bypassed through alternate APIs or outputs. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 77 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

#### **26. M0 exit gates** 

|**Gate**|**Name**|**Pass condition**|
|---|---|---|
|M0-GATE-1|Organizational Isolation|Multi-org, multi-client agency<br>collaboration and least privilege are<br>correct and leakage-free.|
|M0-GATE-2|Exact Content & Temporal Integrity|Project->Content->ContentVersion, media<br>lineage and normalized temporal truth<br>are stable and exact.|
|M0-GATE-3|Durable Execution|Operation/Step/Attempt/Log/Result<br>survive retries, waits, restarts and<br>duplicate delivery.|
|M0-GATE-4|Content Intelligence Integrity|Analysis proposals become governed<br>Core only through explicit bridges and<br>provenance.|
|M0-GATE-5|Validation, Rights & Need-to-Know|Validation/Evidence/access/entitlement<br>boundaries are non-bypassable.<br>|
|M0-GATE-6|End-to-End Product|Authorized user completes the ofcial M0<br>exit journey and re-enters from<br>Home/Projects/Overview without<br>scope/version/auth loss.|



#### **27. Traceability matrix** 

|**Guide decision**|**Implementation invariant**|**Packets/Wave**|**Verifcation**|
|---|---|---|---|
|D187-D189|M0 frst release hierarchy /<br>Understand the Content|Entire pack; Waves 0-11|All M0 gates|
|D193|Manual/human-in-loop valid; AI<br>autonomynotgate|Waves 8-10|E2E-09; M0-GATE-4|
|D194|Clearance-relevant Analysis teaser<br>allowed before full Clearance|M0-07-005/007/008|Teaser/non-bypass tests|
|D196|Catalog/Operations may precede<br>full UI|Waves 4 and 7|Wave gates|
|D197|Licensing, authorization, exact<br>version binding cannot be<br>bypassed|Waves 1-11|M0-GATE-1/2/5|
|D198|Next artifact is Development<br>Handof by vertical<br>slices/dependencies|This Execution Pack|Packet/Wave reporting|
|Guide §70|Operation/Step/Attempt/Log/<br>Result; waiting/retry/compensation|Wave 4|OPS gates|
|Guide Editor backlog|Temporal Editor, SceneStructure,<br>Evidence, Validation,<br>Candidate/Core distinction|Wave 8/10|Editor + validation gates|
|Guide M0 Catalog|Canonical<br>Brand/Product/Person/Character +<br>CanonicalLink|Wave 7|Catalog boundary tests|
|Guide M0 Settings|SET-01/02/07 basic|M0-09-004|Settings access/audit tests|



#### **28. Appendix A - Canonical code-level contracts** 

```
// Temporal
TimecodeMs; DurationMs; TimeRange { start_ms, end_ms }
```

```
// Identity / access
RequestContext { user_id, acting_organization_id }
```

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 78 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

```
EffectiveAccessDecision { allowed, effective_capabilities, reasons, visibility_scope }
```

```
// Media
MediaTechnicalProfile
DerivedAssetFile<PLAYBACK_PROXY>
FrameDerivative { source_asset_file_id, requested_at_ms, presented_at_ms, strategy,
profile_version }
MediaReadinessView
// Operations
Operation -> OperationStep -> OperationAttempt
OperationLog
OperationResult { result_type, result_id }
// Analysis/Core
AnalysisRun -> DetectionCandidate -> AnalysisSnapshot
InventoryItem -> Appearance
Scene
ValidationDecision
Evidence
CanonicalEntity ← CanonicalLink ← InventoryItem
// Rights
License -> LicenseTerm -> UsageRightGrant -> EffectiveEntitlement
```

#### **29. Appendix B - Source references** 

- IwantIt - Guía Maestra de Producto y Plataforma v2.21 RC1, Global MVP Execution Model, 23 Aug 2026. Normative basis for M0/M1/M2 hierarchy, M0 required scope, release criteria, D187-D198 and precedence over local P0 for release planning. 

- IwantIt v2.20 Clean Master sections embedded in v2.21. Normative basis for Effective Access, need-to-know, Project Settings, Operations §70, Editor commands, Catalog canonical model, Privacy/Data Visibility and state/domain semantics. 

- IWantIt Identidad Gráfica, Séptimo Barcelona, 22 Sep 2023. Visual basis for brand colors and overall document identity. The document uses the brand palette #1B365D, #D7D2CB, #147BD1 and #CF7F00; fonts use compatible installed substitutes rather than distributing brand font files. 

###### **Source discipline** 

Where this pack introduces an implementation-specific choice not mandated by the Guide - for example TypeScript/Next/Nest/PostgreSQL/Prisma/BullMQ/MinIO - it is explicitly an Execution Handoff decision for the <u>greenfield vNext, not a claim that the Guide prescribes that technology.</u> 

## **M0 ARCHITECTURE** 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 79 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

END OF v1.0 BASELINE - REFER TO PART II v1.1 OVERLAY 

Historical v1.0 next-action note superseded by the revised execution order in Part II. 

IwantIt / M0 Greenfield Development Execution Pack v1.1 - Part I historical baseline 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 80 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

# **PART II - v1.1 Normative Freeze Remediation Overlay** 

###### **Supersession rule** 

This Part II is normative for v1.1. Where it conflicts with a packet or registry in Part I/v1.0, Part II replaces that clause. Unaffected v1.0 content remains valid. No implementation shortcut may resolve a conflict. 

This overlay applies FRZ-01 through FRZ-15 after the independent external product/architecture audit. It closes authentication/bootstrap, support access, Quick Create policy, replacement equivalence, SceneStructure, taxonomy unification, Inventory/Appearance completion, dimensional Validation, Analysis reproducibility, entitlement provisioning, screen contracts, WorkContext, NFR and traceability gaps. 

#### **R0. Revised execution order** 

|**WAVE**|**PACKETS / PURPOSE**|
|---|---|
|0A|DEV-000 - Greenfield repository bootstrap|
|0B|DEV-001 - Optimistic concurrency foundation; AUTH-00-001..007 -<br>authentication and identity bootstrap|
|1|M0-01 IAM + M0-01-011 Support Access integration|
|2|Project / Content / ContentVersion foundation|
|3|Upload / source media / technical profile / temporal / secure access + M0-<br>03-008 Technical Replacement Assessment|
|4|Operation -> Step -> Attempt -> Log -> Result durable runtime|
|5|Playback proxy / FrameDerivative / MediaReadiness|
|6|Quick Create / NPW + M0-02-010 InitialAnalysisPolicyVersion|
|7|M0-05-000 Unified Taxonomy + Inventory / Canonical identity / cast / split<br>& lineage|
|8|SceneStructure / Appearance / Editor / minimum ContextualRelationship|
|9|Analysis input manifests / candidates / contexts / vertical relevance /<br>currentness / workspace|
|10|Dimensional Validation / Evidence|
|11|Home / Projects / Overview / Settings / entitlement guards / security-<br>recovery / global E2E|



#### **R1. Wave 0B - Authentication Foundation** 

###### **Technology decision** 

M0 uses WorkOS AuthKit Hosted UI behind AuthenticationProviderPort. IwantIt remains owner of 

User/AuthIdentity/ApplicationSession and all IAM. Before production, WorkOS DPA/subprocessor/security/residency review is required; Auth0 EU is the approved adapter fallback if strict EU identity-data residency is contractually required and unavailable. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 81 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

##### **AUTH-00-001 - Auth domain boundary + User/AuthIdentity** 

**Objective.** Create the provider-independent identity boundary that resolves a verified external identity to an IwantIt User. 

**Scope.** Domain model and adapter contract only; no Organization or Project authority is derived from the identity provider. 

###### **Invariants.** 

- User.id is the only product human identity key. 

- Email is not authorization. 

- UNIQUE(provider, provider_subject). 

- Provider Organization/Role/Permission objects are not imported as authority. 

- User SUSPENDED is global account state and distinct from Membership suspension. 

###### **Model / schema contract** 

```
User(id, display_name, account_state ACTIVE|SUSPENDED, timestamps)
AuthIdentity(id, user_id FK, provider, provider_subject, email_normalized, email_verified,
created_at, last_authenticated_at)
```

```
UNIQUE(provider, provider_subject)
```

```
AuthenticationProviderPort { startAuthentication; completeAuthentication; getVerifiedIdentity;
requestVerification; logoutProviderSession; revokeProviderSessions }
```

###### **Commands** 

- ResolveOrCreateVerifiedUser(provider identity) 

- SuspendUserAccount / ReactivateUserAccount are defined in AUTH-00-006, not here. 

###### **Queries / read models** 

- Resolve AuthIdentity by provider subject 

- Get current User account state 

###### **API / contract** 

```
Provider callback is adapter-specific. Domain APIs must receive AuthenticatedPrincipal rather than
raw provider tokens.
```

###### **Events** 

- UserCreated (application/domain as appropriate) 

- AuthIdentityLinked 

###### **Authorization and visibility** 

- This packet establishes authentication identity only; no Project/Organization data is accessible from it. 

###### **Errors / conflicts** 

- AUTH_IDENTITY_NOT_VERIFIED 

- USER_ACCOUNT_SUSPENDED 

- AUTH_IDENTITY_CONFLICT 

###### **Acceptance tests** 

- Duplicate provider callback resolves one User/AuthIdentity. 

- Same email on unrelated provider identity does not silently merge Users. 

- Provider subject never appears in business authority tables. 

###### **Definition of Done** 

- Schema constraints green. 

- Provider-neutral unit tests green. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 82 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

- No WorkOS SDK import outside authentication adapter package. 

###### **Codex execution instruction** 

```
Implement the provider-independent identity boundary. Do not add user.role, provider organization
roles, or email-based authorization.
```

##### **AUTH-00-002 - Application Session + security** 

**Objective.** Create IwantIt-owned application sessions and Acting Organization context after provider authentication. 

**Scope.** Secure server-side session contract, rotation/revocation, Acting Organization persistence and request principal. 

###### **Invariants.** 

- Acting Organization is session/request-scoped, never User.current_organization_id. 

- Session never stores authoritative capabilities, Project IDs or entitlements. 

- Membership/grant changes take effect without re-login. 

- Provider tokens are server-side secrets and never localStorage/URL/Audit data. 

###### **Model / schema contract** 

```
ApplicationSession(session_id, user_id, acting_organization_id?, authenticated_at, last_seen_at,
expires_at, authentication_strength, revoked_at?)
```

```
AuthenticatedPrincipal(user_id, auth_session_id, authenticated_at, authentication_strength)
RequestContext(principal, acting_organization_id?)
```

###### **Commands** 

- CreateApplicationSession 

- RotateApplicationSession 

- RevokeApplicationSession 

- SetActingOrganization 

###### **Queries / read models** 

- GET /auth/session 

- GET /me/context 

###### **API / contract** 

```
GET /auth/session
POST /auth/logout
GET /me/context
POST /me/acting-organization { organization_id }
```

###### **Events** 

- Application session events are security telemetry by default; sensitive account changes are audited. 

###### **Authorization and visibility** 

- SetActingOrganization requires ACTIVE temporally-valid Membership. 

- 0 active memberships -> onboarding; 1 may auto-select; >1 requires explicit choice. 

###### **Errors / conflicts** 

- ACTING_ORGANIZATION_REQUIRED 

- MEMBERSHIP_NOT_ACTIVE 

- SESSION_REVOKED 

###### **Acceptance tests** 

- Multi-org chooser required. 

- Membership suspension invalidates current Acting Org without global User logout. 

- Project grant revoke applies on next request without re-login. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 83 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

- Cookies are Secure/HttpOnly/SameSite and CSRF protected where applicable. 

###### **Definition of Done** 

- Session revocation works across API instances. 

- No long-lived capability snapshot in session. 

###### **Codex execution instruction** 

```
Implement local application sessions and Acting Organization. Treat authentication and authorization
as separate layers.
```

##### **AUTH-00-003 - Registration / verification / recovery** 

**Objective.** Connect hosted AuthKit sign-up/sign-in/verification/recovery to the provider-independent User boundary. 

**Scope.** Hosted authentication UX and callback integration; no custom credential storage. 

###### **Invariants.** 

- Verified identity is required before Organization bootstrap or invitation acceptance. 

- Account recovery must not enumerate whether an account exists. 

- return_to is internal, integrity-checked and reauthorized after login. 

- Rate-limit sign-in/sign-up/verification/recovery endpoints. 

###### **Commands** 

- StartHostedAuthentication 

- CompleteHostedAuthentication 

###### **Queries / read models** 

- Provider-hosted state + local session result 

###### **API / contract** 

```
Use WorkOS AuthKit Hosted UI in M0; callback creates/resolves AuthIdentity and ApplicationSession.
Exact callback paths are adapter configuration, not domain contract.
```

###### **Authorization and visibility** 

- Unauthenticated surface only. After completion, all product data uses local authorization. 

###### **Errors / conflicts** 

- AUTHENTICATION_FAILED 

- AUTH_IDENTITY_NOT_VERIFIED 

- USER_ACCOUNT_SUSPENDED 

###### **Acceptance tests** 

- New prospect registration. 

- Existing login. 

- No account enumeration. 

- Open redirect attempt rejected. 

###### **Definition of Done** 

- No password hash/verification token infrastructure in IwantIt. 

- Provider failure returns safe user-facing state. 

###### **Codex execution instruction** 

```
Use WorkOS AuthKit Hosted UI through the adapter. Do not build first-party password storage.
```

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 84 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

##### **AUTH-00-004 - Organization bootstrap** 

**Objective.** Allow a verified User with zero active Memberships to create the first Organization and become its Organization Admin. 

**Scope.** Minimal onboarding only. 

###### **Invariants.** 

- Only verified authenticated Users with zero active Memberships may use self-service bootstrap in M0. 

- Organization name is the only required business input. 

- No autojoin by email domain. 

- Operation is atomic and idempotent. 

- RolePackageVersion ORGANIZATION_ADMIN v1 is referenced explicitly. 

###### **Model / schema contract** 

```
Bootstrap transaction: Organization + ACTIVE OrganizationMembership(ORGANIZATION_ADMIN v1) + Acting
Organization + Audit/outbox.
```

###### **Commands** 

- BootstrapOrganization 

###### **Queries / read models** 

- Bootstrap eligibility projection 

###### **API / contract** 

```
POST /organizations/bootstrap { name, idempotency_key }
```

###### **Events** 

- OrganizationCreated 

- MembershipCreated 

###### **Authorization and visibility** 

- Verified User, zero active Memberships. 

###### **Errors / conflicts** 

- BOOTSTRAP_NOT_ELIGIBLE 

- CONCURRENT_MODIFICATION 

- IDEMPOTENCY_CONFLICT 

###### **Acceptance tests** 

- Double submit returns same Organization. 

- User with existing active Membership cannot self-proliferate tenants. 

- Same email domain never joins another Organization automatically. 

###### **Definition of Done** 

- Transaction failure leaves no partial Organization/Membership. 

###### **Codex execution instruction** 

```
Implement first-organization bootstrap as one transaction. Owner/admin authority must come from
Membership, not created_by shortcuts.
```

##### **AUTH-00-005 - Invitation identity integration** 

**Objective.** Make invitations work for existing or new verified identities without turning the invite token into authority. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 85 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Scope.** Continuation across hosted authentication and atomic acceptance. 

###### **Invariants.** 

- Invitation is pre-authority until accepted. 

- DB stores token hash only. 

- Current verified identity must match invited identity. 

- ACTIVE existing Membership -> already satisfied; SUSPENDED/ENDED is never silently reactivated. 

- Accepting does not silently switch Acting Organization when another context is active. 

###### **Commands** 

- IssueInvitation (existing M0-01) 

- AcceptInvitation 

###### **Queries / read models** 

- Safe invitation preview sufficient to continue authentication 

###### **API / contract** 

```
POST /invitations/:id/accept (authenticated, verified invited identity)
```

###### **Events** 

- InvitationAccepted 

- MembershipCreated 

###### **Authorization and visibility** 

- Token grants only continuation/acceptance capability, not Organization/Project read. 

###### **Errors / conflicts** 

- INVITATION_EXPIRED 

- INVITATION_REVOKED 

- INVITATION_WRONG_IDENTITY 

- INVITATION_ALREADY_SATISFIED 

###### **Acceptance tests** 

- Invited new user -> register -> verify -> accept. 

- Wrong identity denied. 

- Expired/revoked/replay denied. 

###### **Definition of Done** 

- Acceptance transaction is atomic and idempotent. 

###### **Codex execution instruction** 

```
Integrate invitation continuation with AuthKit without creating authority before Membership
acceptance.
```

##### **AUTH-00-006 - Global account suspension + session invalidation** 

**Objective.** Provide exceptional global identity shutdown distinct from Membership suspension. 

**Scope.** Security/admin command and session revocation. 

###### **Invariants.** 

- Global suspension revokes all application sessions. 

- Memberships and Project grants remain historical and are not rewritten. 

- Reactivation does not revive expired/revoked Memberships/grants. 

- Ordinary customer offboarding uses Membership suspension/end, not global User suspension. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 86 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Commands** 

- SuspendUserAccount 

- ReactivateUserAccount 

###### **Queries / read models** 

- Internal account security view 

###### **Events** 

- UserAccountSuspended 

- UserAccountReactivated 

###### **Authorization and visibility** 

- internal capability user.account.suspend only; Audit mandatory. 

###### **Errors / conflicts** 

- USER_ALREADY_SUSPENDED 

- USER_NOT_SUSPENDED 

###### **Acceptance tests** 

- Global suspension blocks new product access immediately. 

- Membership suspension affects only one Organization. 

###### **Definition of Done** 

- Revocation propagates to all active sessions. 

###### **Codex execution instruction** 

```
Implement global account suspension as security control, not customer membership management.
```

##### **AUTH-00-007 - Authentication E2E hardening** 

**Objective.** Close the authentication/bootstrap surface before IAM Wave 1 can proceed. 

**Scope.** E2E, abuse, recovery and security tests. 

###### **Invariants.** 

- No Product/IAM Wave proceeds while authentication isolation is red. 

- All callbacks/return routes reauthorize target context. 

###### **Acceptance tests** 

- AUTH-001 prospect -> verified User -> Organization Admin 

- AUTH-002 existing one-org login 

- AUTH-003 multi-org explicit selection 

- AUTH-004 invited new person flow 

- AUTH-005 existing correct user accepts 

- AUTH-006 wrong identity denied 

- AUTH-007 expired/revoked/replayed invitation denied 

- AUTH-008 Membership suspension != User suspension 

- AUTH-009 global suspension terminates product access 

- AUTH-010 Acting Org becomes invalid mid-session 

- AUTH-011 Project grant revoke applies without re-login 

- AUTH-012 return_to cannot bypass access 

- AUTH-013 recovery non-enumeration 

- AUTH-014 bootstrap idempotency 

- AUTH-015 same-domain no autojoin 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 87 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Definition of Done** 

- AUTH-GATE green. 

- No P0 open. 

- Security scan confirms no credential/token leakage. 

###### **Codex execution instruction** 

```
Run the full authentication hardening suite. Stop the Wave if any authority or identity-isolation
test fails.
```

#### **R2. Capability Registry v1.1 and RolePackageVersion v1** 

|**CAPABILITY**|**SCOPE**|**PROJECT DELEGABLE**|**SURFACE**|**LEVEL**|
|---|---|---|---|---|
|organization.view|ORGANIZATION|False|Organization|READ|
|organization.members.view|ORGANIZATION|False|Team & Access|READ|
|organization.members.manage|ORGANIZATION|False|Team & Access|MANAGE|
|organization.invitations.manage|ORGANIZATION|False|Team & Access|MANAGE|
|project.create|ORGANIZATION|False|Projects/New Project|OPERATE|
|project.portfolio.view|ORGANIZATION|False|Projects|READ|
|project.view|PROJECT|True|Project|READ|
|project.manage|PROJECT|True|Project|MANAGE|
|project.archive|PROJECT|True|Settings|MANAGE|
|project.assignments.view|PROJECT|True|Team & Access/Settings|READ|
|project.assignments.manage|PROJECT|True|Team & Access/Settings|MANAGE|
|project.access.view|PROJECT|False|Team & Access|READ|
|project.access.manage|PROJECT|False|Team & Access|MANAGE|
|content.view|PROJECT|True|Content|READ|
|content.manage|PROJECT|True|Content|MANAGE|
|asset.view|PROJECT|True|Media|READ|
|asset.upload|PROJECT|True|Media|OPERATE|
|asset.manage|PROJECT|True|Media|MANAGE|
|analysis.view|PROJECT|True|Analysis|READ|
|analysis.run|PROJECT|True|Analysis|OPERATE|
|analysis.review|PROJECT|True|Analysis|OPERATE|
|editor.view|PROJECT|True|Editor|READ|
|inventory.view|PROJECT|True|Editor/Analysis|READ|
|inventory.create|PROJECT|True|Editor/Analysis|OPERATE|
|inventory.edit|PROJECT|True|Editor/Analysis|OPERATE|
|inventory.merge|PROJECT|True|Editor/Analysis|OPERATE|
|inventory.split|PROJECT|True|Editor/Analysis|OPERATE|
|inventory.archive|PROJECT|True|Editor/Analysis|OPERATE|
|appearance.view|PROJECT|True|Editor/Analysis|READ|
|appearance.create|PROJECT|True|Editor/Analysis|OPERATE|
|appearance.edit|PROJECT|True|Editor/Analysis|OPERATE|



Internal  /  Implementation Contract  /  24 Aug 2026 

Page 88 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

|**CAPABILITY**|**SCOPE**|**PROJECT DELEGABLE**|**SURFACE**|**LEVEL**|
|---|---|---|---|---|
|appearance.archive|PROJECT|True|Editor/Analysis|OPERATE|
|scene.view|PROJECT|True|Editor|READ|
|scene.create|PROJECT|True|Editor|OPERATE|
|scene.edit|PROJECT|True|Editor|OPERATE|
|scene.split|PROJECT|True|Editor|OPERATE|
|scene.merge|PROJECT|True|Editor|OPERATE|
|relationship.view|PROJECT|True|Analysis/Editor|READ|
|relationship.edit|PROJECT|True|Analysis/Editor|OPERATE|
|validation.view|PROJECT|True|Analysis/Editor|READ|
|validation.decide|PROJECT|True|Analysis/Editor|OPERATE|
|evidence.view|PROJECT|True|Analysis/Editor|READ|
|evidence.create|PROJECT|True|Analysis/Editor|OPERATE|
|catalog.view|PROJECT|True|Catalog/Analysis|READ|
|catalog.link|PROJECT|True|Analysis/Editor|OPERATE|
|operations.view|PROJECT|True|Operations/embedded|READ|
|operations.retry|PROJECT|True|Operations/embedded|OPERATE|
|operations.cancel|PROJECT|True|Operations/embedded|OPERATE|
|project.settings.general.view|PROJECT|True|Settings|READ|
|project.settings.general.manage|PROJECT|True|Settings|MANAGE|
|project.settings.audit.view|PROJECT|True|Settings/Audit|READ|
|internal.platform.view|INTERNAL_PLATFORM|False|Administration|READ|
|internal.platform.manage|INTERNAL_PLATFORM|False|Administration|MANAGE|
|support.access.view|INTERNAL_PLATFORM|False|Administration|READ|
|support.access.issue|INTERNAL_PLATFORM|False|Administration|MANAGE|
|support.access.revoke|INTERNAL_PLATFORM|False|Administration|MANAGE|
|support.access.use|INTERNAL_PLATFORM|False|Customer support|OPERATE|
|pilot.entitlement.issue|INTERNAL_PLATFORM|False|Administration|MANAGE|
|user.account.suspend|INTERNAL_PLATFORM|False|Administration|MANAGE|



**ROLE PACKAGE EXACT M0 v1 CAPABILITY SET** organization.view, organization.members.view, organization.members.manage, organization.invitations.manage, project.create, project.portfolio.view, project.view, project.manage, project.archive, project.assignments.view, project.assignments.manage, ORGANIZATION_ADMIN project.access.view, project.access.manage, content.view, asset.view, analysis.view, editor.view, inventory.view, appearance.view, scene.view, relationship.view, validation.view, evidence.view, catalog.view, operations.view, operations.retry, operations.cancel, project.settings.general.view, project.settings.general.manage, project.settings.audit.view organization.view, project.create, project.portfolio.view, project.view, project.manage, project.archive, project.assignments.view, project.assignments.manage, project.access.view, project.access.manage, content.view, content.manage, asset.view, asset.upload, asset.manage, analysis.view, analysis.run, analysis.review, editor.view, PROJECT_MANAGER inventory.view, inventory.create, inventory.edit, inventory.merge, inventory.split, inventory.archive, appearance.view, appearance.create, appearance.edit, appearance.archive, scene.view, scene.create, scene.edit, scene.split, scene.merge, relationship.view, relationship.edit, validation.view, evidence.view, evidence.create, catalog.view, catalog.link, operations.view, operations.retry, operations.cancel, 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 89 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

|**ROLE PACKAGE**|**EXACT M0 v1 CAPABILITY SET**|
|---|---|
||project.settings.general.view, project.settings.general.manage, project.settings.audit.view|
|CONTENT_EDITOR|project.portfolio.view, project.view, content.view, content.manage, asset.view, asset.upload,<br>editor.view, inventory.view, inventory.create, inventory.edit, inventory.merge, inventory.split,<br>inventory.archive, appearance.view, appearance.create, appearance.edit,<br>appearance.archive, scene.view, scene.create, scene.edit, scene.split, scene.merge,<br>relationship.view, relationship.edit, analysis.view, analysis.review, validation.view,<br>evidence.view, evidence.create, catalog.view, catalog.link, operations.view|
|VALIDATOR|project.portfolio.view, project.view, content.view, asset.view, editor.view, inventory.view,<br>inventory.edit, appearance.view, appearance.edit, scene.view, scene.edit, relationship.view,<br>relationship.edit, analysis.view, analysis.review, validation.view, validation.decide,<br>evidence.view, evidence.create, catalog.view, catalog.link, operations.view|
|ANALYST|project.portfolio.view, project.view, content.view, asset.view, analysis.view, analysis.run,<br>analysis.review, editor.view, inventory.view, inventory.create, inventory.edit,<br>inventory.merge, inventory.split, appearance.view, appearance.create, appearance.edit,<br>scene.view, relationship.view, relationship.edit, validation.view, evidence.view,<br>evidence.create, catalog.view, catalog.link, operations.view|
|PP_INTERACTIVE_EDITOR|project.portfolio.view, project.view, content.view, asset.view, editor.view, inventory.view,<br>inventory.edit, appearance.view, appearance.edit, scene.view, analysis.view,<br>analysis.review, validation.view, evidence.view, evidence.create, catalog.view, catalog.link,<br>operations.view|
|CLEARANCE_REVIEWER|project.portfolio.view, project.view, content.view, asset.view, editor.view, inventory.view,<br>appearance.view, scene.view, relationship.view, analysis.view, analysis.review,<br>validation.view, evidence.view, evidence.create, catalog.view, operations.view|
|CLEARANCE_AUTHORITY|project.portfolio.view, project.view, content.view, asset.view, editor.view, inventory.view,<br>appearance.view, scene.view, relationship.view, relationship.edit, analysis.view,<br>analysis.review, validation.view, validation.decide, evidence.view, evidence.create,<br>catalog.view, operations.view|
|ADVERTISING_MANAGER|project.portfolio.view, project.view, content.view, asset.view, analysis.view, analysis.review,<br>editor.view, inventory.view, appearance.view, scene.view, relationship.view, validation.view,<br>evidence.view, catalog.view, catalog.link, operations.view|
|VIEWER|project.portfolio.view, project.view, content.view, asset.view, analysis.view, inventory.view,<br>appearance.view, scene.view, relationship.view, validation.view, evidence.view,<br>catalog.view|
|INTEGRATION_ADMIN|project.portfolio.view, project.view, project.access.view, content.view, asset.view,<br>operations.view, project.settings.general.view, project.settings.audit.view|
|INTERNAL_ADMIN|internal.platform.view, internal.platform.manage, support.access.view, support.access.issue,<br>support.access.revoke, support.access.use, pilot.entitlement.issue, user.account.suspend|



###### **Authority rule** 

RolePackageVersion is a package of potential capabilities. Project authority remains the intersection of active Membership package, Acting Organization, ProjectAccessGrant, collaboration ceiling when applicable, state/Data Visibility/domain policy and entitlement. INTERNAL_ADMIN has no implicit customer Project access. 

##### **M0-01-011 - Support Access foundation** 

**Objective.** Materialize time-limited audited support access so internal staff can diagnose customer Projects without standing superuser authority. 

**Scope.** SupportAccessGrant aggregate and resolver integration only; no full ADM workspace. 

###### **Invariants.** 

- Default grant validity 4h; maximum 24h. 

- Grant targets one Project and an explicit capability ceiling. 

- Support grant never grants License/UsageRight or mutates customer ProjectAccessGrant. 

- Expired/revoked grant disappears from effective access immediately. 

- Issue/use/revoke is auditable. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 90 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Model / schema contract** 

```
SupportAccessGrant(id, support_membership_id, project_id, capability_scope[], reason,
support_case_reference?, valid_from, valid_until, state ACTIVE|REVOKED|EXPIRED, issued_by_user_id,
revoked_by_user_id?, created_at, revoked_at?)
```

###### **Commands** 

- IssueSupportAccessGrant 

- RevokeSupportAccessGrant 

###### **Queries / read models** 

- AuthorizedSupportAccessQuery 

###### **API / contract** 

```
Internal-only endpoints behind internal capabilities; no public customer API required in M0.
```

###### **Authorization and visibility** 

- issue/revoke requires internal support capabilities; use requires support.access.use AND active grant AND target visibility policy. 

###### **Errors / conflicts** 

- SUPPORT_GRANT_REQUIRED 

- SUPPORT_GRANT_EXPIRED 

- SUPPORT_SCOPE_EXCEEDED 

###### **Acceptance tests** 

- Internal admin without grant cannot read customer Project. 

- Grant ceiling enforced. 

- Expiry/revoke immediate. 

###### **Definition of Done** 

- Support access cannot be used as entitlement or Project membership shortcut. 

###### **Codex execution instruction** 

```
Implement support access as a temporary intersected authority dimension, never as a superuser
bypass.
```

##### **DEV-001 - Optimistic concurrency foundation** 

**Objective.** Prevent silent last-write-wins on governed multi-user truth. 

**Scope.** Shared revision precondition contract plus persistence helpers. 

###### **Invariants.** 

- Sensitive mutable aggregates expose monotonically increasing revision. 

- Commands include expected_revision. 

- Mismatch returns 409 CONCURRENT_MODIFICATION without applying partial writes. 

- At minimum applies to SceneStructure, InventoryItem, Appearance, CanonicalLink and sensitive ProjectAccessGrant changes. 

###### **Model / schema contract** 

```
revision BIGINT NOT NULL; update ... WHERE id=? AND revision=expected; atomic increment on success.
```

###### **Errors / conflicts** 

- CONCURRENT_MODIFICATION 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 91 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Acceptance tests** 

- Two writers load revision N; first commits N+1; second receives 409 and no data loss. 

###### **Definition of Done** 

- Shared library used by all listed aggregates. 

###### **Codex execution instruction** 

```
Implement one reusable optimistic concurrency pattern. Do not create module-specific last-write-wins
behavior.
```

##### **M0-02-010 - Initial Analysis Policy** 

**Objective.** Govern prospect/free Initial Analysis cost, abuse, media acceptance and feature/provider profile with a versioned policy. 

**Scope.** Policy entity/config, resolver and logical scan identity. 

###### **Invariants.** 

- Policy cannot relax Security/Data Visibility. 

- Values are versioned data/config, not hardcoded branches. 

- Operation retry is not a new scan. 

- Logical scan binds exact CV, source checksum, policy version and analysis profile version. 

###### **Model / schema contract** 

```
InitialAnalysisPolicyVersion(id, version, allowed_media_profiles, max_file_size, max_duration,
free_scan_policy, rerun_policy, analysis_feature_profile, provider_profile, queue_priority,
rate_policy, cost_guard, human_review_policy, effective_from)
```

###### **Commands** 

- ResolveInitialAnalysisPolicy 

###### **Queries / read models** 

- InitialAnalysisEligibilityProjection 

###### **Errors / conflicts** 

- INITIAL_ANALYSIS_POLICY_DENIED 

- INITIAL_ANALYSIS_QUOTA_EXCEEDED 

- MEDIA_PROFILE_NOT_ALLOWED 

###### **Acceptance tests** 

- Retry does not decrement quota twice. 

- Policy version is captured by AnalysisRun. 

###### **Definition of Done** 

- Production config must be explicit before go-live; tests may use fixtures. 

###### **Codex execution instruction** 

```
Implement policy-driven eligibility and logical scan identity. Do not hardcode commercial limits in
controllers/workers.
```

##### **M0-02-005 v1.1 - Quick Create orchestration reconciliation** 

**Objective.** Reconcile Quick Create with the final NPW contract and provide the stable boundary to later Analysis implementation. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 92 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

**Scope.** Two explicit creation paths: Analyse Content with uploaded media, and Create Project without video. 

###### **Invariants.** 

- Analyse Content remains one user intent: upload claim, Project/Content/CV/media foundation and Initial Analysis handoff. 

- Create Project without video creates Project + creator ProjectAccessGrant + Content + CV-001 DRAFT only and routes to Project Overview. 

- No Project is created when a file is merely selected; media is uploaded/staged first. 

- Acting Organization determines ownership and is never trusted from request body. 

- InitialAnalysisPort is the stable application boundary; replacing its temporary adapter with the concrete Analysis adapter must not create duplicate AnalysisRuns. 

###### **Model / schema contract** 

```
InitialAnalysisPort { startInitialAnalysis(project_id, content_version_id, source_asset_file_id,
policy_version_id, correlation_id) -> analysis_run_ref }
```

###### **Commands** 

- QuickCreateWithAnalysis 

- CreateProjectWithoutVideo 

###### **Queries / read models** 

- QuickCreateEligibilityProjection 

###### **API / contract** 

```
POST /projects/quick-create { title, content_type?, upload_session_id, idempotency_key }
POST /projects { title, content_type?, creation_mode: STANDARD, idempotency_key }  // no-video M0
route
```

###### **Events** 

- ProjectCreated 

- ContentCreated 

- ContentVersionCreated 

- InitialAnalysisRequested when media path used 

###### **Authorization and visibility** 

- project.create in validated Acting Organization; source upload/claim remains creator/context-bound. 

###### **Errors / conflicts** 

- INITIAL_ANALYSIS_POLICY_DENIED 

- UPLOAD_ORGANIZATION_MISMATCH 

- IDEMPOTENCY_CONFLICT 

###### **Acceptance tests** 

- No-video path creates no Asset/AssetFile/AnalysisRun. 

- Media path starts exactly one logical Initial Analysis through the port. 

- Swapping the InitialAnalysisPort adapter does not duplicate runs. 

- Double-submit is idempotent on both paths. 

###### **Definition of Done** 

- Both final Guide creation paths are executable without frontend orchestration. 

- InitialAnalysisPort is explicit in contracts/tests. 

###### **Codex execution instruction** 

```
Replace the v1.0 single-path Quick Create assumption. Implement separate no-video and Analyse
Content commands, and route analysis through InitialAnalysisPort so Wave 6 does not depend on a
concrete Wave 9 adapter.
```

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 93 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

##### **M0-02-007 v1.1 - NPW-01 reconciled Quick Create UI** 

**Objective.** Align the New Project surface with the final Guide while preserving minimum friction. 

**Scope.** One short form with progressive optional metadata and two explicit exits. 

###### **Invariants.** 

- Project title is required. 

- Content Type is optional/progressive and must not block creation or analysis. 

- Video is optional at form level. 

- When media exists, Analyse Content is the primary CTA. 

- Create project without video is the secondary path. 

- Team, licenses, modules, territory, rights and integrations stay out of NPW. 

###### **Commands** 

- QuickCreateWithAnalysis 

- CreateProjectWithoutVideo 

###### **Queries / read models** 

- QuickCreateEligibilityProjection 

- ActingOrganizationSummary 

###### **Authorization and visibility** 

- UI is rendered only for project.create in the current Acting Organization; server reauthorizes on submit. 

###### **Errors / conflicts** 

- PROJECT_TITLE_REQUIRED 

- UPLOAD_NOT_READY 

- ACTING_ORGANIZATION_REQUIRED 

###### **Acceptance tests** 

- Title-only project can be created without video. 

- Title + optional Content Type + uploaded video can Analyse Content. 

- No hidden configuration is required before either path. 

- Organization switch invalidates staged-context assumptions and never auto-switches silently. 

###### **Definition of Done** 

- NPW-01 screen contract and Greenfield packet agree exactly. 

- No v1.0 title+video-only contradiction remains normative. 

###### **Codex execution instruction** 

```
Implement the final NPW-01 contract: title required, Content Type optional/progressive, video
optional, Analyse Content primary with media, Create project without video secondary. Do not
reintroduce a long wizard.
```

##### **M0-03-008 - Technical Replacement Assessment** 

**Objective.** Reconcile AssetFile replacement with ContentVersion semantics after governed truth exists. 

**Scope.** Assessment + same-CV replacement / new-CV routing. 

###### **Invariants.** 

- Duration equality alone is insufficient. 

- EQUIVALENT requires same editorial cut and compatible normalized temporal mapping. 

- UNCERTAIN cannot replace in same CV once downstream truth exists. 

- Old exact AssetFile/checksum references remain historical. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 94 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Model / schema contract** 

```
TechnicalReplacementAssessment(outcome EQUIVALENT|NOT_EQUIVALENT|UNCERTAIN, old_asset_file_id,
new_upload/source_ref, technical_comparison, sampled_temporal_correspondence, human_confirmation?,
created_at)
```

###### **Commands** 

- AssessTechnicalReplacement 

- ReplaceTechnicalAssetFile 

- CreateContentVersion when not equivalent 

###### **Errors / conflicts** 

- TECHNICAL_REPLACEMENT_UNCERTAIN 

- CONTENT_VERSION_REPLACEMENT_NOT_EQUIVALENT 

###### **Acceptance tests** 

- Equivalent technical encode keeps same CV and supersedes file. 

- Frame-padding/offset mismatch routes to new CV. 

- Evidence remains bound to old source. 

###### **Definition of Done** 

- No source overwrite. 

- Replacement decision is auditable. 

###### **Codex execution instruction** 

```
Implement governed replacement equivalence. Remove the v1.0 pre-analysis-only shortcut.
```

##### **M0-05-000 - Unified Taxonomy foundation** 

**Objective.** Provide the sole versioned vocabulary engine for Inventory and Context classification. 

**Scope.** Taxonomy core used by later Catalog/Analysis; no separate Family/Context term engines. 

###### **Invariants.** 

- Assignments bind exact TaxonomyVersion. 

- New versions never silently reinterpret historical assignments. 

- Family is a UI/business presentation of Inventory Taxon. 

- Key Context is a Context TaxonomyAssignment. 

###### **Model / schema contract** 

```
Taxonomy(id, purpose) -> TaxonomyVersion(id, taxonomy_id, version, lifecycle) -> Taxon(id,
taxonomy_version_id, parent_id?, code, label) -> TaxonomyAssignment(id, taxonomy_version_id,
taxon_id, subject_type, subject_id, provenance)
```

###### **Commands** 

- AssignTaxon 

- ReplaceTaxonomyAssignment 

- CreateTaxonomyVersion (internal seed/admin foundation) 

###### **Queries / read models** 

- TaxonomyForPurposeQuery 

- AssignmentsForSubjectQuery 

###### **Errors / conflicts** 

- TAXONOMY_VERSION_MISMATCH 

- TAXON_NOT_IN_VERSION 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 95 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Acceptance tests** 

- Inventory Family uses INVENTORY taxonomy. 

- Context uses CONTEXT taxonomy. 

- Version upgrade leaves old assignment semantics intact. 

###### **Definition of Done** 

- No FamilyRegistry or ContextTerm engine exists. 

###### **Codex execution instruction** 

```
Implement one taxonomy abstraction used by Inventory and Context. Do not duplicate vocabulary
engines.
```

##### **M0-05-008 - Inventory split + Appearance completion** 

**Objective.** Complete governed Core correction and Appearance semantics required by Editor/Analysis. 

**Scope.** Split lineage, lifecycle/modality and derived metrics. 

###### **Invariants.** 

- Split preserves explicit lineage and redistributes appearances explicitly. 

- Canonical/taxonomy links are reviewed, not blindly cloned. 

- Appearance lifecycle ACTIVE/SUPERSEDED/ARCHIVED. 

- Modalities may include VISUAL, ON_SCREEN_TEXT, AUDIBLE, MENTIONED. 

- Total on-screen time is union of active intervals. 

###### **Model / schema contract** 

```
InventoryLineage(relation MERGED_FROM|SPLIT_FROM, source_item_id, target_item_id, created_at)
Appearance(... lifecycle, modalities[], provenance, revision)
```

###### **Commands** 

- SplitInventoryItem 

- existing MergeInventoryItems 

- Appearance commands use expected_revision 

###### **Queries / read models** 

- DerivedAppearanceMetricsQuery 

###### **Errors / conflicts** 

- INVALID_SPLIT_PARTITION 

- APPEARANCE_ALREADY_ASSIGNED_IN_SPLIT 

- CONCURRENT_MODIFICATION 

###### **Acceptance tests** 

- Mixed mistaken grouping can split into two items without losing appearances. 

- Overlapping appearances do not double-count on-screen time. 

###### **Definition of Done** 

- Lineage and validation impact recorded. 

###### **Codex execution instruction** 

```
Add split and complete Appearance semantics without introducing Track as a second truth.
```

##### **M0-06-002 v1.1 - SceneStructure aggregate** 

**Objective.** Replace v1.0 plain Scene projection with an exact-ContentVersion SceneStructure aggregate. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 96 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

**Scope.** Scene authoring, validity and coverage. 

###### **Invariants.** 

- Authoring may be INCOMPLETE. 

- VALID means coverage [0,duration) with no gaps/invalid overlaps. 

- Scene edits never automatically change Appearance timing. 

- All mutations require expected_revision. 

###### **Model / schema contract** 

```
SceneStructure(id, content_version_id, revision, timestamps)
Scene(id, scene_structure_id, start_ms, end_ms, label?, provenance)
Derived integrity INCOMPLETE|VALID|INVALID
```

###### **Commands** 

- CreateScene 

- AdjustScene 

- SplitScene 

- MergeScenes 

- ValidateSceneStructure 

###### **Errors / conflicts** 

- SCENE_STRUCTURE_INVALID 

- CONCURRENT_MODIFICATION 

###### **Acceptance tests** 

- Gap structure remains INCOMPLETE/INVALID until repaired. 

- Boundary edit leaves Appearance unchanged. 

###### **Definition of Done** 

- Editor scene mode uses this aggregate only. 

###### **Codex execution instruction** 

```
Replace any independent Scene CRUD authority with SceneStructure aggregate semantics.
```

##### **M0-06-009 - Minimum ContextualRelationship** 

**Objective.** Support manual governed relationships needed by Analysis/Editor/Validation without building advanced graph automation. 

**Scope.** Manual project/CV-scoped relationship only. 

###### **Invariants.** 

- Relationship has exact Project and ContentVersion context. 

- Optional Scene/TimeRange may constrain validity. 

- Revision/concurrency and RELATIONSHIP validation dimension apply. 

- AI can propose but not silently materialize validated relationship truth. 

###### **Model / schema contract** 

```
ContextualRelationship(id, project_id, content_version_id, subject_ref, relationship_type,
object_ref, scene_id?, start_ms?, end_ms?, provenance, revision)
```

###### **Commands** 

- CreateRelationship 

- ChangeRelationship 

- ArchiveRelationship 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 97 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Queries / read models** 

- RelationshipsForSubjectQuery 

###### **Acceptance tests** 

- Relationship edit causes RELATIONSHIP revalidation as policy declares. 

###### **Definition of Done** 

- No advanced inference engine required. 

###### **Codex execution instruction** 

```
Implement manual relationship truth only; keep advanced automated relationships deferred.
```

##### **M0-07-001 v1.1 - AnalysisRun + frozen input manifest** 

**Objective.** Make every AnalysisRun reproducible and exact with immutable inputs. 

**Scope.** Run identity, methodology/profile/provider/cost and AnalysisInputManifest. 

###### **Invariants.** 

- Manifest explicitly records EMPTY for absent applicable inputs. 

- Worker retries reuse the same frozen manifest. 

- Deliberate rerun creates a new logical run/manifest. 

- No current/latest ContentVersion resolution inside run execution. 

###### **Model / schema contract** 

```
AnalysisRun(project_id, content_version_id, operation_id, analysis_profile_version_id,
methodology_version_id, initial_analysis_policy_version_id?, provider, provider_model,
input_manifest_id, output_manifest_id?, state, estimated_cost?, observed_cost?, timestamps)
AnalysisInputManifest(exact CV, AssetFile IDs+SHA256, technical profile version, SceneStructure
revision/hash, Inventory baseline/hash, Validation baseline/hash, TaxonomyVersion IDs,
ruleset/config versions, provider/model)
```

###### **Commands** 

- StartInitialAnalysis 

- StartAnalysisRerun 

###### **Queries / read models** 

- AnalysisRunDetailQuery 

###### **Errors / conflicts** 

- ANALYSIS_INPUT_CHANGED 

- ANALYSIS_PROFILE_NOT_AVAILABLE 

###### **Acceptance tests** 

- Manifest remains immutable across retry. 

- Changing taxonomy after run does not mutate old manifest. 

###### **Definition of Done** 

- Provider execution can be reproduced/audited from frozen identifiers and configuration. 

###### **Codex execution instruction** 

```
Expand AnalysisRun to freeze all applicable inputs. Do not read mutable latest Core/taxonomy state
inside a retry.
```

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 98 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

##### **M0-07-006 v1.1 - AnalysisSnapshot currentness** 

**Objective.** Derive whether an immutable AnalysisSnapshot is current for the present applicable inputs. 

**Scope.** Currentness resolver; historical snapshots preserved. 

###### **Invariants.** 

- No mutable is_current flag as truth. 

- OUTDATED is not invalid/deleted. 

- Reasons are explicit and explainable. 

###### **Model / schema contract** 

```
Currentness = CURRENT | OUTDATED; reasons = CONTENT_VERSION_CHANGED|SOURCE_CHANGED|
SCENE_STRUCTURE_CHANGED|INVENTORY_CHANGED|VALIDATION_CHANGED|TAXONOMY_CHANGED|METHODOLOGY_CHANGED|
PROFILE_CHANGED
```

###### **Queries / read models** 

- ResolveAnalysisSnapshotCurrentness 

- CurrentAnalysisProjection 

###### **Acceptance tests** 

- SceneStructure change marks previous snapshot OUTDATED. 

- Old snapshot remains retrievable when authorized. 

###### **Definition of Done** 

- No silent snapshot rewrite. 

###### **Codex execution instruction** 

```
Implement dependency comparison currentness; preserve historical analysis.
```

##### **M0-07-005 v1.1 - Scoped Vertical Relevance** 

**Objective.** Model vertical relevance without forcing all appearances of an item to share the same commercial meaning. 

**Scope.** Assignments at InventoryItem/Appearance/Scene/ContextAssignment; candidate may carry proposal only. 

###### **Invariants.** 

- Item relevance never auto-propagates. 

- Vertical values ADVERTISING, INTERACTIVE, CLEARANCE do not imply product implementation/entitlement. 

- Accepted assignment preserves provenance. 

###### **Model / schema contract** 

```
VerticalRelevanceAssignment(subject_type, subject_id, vertical, relevance, reason/provenance,
revision)
```

###### **Commands** 

- AssignVerticalRelevance 

- PropagateVerticalRelevance explicitly if a future command is authorized 

###### **Queries / read models** 

- VerticalRelevanceForSubjectQuery 

###### **Acceptance tests** 

- One Appearance can be Interactive-relevant while another Appearance of same item is not. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 99 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Definition of Done** 

- Analysis Opportunities use scoped assignments correctly. 

###### **Codex execution instruction** 

```
Implement scoped relevance assignments. Never turn Inventory tags into implicit per-Appearance
truth.
```

##### **M0-07-009 v1.1 - Analysis Workspace contract** 

**Objective.** Restore the full M0 workspace operations required by the Guide and approved wireframes. 

**Scope.** Server-authorized appearance-oriented workspace and inspector projections. 

###### **Invariants.** 

- Counts/facets/search all use the same authorized universe. 

- Exploitable detail requires ANALYSIS_DETAIL entitlement where policy applies. 

- Candidate remains proposal until a governed bridge materializes Core truth. 

- Open in Editor carries exact CV/subject/time and opaque return_context_id. 

###### **Commands** 

- AcceptDetectionCandidate 

- CorrectAndAcceptDetectionCandidate 

- RejectDetectionCandidate 

- Merge/Split Inventory 

- AssignTaxon 

- Validation commands 

- ResolvePersonToCharacter 

- relationship commands 

- AssignVerticalRelevance 

###### **Queries / read models** 

- AuthorizedAnalysisWorkspaceQuery 

- AnalysisInspectorQuery 

- ExtendedElementQuery 

###### **Authorization and visibility** 

- analysis.view/review plus command-specific Core/Validation capabilities; entitlement separately gates detail. 

###### **Acceptance tests** 

- No-license sees aggregate teaser but not row/appearance detail. 

- Clearance-only actor does not discover Advertising-only private surfaces/data. 

- Bulk action cannot cross authorized universe. 

###### **Definition of Done** 

- VIS-020/021/022 screen contracts green. 

###### **Codex execution instruction** 

```
Implement the Analysis workspace from server-authorized read models; no client-side filtering as
security boundary.
```

##### **M0-08-001 v1.1 - Dimensional immutable ValidationDecision** 

**Objective.** Replace v1.0 flat validation semantics with dimension-specific historical decisions. 

**Scope.** Validation decisions and projection. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 100 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Invariants.** 

- Dimensions IDENTITY/TEMPORAL/SPATIAL/MODALITY/TAXONOMY/RELATIONSHIP. 

- Decision is immutable and references subject_revision. 

- CORRECT_AND_VALIDATE first executes owner-domain mutation, then validates resulting revision. 

- AI never silently validates human truth. 

###### **Model / schema contract** 

```
ValidationDecision(id, subject_type, subject_id, subject_revision, dimension, decision VALIDATE|
REJECT|CORRECT_AND_VALIDATE, actor_user_id, membership_id, acting_organization_id, evidence_ids[],
reason?, correlation_id, created_at)
```

```
Projection: NOT_REVIEWED|IN_REVIEW|VALIDATED|REJECTED|NEEDS_REVALIDATION per dimension
```

###### **Commands** 

- ValidateSubjectDimension 

- RejectSubjectDimension 

- CorrectAndValidateSubjectDimension 

###### **Queries / read models** 

- ValidationProjectionForSubject 

###### **Acceptance tests** 

- Identity may be VALIDATED while Taxonomy NEEDS_REVALIDATION. 

- Historical decision remains after mutation. 

###### **Definition of Done** 

- No isValidated boolean acts as history. 

###### **Codex execution instruction** 

```
Replace flat validation with immutable dimensional decisions and derived current projection.
```

##### **M0-08-002 v1.1 - Validation Impact Policy** 

**Objective.** Determine which prior validated dimensions become NEEDS_REVALIDATION after material mutations. 

**Scope.** Versioned/static registry and integration into owner-domain commands. 

###### **Invariants.** 

- Policy never deletes old decisions. 

- Owner-domain mutation and resulting impact are committed/audited coherently. 

- Impact is explicit, testable and explainable. 

###### **Model / schema contract** 

```
ValidationImpactPolicy(mutation_type -> affected dimensions / conditional rules)
```

###### **Commands** 

- ApplyValidationImpact (application internal) 

###### **Acceptance tests** 

- AdjustAppearanceTiming affects TEMPORAL and conditionally SPATIAL. 

- ChangeCanonicalLink affects IDENTITY. 

- ChangeTaxonomyAssignment affects TAXONOMY. 

- ChangeRelationship affects RELATIONSHIP. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 101 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

###### **Definition of Done** 

- All material M0 Core commands declare validation impact. 

###### **Codex execution instruction** 

```
Implement one validation impact registry and invoke it from governed mutations; do not let each UI
invent revalidation logic.
```

##### **M0-09-005 v1.1 - Entitlement model + pilot provisioning** 

**Objective.** Make M0 detail access real, scoped and auditable without building the complete Licensing administration product. 

**Scope.** License/Term/UsageRight scope, EffectiveEntitlement resolver, ProductAccessPolicy teaser, internal pilot commands. 

###### **Invariants.** 

- UsageRightGrant declares organization/project scope, product_scope, data_scope, rights, validity and conditions. 

- M0 minimum product_scope ANALYSIS_DETAIL. 

- Opportunity Scan teaser is explicit policy, not fake entitlement. 

- No isDemo/bypassLicensing/hasAnyLicense full access. 

- SupportAccessGrant is not entitlement. 

###### **Model / schema contract** 

```
UsageRightGrant(organization_id, project_id?, product_scope, data_scope, rights[], valid_from,
valid_until?, conditions)
```

###### **Commands** 

- IssuePilotEntitlement 

- RevokePilotEntitlement 

###### **Queries / read models** 

- ResolveEffectiveEntitlement 

- ResolveProductAccessPolicy 

###### **Authorization and visibility** 

- Internal issue/revoke requires pilot.entitlement.issue + Audit. Customer detail additionally requires IAM/Project capability. 

###### **Acceptance tests** 

- Pilot entitlement unlocks only scoped Analysis detail. 

- Expiry blocks new detail query/export/regeneration. 

- Advertising license cannot imply Clearance/Interactive detail. 

###### **Definition of Done** 

- No bypass flag exists. 

###### **Codex execution instruction** 

```
Implement M0 rights using canonical licensing entities and a scoped resolver. Keep commercial admin
UX deferred.
```

#### **R10. WorkContext, deep-link and screen-contract integration** 

```
WorkContext (non-authoritative)
```

```
{ user_id, acting_organization_id, project_id, content_version_id, surface,
  scene_id?, inventory_item_id?, appearance_id?, timecode_ms?, view_state?, updated_at }
```

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 102 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

```
Restore always reauthorizes.
```

```
return_context_id is opaque and session-bound.
```

- M0 ownership transfer is deferred; SET-01 supports permitted identity changes plus Archive/Restore only. 

- Integration foundation retains Integration -> Environment -> Credential -> TechnicalScopeGrant -> ProjectIntegrationBinding; no full Integration UI in M0. 

- Administration foundation retains Support Access, pilot entitlements, audit lookup and account security only. 

- The companion M0 Screen Contract Matrix v1.0 is implementation-normative for REQUIRED/FOUNDATION screens. 

#### **R11. Retention / SLO / Recovery / Accessibility** 

|**CONCERN**|**v1.1 BASELINE**|
|---|---|
|Unclaimed upload|Delete 24h after expiry|
|Worker temp|Immediate; reconcile <=24h|
|Invalid abandoned upload|<=72h|
|Source media|Governed; no TTL while active/referenced|
|Proxy/FRAME_UI|Reconstructable; GC eligible after 30d unreferenced|
|Evidence|Governed, no cache TTL deletion|
|Audit|24 months|
|Logs|30 days|
|Security telemetry|90 days|
|Availability|99.9% monthly customer API/session|
|Read p95|<500ms ordinary API|
|Mutation p95|<1s ordinary mutation|
|Home/Projects p95|<1.5s backend|
|Operation dispatch|<5s p95 after commit|
|RPO / RTO|<=15 min / <=4 h|
|Accessibility|WCAG 2.2 AA|



###### **Pre-production evidence** 

Retention/legal validation, real workload SLO proof, tested restore and WCAG audit are pre-production conditions. They do not permit architecture shortcuts during M0 implementation. 

#### **R12. Traceability and drift gates** 

```
Guide decision -> invariant -> entity/command/query -> packet -> screen/API -> test -> gate
```

- Use stable DEC/INV/ENT/CAP/CMD/QRY/EVT/SCR/PKT/TC/GATE/ADR identifiers. 

- Every M0 screen has an owner packet and acceptance tests; every capability has at least one consumer; every command has tests. 

- Architecture drift search remains mandatory after every Wave: no ProjectMember, User.role, current/latest temporal authority, public media URL, Project.status god-state, isValidated truth, avg-fps VFR exact mapping, AI direct accepted truth, UI thumbnail as Evidence, role-name navigation or license bypass. 

###### **v1.1 packet execution rule** 

For packets superseded in this overlay, Codex must read the v1.1 replacement first and must not implement conflicting v1.0 clauses. A packet report must state which normative version was used. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 103 

**IWANTIT  |  M0 GREENFIELD DEVELOPMENT EXECUTION PACK  |  v1.1** 

#### **R13. v1.1 implementation freeze gate** 

- Guide v2.22 Freeze Candidate + this v1.1 + M0 Screen Contract Matrix + Correction/Traceability Register are the complete implementation basis. 

- FRZ-16 returned P0=0 on 24 August 2026; the set is FROZEN FOR IMPLEMENTATION. 

- Any future P0-level contradiction is a freeze regression: implementation of the affected boundary stops until the artifact is corrected through explicit change control and re-audited. 

Internal  /  Implementation Contract  /  24 Aug 2026 

Page 104 

