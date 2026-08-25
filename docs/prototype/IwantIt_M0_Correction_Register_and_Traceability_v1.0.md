# lwantit 

~~EE~~ 



IwantIt  |  Content Intelligence Infrastructure 

## **Document control** 

|**FIELD**|**VALUE**|
|---|---|
|Artifact|IwantIt M0 Correction Register and Traceability Matrix|
|Version|v1.0|
|Status|Frozen closure record|
|Date|24 August 2026|
|Normative basis|External freeze audit + Guía Maestra v2.22 FROZEN + Greenfield v1.1|
|Purpose|Prove that every blocking audit finding is resolved, that corrections are not silent, and that product<br>decisions trace to implementation/testing gates.|
|Change control|Structural changes after freeze require ADR and owner approval.|



### **Interpretation** 

CLOSED means the product/architecture decision is defined and represented in the remediated artifact set. It does not mean the feature has been implemented in code. 

## **1. P0 Correction Register** 

|**ID**|**FINDING**|**WHY IT BLOCKED FREEZE**|**NORMATIVE CORRECTION**|**FRZ**|**STATUS**|
|---|---|---|---|---|---|
|P0-01|Execution packets too compressed|Critical packets could still force<br>implementation choices.|Expand affected packets with<br>schema/invariants/commands/queries/API/e<br>vents/auth/concurrency/errors/tests.|FRZ-01..FRZ-15|CLOSED|
|P0-02|Authentication missing|Authorization existed but the route from<br>human to authenticated User was<br>undefined.|Add managed authentication foundation<br>before IAM.|FRZ-01|CLOSED|
|P0-03|First Organization/Admin bootstrap missing|Clean installation and invited-new-user<br>paths were incomplete.|Self-service verified bootstrap and invitation<br>identity resolution.|FRZ-01|CLOSED|
|P0-04|Role packages lacked exact capability sets|Role names existed without deterministic v1<br>package composition.|Freeze capability registry and<br>RolePackageVersion v1 matrix.|FRZ-02|CLOSED|
|P0-05|Internal admin could become implicit<br>superuser|Support access boundary was not<br>materialized in M0 handoff.|Time-limited audited SupportAccessGrant.|FRZ-02|CLOSED|
|P0-06|New Project contradicted final Guide|Execution pack omitted no-video creation<br>and progressive Content Type.|Reconcile NPW contract.|FRZ-03|CLOSED|
|P0-07|Initial Analysis Policy missing|Free/prospect analysis had no versioned<br>cost/abuse/feature governance.|Add InitialAnalysisPolicyVersion.|FRZ-03|CLOSED|
|P0-08|Asset replacement contradicted Guide|Pack incorrectly restricted same-CV|Use governed editorial + temporal|FRZ-04|CLOSED|



Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

|**ID**|**FINDING**|**WHY IT BLOCKED FREEZE**|**NORMATIVE CORRECTION**|**FRZ**|**STATUS**|
|---|---|---|---|---|---|
|||replacement to pre-analysis.|equivalence assessment.|||
|P0-09|Scene simplified below Guide contract|Plain Scene CRUD lost structure<br>coverage/order validity.|Restore SceneStructure aggregate.|FRZ-05|CLOSED|
|P0-10|Duplicate taxonomy concepts|Family and Context could become separate<br>vocabulary engines.|Use one Taxonomy engine.|FRZ-06|CLOSED|
|P0-11|SplitInventoryItem missing|Incorrect AI/human grouping could not be<br>safely corrected.|Add split command + lineage.|FRZ-07|CLOSED|
|P0-12|Appearance incomplete|Lifecycle, modality and derived-metric<br>semantics were under-specified.|Complete Appearance contract.|FRZ-07|CLOSED|
|P0-13|Validation model too flat|ACCEPT/REJECT/CORRECT lost<br>dimensions and revalidation.|Use dimensional immutable<br>ValidationDecision + impact policy.|FRZ-08|CLOSED|
|P0-14|Analysis inputs not fully frozen|Reproducibility/currentness could not be<br>proven.|Add AnalysisInputManifest and currentness<br>resolver.|FRZ-09|CLOSED|
|P0-15|Vertical relevance too coarse|Item tags could incorrectly propagate to all<br>appearances.|First-class scoped<br>VerticalRelevanceAssignment.|FRZ-09|CLOSED|
|P0-16|Entitlement bootstrap/scope incomplete|Detailed M0 could not be safely unlocked<br>for pilots.|Audited pilot provisioning using real rights<br>entities.|FRZ-10|CLOSED|
|P0-17|Optimistic concurrency missing|Concurrent human edits could silently<br>overwrite truth.|revision + expected_revision; 409 on<br>conflict.|FRZ-05|CLOSED|
|P0-18|M0 screen implementation matrix missing|Wireframes and packets lacked a<br>deterministic 1:1 contract.|Create M0 Screen Contract Matrix.|FRZ-11|CLOSED|



## **2. P1 Scope/Correction Register** 

|**ID**|**FINDING**|**RESOLUTION**|**FRZ**|
|---|---|---|---|
|P1-01|ContextualRelationship M0 scope|Resolved: manual relationship minimum included;<br>automation deferred.|FRZ-12|
|P1-02|WorkContext persistence|Resolved as non-authoritative resume context with<br>reauthorization.|FRZ-12|
|P1-03|Project ownership transfer|Deferred from customer M0; Archive/Restore remain.|FRZ-12|
|P1-04|Integrations/Admin minimal foundation|Contracts/foundations retained without full workspaces.|FRZ-12|
|P1-05|Retention/deletion matrix|M0 baseline defined; legal/contract validation remains<br>pre-production.|FRZ-13|
|P1-06|SLO/RPO/RTO thresholds|M0 measurable targets defined; must be proven pre-<br>production.|FRZ-13|
|P1-07|Accessibility baseline|WCAG 2.2 AA made cross-cutting acceptance target.|FRZ-13|
|P1-08|Local historical P0 vs global M0|Resolved by explicit scope reconciliation: schema|FRZ-15|



Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

|**ID**|**FINDING**|**RESOLUTION**|**FRZ**|
|---|---|---|---|
|||supports N versions; M0 exit gate requires CV-001 path.||
|P1-09|Deep-link/return context|Opaque return_context_id and WorkContext protocol<br>defined.|FRZ-12|
|P1-10|Traceability too small|Expanded stable-ID forward and reverse traceability<br>model.|FRZ-14|
|P1-11|Analysis Workspace packet too shallow|Required workspace operations restored in normative<br>overlay/screen contracts.|FRZ-09/11|
|P1-12|Scene/context metadata subset|Minimal governed Scene label/context assignment model<br>retained; richer scene ontology deferred.|FRZ-05/06|
|P1-13|Quick Create depends on later Analysis|Stable InitialAnalysisPort explicitly introduced; adapter<br>swapped later without duplicate runs.|FRZ-03|



## **3. Decision Register D199-D218** 

|**DECISION**|**TITLE**|**NORMATIVE OUTCOME**|
|---|---|---|
|D199|Managed authentication provider|IwantIt uses a managed identity provider for authentication; identity provider<br>authenticates, IwantIt authorizes.|
|D200|WorkOS AuthKit M0|WorkOS AuthKit Hosted UI is the M0 provider behind AuthenticationProviderPort;<br>production use is subject to DPA/security/residency review. Auth0 EU is the<br>approved fallback if strict EU identity residency cannot be contractually satisfied.|
|D201|Provider-independent identity|IwantIt User/AuthIdentity/ApplicationSession remain canonical; WorkOS<br>Organization/Role/Permission are not product authorization truth.|
|D202|Prospect bootstrap|Verified self-service prospect onboarding may bootstrap the first Organization and<br>ORGANIZATION_ADMIN Membership atomically; no email-domain autojoin.|
|D203|Support access|Internal staff have no standing customer Project/data access. Time-limited<br>SupportAccessGrant is mandatory for support access.|
|D204|New Project reconciliation|NPW remains short: title required, Content Type optional/progressive, video optional<br>at form level; Analyse content is primary when media exists; Create project without<br>video is the secondary path.|
|D205|Initial Analysis Policy|Initial Analysis is governed by a versioned policy for allowed media, quota/rate,<br>feature/profile, provider, cost and review policy. Retry does not consume a new<br>logical scan.|
|D206|Technical asset replacement|A new AssetFile may remain in the same ContentVersion only after editorial and<br>temporal equivalence assessment; uncertainty routes to a new<br>ContentVersion/remapping.|
|D207|SceneStructure aggregate|SceneStructure is the aggregate root for ordered Scenes. A governed VALID<br>structure covers the exact ContentVersion duration without gaps or invalid overlaps.|
|D208|Optimistic concurrency|Sensitive Core/IAM writes use revision + expected_revision; last-write-wins is<br>forbidden.|



Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

|**DECISION**|**TITLE**|**NORMATIVE OUTCOME**|
|---|---|---|
|D209|Unified Taxonomy|Taxonomy/TaxonomyVersion/Taxon/TaxonomyAssignment is the sole taxonomy<br>engine. Inventory Family and Context are projections/assignments over it.|
|D210|Inventory split and lineage|SplitInventoryItem is a first-class governed command preserving appearance<br>redistribution, canonical/taxonomy review, validation impact and lineage.|
|D211|Appearance completion|Appearance has explicit lifecycle, modality, provenance, exact ContentVersion and<br>revision. Counts/time/coverage are derived, not editable facts.|
|D212|Dimensional validation|ValidationDecision is immutable and dimension-specific; current validation state is a<br>projection and material mutations can cause NEEDS_REVALIDATION.|
|D213|Analysis reproducibility|AnalysisRun freezes exact inputs in AnalysisInputManifest including source<br>checksums, SceneStructure, Core/Validation baseline, taxonomy versions,<br>rules/config and provider/model.|
|D214|Analysis currentness|AnalysisSnapshot currentness is derived from dependency comparison; OUTDATED<br>remains historical and is not silently rewritten.|
|D215|Scoped vertical relevance|Vertical relevance can be assigned to InventoryItem, Appearance, Scene or<br>ContextAssignment; item relevance never silently propagates to all appearances.|
|D216|M0 entitlement provisioning|M0 uses real License/LicenseTerm/UsageRightGrant entities and audited internal<br>pilot provisioning; teaser access is policy, not bypass flags.|
|D217|M0 Screen Contract Matrix|Every M0 required/foundation screen has a written screen contract. Written contract<br>prevails over incidental wireframe sample data/copy.|
|D218|Operational closure baseline|M0 freeze includes WorkContext/deep-link protocol, manual relationship foundation,<br>retention, SLO/RPO/RTO, WCAG 2.2 AA, complete traceability and final external re-<br>audit.|



## **4. Explicit Cross-Document Correction Log** 

|**ID**|**SOURCE**|**OLD / AMBIGUOUS**|**NEW NORMATIVE CONTRACT**|**DECISION**|**AFFECTED**|
|---|---|---|---|---|---|
|COR-001|Greenfield v1.0|Authentication absent before IAM|Wave 0B AUTH-00-001..007; WorkOS AuthKit<br>adapter; User/AuthIdentity/ApplicationSession<br>local|D199-D202|AUTH screens, IAM, DEV order|
|COR-002|Greenfield v1.0|Role packages listed but not exact capability<br>matrix|Capability registry v1.1 + exact<br>RolePackageVersion v1 sets|D203|IAM/SET/Navigation tests|
|COR-003|Greenfield v1.0|INTERNAL_ADMIN risk of implicit customer<br>access|SupportAccessGrant<br>time-limited/project/capability scoped; no<br>standing customer access|D203|M0-01-011, internal admin|
|COR-004|Greenfield v1.0 NPW|Title + video only; no no-video route|Title required; Content Type optional; Video<br>optional; Analyse primary; Create without video<br>secondary|D204|NPW-01, Project Overview|
|COR-005|Guide/Greenfield gap|Initial Analysis no explicit commercial/cost<br>policy object|InitialAnalysisPolicyVersion governs<br>media/quota/rerun/features/provider/cost/review|D205|M0-02-010, AnalysisRun|
|COR-006|Greenfield v1.0 media|Same-CV technical replacement effectively pre-<br>analysis-only|Editorial + temporal equivalence assessment;<br>UNCERTAIN/NOT_EQUIVALENT routes to<br>new CV|D206|M0-03-008, Content versioning|



Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

|**ID**|**SOURCE**|**OLD / AMBIGUOUS**|**NEW NORMATIVE CONTRACT**|**DECISION**|**AFFECTED**|
|---|---|---|---|---|---|
|COR-007|Greenfield v1.0 Editor|Plain Scene CRUD/projection|SceneStructure aggregate with<br>coverage/integrity and revision|D207-D208|EDT-03|
|COR-008|Greenfield v1.0|Family registry + separate Context taxonomy<br>concept|Single Taxonomy/Version/Taxon/Assignment<br>engine|D209|M0-05-000, Analysis contexts|
|COR-009|Greenfield v1.0 Inventory|Merge but no governed split|SplitInventoryItem + InventoryLineage + explicit<br>Appearance partition|D210|M0-05-008|
|COR-010|Greenfield v1.0 Appearance|Lifecycle/modality/derived metric contract<br>incomplete|ACTIVE/SUPERSEDED/ARCHIVED;<br>modalities; union time derived; revision|D211|Editor/Extended Element|
|COR-011|Greenfield v1.0 Validation|Flat ACCEPT/REJECT/CORRECT projection|Dimension-specific immutable decisions +<br>NEEDS_REVALIDATION impact|D212|M0-08 v1.1|
|COR-012|Greenfield v1.0 Analysis|Run not sufficiently reproducible|Immutable AnalysisInputManifest +<br>methodology/profile/policy/provider/model/cost|D213|M0-07-001 v1.1|
|COR-013|Greenfield v1.0 Analysis|Snapshot availability treated more like<br>completion than dependency currentness|Derived CURRENT/OUTDATED with reason<br>codes; history preserved|D214|M0-07-006 v1.1|
|COR-014|Greenfield v1.0|Vertical relevance could be interpreted as item<br>tags|Scoped VerticalRelevanceAssignment; no auto<br>propagation|D215|M0-07-005 v1.1|
|COR-015|Greenfield v1.0 Entitlement|Model existed but pilot unlock/scope under-<br>specified|Scoped UsageRightGrant + real audited pilot<br>provisioning + ProductAccessPolicy teaser|D216|M0-09-005 v1.1|
|COR-016|Guide/Greenfield/Wireframes|No deterministic M0 screen contract layer|M0 Screen Contract Matrix v1.0|D217|All REQUIRED/FOUNDATION screens|
|COR-017|Greenfield v1.0|WorkContext/deep link implicit|Non-authoritative WorkContext + opaque<br>return_context_id + reauthorization|D218|Home/Analysis/Editor|
|COR-018|M0 NFR|Retention/SLO/accessibility insufficiently explicit|Retention classes, RPO/RTO/performance<br>baseline, WCAG 2.2 AA|D218|All packets/gates|
|COR-019|Execution order|DEV-000 -> IAM omitted authentication<br>dependency|Wave 0A repository -> 0B auth/concurrency -><br>IAM|D199/D208|Execution plan|
|COR-020|Project Settings|Ownership transfer could be inferred in M0|Archive/Restore M0; ownership transfer<br>explicitly deferred|D218|SET-01|
|COR-021|Analysis/Validation|Relationships could remain ambiguous|Minimum manual ContextualRelationship in M0;<br>advanced automation deferred|D218|Analysis inspector/Editor|



## **5. Decision -> Implementation -> Evidence Traceability** 

|**DECISION**|**INVARIANT / OUTCOME**|**PACKETS**|**SCREENS/APIS**|**TEST EVIDENCE**|**GATE**|
|---|---|---|---|---|---|
|D199-D202|Auth boundary, provider, bootstrap|AUTH-00-001..007|AUTH-01..04|AUTH-001..015|AUTH-GATE; M0-GATE-1|
|D203|Role matrix + SupportAccessGrant|M0-01-003/004/006/007/011|IAM-01..03; SET-02; internal foundation|IAM hardening + support tests|M0-GATE-1; M0-GATE-5|
|D204|NPW reconciliation|M0-02-005/007/008|NPW-01/02; PRO-01|E2E-01/05/12|M0-GATE-6|
|D205|Initial Analysis Policy|M0-02-010; M0-07-001 v1.1|NPW-01/02; Analysis|policy quota/rerun tests|M0-GATE-3/4/6|
|D206|Technical replacement equivalence|M0-03-008|CNT-04; PRO-01 attention|media replacement E2E|M0-GATE-2|
|D207-D208|SceneStructure + concurrency|DEV-001; M0-06-002 v1.1|EDT-03; EDT-01/02|TC-SCENE-*; concurrency tests|M0-GATE-2/4|



Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

|**DECISION**|**INVARIANT / OUTCOME**|**PACKETS**|**SCREENS/APIS**|**TEST EVIDENCE**|**GATE**|
|---|---|---|---|---|---|
|D209|Unified Taxonomy|M0-05-000|Analysis inspector/workspace; Editor|taxonomy version tests|M0-GATE-4|
|D210-D211|Inventory split + Appearance completion|M0-05-008; M0-06|EDT-01/02; VIS-022|Core split/derived metric tests|M0-GATE-4|
|D212|Dimensional Validation|M0-08-001/002 v1.1|EDT-02; VIS-021|Validation impact/evidence tests|M0-GATE-4/5|
|D213-D214|Analysis manifest/currentness|M0-07-001/006 v1.1|VIS-003/020/021|Analysis reproducibility/currentness tests|M0-GATE-4|
|D215|Scoped vertical relevance|M0-07-005 v1.1|VIS-020/021/022|appearance-level relevance tests|M0-GATE-4/5|
|D216|Scoped entitlements/pilot|M0-09-005 v1.1|Analysis teaser/detail; internal foundation|E2E-11; expiry/bypass tests|M0-GATE-5|
|D217|Screen contract matrix|All screen-owning packets|All REQUIRED/FOUNDATION screens|screen acceptance tests|M0-GATE-6|
|D218|WorkContext/NFR/traceability|M0-06-009; M0-09; cross-cutting|Home/Editor/Analysis/Settings|E2E-12; A11Y; DR|M0-GATE-3/5/6|



## **6. Reverse Traceability - Screen -> Packet/Test/Authority** 

|**SCREEN**|**M0**|**OWNING PACKET**|**TESTS**|**AUTHORITY**|
|---|---|---|---|---|
|AUTH-01|REQUIRED|AUTH-00-001/002/003|AUTH-001,002,013|None before authentication|
|AUTH-02|REQUIRED|AUTH-00-004|AUTH-001,014,015|Authenticated verified User; zero active memberships|
|AUTH-03|REQUIRED|AUTH-00-002; M0-01-004|AUTH-003,010|Active Membership in selected Organization|
|AUTH-04|REQUIRED|AUTH-00-005; M0-01-005|AUTH-004..007|Verified invited identity|
|HOME-01A|REQUIRED|M0-09-001|E2E-01,02,12|project.portfolio.view as applicable|
|HOME-01B|REQUIRED|M0-09-001|E2E-01|project.create|
|HOME-01C|REQUIRED|M0-09-001|E2E-07,08,12|operations.view + target visibility|
|PRJ-01|REQUIRED|M0-09-002|E2E-02,03,12|project.portfolio.view / project.create / project.archive|
|NPW-01|REQUIRED|M0-02-007/005/010|E2E-01,05|project.create|
|NPW-02|REQUIRED|M0-02-008; M0-04|E2E-07,08,12|project.view + target-specific operation/analysis caps|
|PRO-01A|REQUIRED|M0-09-003|E2E-05,08|project.view|
|PRO-01B|REQUIRED|M0-09-003|E2E-01,12|project.view|
|PRO-01C|REQUIRED|M0-09-003|E2E-05,08|project.view + action-specific|
|CNT-01|FOUNDATION|M0-02-002; M0-09|TC-CONTENT-*|content.view|
|CNT-04|FOUNDATION|M0-03-006/008|MEDIA-GATE-*|content.view + asset.view/manage|
|CNT-05|FOUNDATION|M0-05-004|TC-CAST-*|catalog.view/link + inventory edit as applicable|
|EDT-01|REQUIRED|M0-06-*|E2E-01,06,09|editor.view + operation-specific core caps|
|EDT-02|REQUIRED|M0-06; M0-08|E2E-09,10|appearance.view/edit + validation/evidence as applicable|
|EDT-03|REQUIRED|M0-06-002; FRZ-05|TC-SCENE-*|scene.view/create/edit/split/merge|



Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

|**SCREEN**|**M0**|**OWNING PACKET**|**TESTS**|**AUTHORITY**|
|---|---|---|---|---|
|VIS-003|REQUIRED|M0-07-007/008/009|E2E-01,11|analysis.view|
|VIS-020|REQUIRED|M0-07-009|E2E-09,11|analysis.view/review + specific core caps|
|VIS-021|REQUIRED|M0-07; M0-08|E2E-09,10,11|analysis.review + specific capabilities|
|VIS-022|REQUIRED|M0-05/06/07|TC-EXTENDED-*|inventory.view + appearance.view|
|IAM-01|REQUIRED|M0-01-009|IAM-GATES|organization.members.view/manage; invitations manage|
|IAM-02|REQUIRED|M0-01-009|IAM-GATES|organization.members.* + project assignments as<br>applicable|
|IAM-03|REQUIRED|M0-01-005/009|AUTH-004..007|organization.invitations.manage|
|SET-01|REQUIRED|M0-09-004|TC-SET01-*|project.settings.general.view/manage + project.archive|
|SET-02|REQUIRED|M0-01-006/007; M0-09-004|IAM-GATES|project.assignments/access view/manage|
|SET-07|REQUIRED|M0-09-004|TC-AUDIT-*|project.settings.audit.view|
|OPS-01|FOUNDATION|M0-04|OPS-GATES|operations.view/retry/cancel + target visibility|
|OPS-02|FOUNDATION|M0-04|OPS-GATES|operations.view/retry/cancel + target visibility|



## **7. Capability -> Consumer Traceability** 

|**CAPABILITY**|**SCOPE**|**DELEGABLE**|**SURFACE**|**LEVEL**|**CONSUMERS**|
|---|---|---|---|---|---|
|organization.view|ORGANIZATION|NO|Organization|READ|ROLE:ORGANIZATION_ADMIN,<br>ROLE:PROJECT_MANAGER|
|organization.members.view|ORGANIZATION|NO|Team & Access|READ|IAM-01, ROLE:ORGANIZATION_ADMIN|
|organization.members.manage|ORGANIZATION|NO|Team & Access|MANAGE|ROLE:ORGANIZATION_ADMIN|
|organization.invitations.manage|ORGANIZATION|NO|Team & Access|MANAGE|IAM-03, ROLE:ORGANIZATION_ADMIN|
|project.create|ORGANIZATION|NO|Projects/New Project|OPERATE|HOME-01B, NPW-01, PRJ-01,<br>ROLE:ORGANIZATION_ADMIN,<br>ROLE:PROJECT_MANAGER|
|project.portfolio.view|ORGANIZATION|NO|Projects|READ|HOME-01A, PRJ-01,<br>ROLE:ADVERTISING_MANAGER,<br>ROLE:ANALYST,<br>ROLE:CLEARANCE_AUTHORITY,<br>ROLE:CLEARANCE_REVIEWER,<br>ROLE:CONTENT_EDITOR,<br>ROLE:INTEGRATION_ADMIN,<br>ROLE:ORGANIZATION_ADMIN,<br>ROLE:PP_INTERACTIVE_EDITOR,<br>ROLE:PROJECT_MANAGER,<br>ROLE:VALIDATOR, ROLE:VIEWER|



Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

|**CAPABILITY**|**SCOPE**|**DELEGABLE**|**SURFACE**|**LEVEL**|**CONSUMERS**|
|---|---|---|---|---|---|
|project.view|PROJECT|YES|Project|READ|NPW-02, PRO-01A, PRO-01B, PRO-01C,<br>ROLE:ADVERTISING_MANAGER,<br>ROLE:ANALYST,<br>ROLE:CLEARANCE_AUTHORITY,<br>ROLE:CLEARANCE_REVIEWER,<br>ROLE:CONTENT_EDITOR,<br>ROLE:INTEGRATION_ADMIN,<br>ROLE:ORGANIZATION_ADMIN,<br>ROLE:PP_INTERACTIVE_EDITOR,<br>ROLE:PROJECT_MANAGER,<br>ROLE:VALIDATOR, ROLE:VIEWER|
|project.manage|PROJECT|YES|Project|MANAGE|ROLE:ORGANIZATION_ADMIN,<br>ROLE:PROJECT_MANAGER|
|project.archive|PROJECT|YES|Settings|MANAGE|PRJ-01, ROLE:ORGANIZATION_ADMIN,<br>ROLE:PROJECT_MANAGER, SET-01|
|project.assignments.view|PROJECT|YES|Team & Access/Settings|READ|ROLE:ORGANIZATION_ADMIN,<br>ROLE:PROJECT_MANAGER|
|project.assignments.manage|PROJECT|YES|Team & Access/Settings|MANAGE|ROLE:ORGANIZATION_ADMIN,<br>ROLE:PROJECT_MANAGER|
|project.access.view|PROJECT|NO|Team & Access|READ|ROLE:INTEGRATION_ADMIN,<br>ROLE:ORGANIZATION_ADMIN,<br>ROLE:PROJECT_MANAGER|
|project.access.manage|PROJECT|NO|Team & Access|MANAGE|ROLE:ORGANIZATION_ADMIN,<br>ROLE:PROJECT_MANAGER|
|content.view|PROJECT|YES|Content|READ|CNT-01, CNT-04,<br>ROLE:ADVERTISING_MANAGER,<br>ROLE:ANALYST,<br>ROLE:CLEARANCE_AUTHORITY,<br>ROLE:CLEARANCE_REVIEWER,<br>ROLE:CONTENT_EDITOR,<br>ROLE:INTEGRATION_ADMIN,<br>ROLE:ORGANIZATION_ADMIN,<br>ROLE:PP_INTERACTIVE_EDITOR,<br>ROLE:PROJECT_MANAGER,<br>ROLE:VALIDATOR, ROLE:VIEWER|
|content.manage|PROJECT|YES|Content|MANAGE|ROLE:CONTENT_EDITOR,<br>ROLE:PROJECT_MANAGER|
|asset.view|PROJECT|YES|Media|READ|CNT-04, ROLE:ADVERTISING_MANAGER,<br>ROLE:ANALYST,<br>ROLE:CLEARANCE_AUTHORITY,<br>ROLE:CLEARANCE_REVIEWER,<br>ROLE:CONTENT_EDITOR,<br>ROLE:INTEGRATION_ADMIN,<br>ROLE:ORGANIZATION_ADMIN,<br>ROLE:PP_INTERACTIVE_EDITOR,<br>ROLE:PROJECT_MANAGER,<br>ROLE:VALIDATOR, ROLE:VIEWER|
|asset.upload|PROJECT|YES|Media|OPERATE|ROLE:CONTENT_EDITOR,<br>ROLE:PROJECT_MANAGER|
|asset.manage|PROJECT|YES|Media|MANAGE|ROLE:PROJECT_MANAGER|
|analysis.view|PROJECT|YES|Analysis|READ|ROLE:ADVERTISING_MANAGER,<br>ROLE:ANALYST,<br>ROLE:CLEARANCE_AUTHORITY,<br>ROLE:CLEARANCE_REVIEWER,<br>ROLE:CONTENT_EDITOR,<br>ROLE:ORGANIZATION_ADMIN,|



Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

|**CAPABILITY**|**SCOPE**|**DELEGABLE**|**SURFACE**|**LEVEL**|**CONSUMERS**|
|---|---|---|---|---|---|
||||||ROLE:PP_INTERACTIVE_EDITOR,<br>ROLE:PROJECT_MANAGER,<br>ROLE:VALIDATOR, ROLE:VIEWER, VIS-003,<br>VIS-020|
|analysis.run|PROJECT|YES|Analysis|OPERATE|ROLE:ANALYST, ROLE:PROJECT_MANAGER|
|analysis.review|PROJECT|YES|Analysis|OPERATE|ROLE:ADVERTISING_MANAGER,<br>ROLE:ANALYST,<br>ROLE:CLEARANCE_AUTHORITY,<br>ROLE:CLEARANCE_REVIEWER,<br>ROLE:CONTENT_EDITOR,<br>ROLE:PP_INTERACTIVE_EDITOR,<br>ROLE:PROJECT_MANAGER,<br>ROLE:VALIDATOR, VIS-021|
|editor.view|PROJECT|YES|Editor|READ|EDT-01, ROLE:ADVERTISING_MANAGER,<br>ROLE:ANALYST,<br>ROLE:CLEARANCE_AUTHORITY,<br>ROLE:CLEARANCE_REVIEWER,<br>ROLE:CONTENT_EDITOR,<br>ROLE:ORGANIZATION_ADMIN,<br>ROLE:PP_INTERACTIVE_EDITOR,<br>ROLE:PROJECT_MANAGER,<br>ROLE:VALIDATOR|
|inventory.view|PROJECT|YES|Editor/Analysis|READ|ROLE:ADVERTISING_MANAGER,<br>ROLE:ANALYST,<br>ROLE:CLEARANCE_AUTHORITY,<br>ROLE:CLEARANCE_REVIEWER,<br>ROLE:CONTENT_EDITOR,<br>ROLE:ORGANIZATION_ADMIN,<br>ROLE:PP_INTERACTIVE_EDITOR,<br>ROLE:PROJECT_MANAGER,<br>ROLE:VALIDATOR, ROLE:VIEWER, VIS-022|
|inventory.create|PROJECT|YES|Editor/Analysis|OPERATE|ROLE:ANALYST, ROLE:CONTENT_EDITOR,<br>ROLE:PROJECT_MANAGER|
|inventory.edit|PROJECT|YES|Editor/Analysis|OPERATE|ROLE:ANALYST, ROLE:CONTENT_EDITOR,<br>ROLE:PP_INTERACTIVE_EDITOR,<br>ROLE:PROJECT_MANAGER,<br>ROLE:VALIDATOR|
|inventory.merge|PROJECT|YES|Editor/Analysis|OPERATE|ROLE:ANALYST, ROLE:CONTENT_EDITOR,<br>ROLE:PROJECT_MANAGER|
|inventory.split|PROJECT|YES|Editor/Analysis|OPERATE|ROLE:ANALYST, ROLE:CONTENT_EDITOR,<br>ROLE:PROJECT_MANAGER|
|inventory.archive|PROJECT|YES|Editor/Analysis|OPERATE|ROLE:CONTENT_EDITOR,<br>ROLE:PROJECT_MANAGER|
|appearance.view|PROJECT|YES|Editor/Analysis|READ|EDT-02, ROLE:ADVERTISING_MANAGER,<br>ROLE:ANALYST,<br>ROLE:CLEARANCE_AUTHORITY,<br>ROLE:CLEARANCE_REVIEWER,<br>ROLE:CONTENT_EDITOR,<br>ROLE:ORGANIZATION_ADMIN,<br>ROLE:PP_INTERACTIVE_EDITOR,<br>ROLE:PROJECT_MANAGER,<br>ROLE:VALIDATOR, ROLE:VIEWER, VIS-022|
|appearance.create|PROJECT|YES|Editor/Analysis|OPERATE|ROLE:ANALYST, ROLE:CONTENT_EDITOR,<br>ROLE:PROJECT_MANAGER|
|appearance.edit|PROJECT|YES|Editor/Analysis|OPERATE|ROLE:ANALYST, ROLE:CONTENT_EDITOR,<br>ROLE:PP_INTERACTIVE_EDITOR,|



Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

|**CAPABILITY**|**SCOPE**|**DELEGABLE**|**SURFACE**|**LEVEL**|**CONSUMERS**|
|---|---|---|---|---|---|
||||||ROLE:PROJECT_MANAGER,<br>ROLE:VALIDATOR|
|appearance.archive|PROJECT|YES|Editor/Analysis|OPERATE|ROLE:CONTENT_EDITOR,<br>ROLE:PROJECT_MANAGER|
|scene.view|PROJECT|YES|Editor|READ|EDT-03, ROLE:ADVERTISING_MANAGER,<br>ROLE:ANALYST,<br>ROLE:CLEARANCE_AUTHORITY,<br>ROLE:CLEARANCE_REVIEWER,<br>ROLE:CONTENT_EDITOR,<br>ROLE:ORGANIZATION_ADMIN,<br>ROLE:PP_INTERACTIVE_EDITOR,<br>ROLE:PROJECT_MANAGER,<br>ROLE:VALIDATOR, ROLE:VIEWER|
|scene.create|PROJECT|YES|Editor|OPERATE|ROLE:CONTENT_EDITOR,<br>ROLE:PROJECT_MANAGER|
|scene.edit|PROJECT|YES|Editor|OPERATE|ROLE:CONTENT_EDITOR,<br>ROLE:PROJECT_MANAGER,<br>ROLE:VALIDATOR|
|scene.split|PROJECT|YES|Editor|OPERATE|ROLE:CONTENT_EDITOR,<br>ROLE:PROJECT_MANAGER|
|scene.merge|PROJECT|YES|Editor|OPERATE|ROLE:CONTENT_EDITOR,<br>ROLE:PROJECT_MANAGER|
|relationship.view|PROJECT|YES|Analysis/Editor|READ|ROLE:ADVERTISING_MANAGER,<br>ROLE:ANALYST,<br>ROLE:CLEARANCE_AUTHORITY,<br>ROLE:CLEARANCE_REVIEWER,<br>ROLE:CONTENT_EDITOR,<br>ROLE:ORGANIZATION_ADMIN,<br>ROLE:PROJECT_MANAGER,<br>ROLE:VALIDATOR, ROLE:VIEWER|
|relationship.edit|PROJECT|YES|Analysis/Editor|OPERATE|ROLE:ANALYST,<br>ROLE:CLEARANCE_AUTHORITY,<br>ROLE:CONTENT_EDITOR,<br>ROLE:PROJECT_MANAGER,<br>ROLE:VALIDATOR|
|validation.view|PROJECT|YES|Analysis/Editor|READ|ROLE:ADVERTISING_MANAGER,<br>ROLE:ANALYST,<br>ROLE:CLEARANCE_AUTHORITY,<br>ROLE:CLEARANCE_REVIEWER,<br>ROLE:CONTENT_EDITOR,<br>ROLE:ORGANIZATION_ADMIN,<br>ROLE:PP_INTERACTIVE_EDITOR,<br>ROLE:PROJECT_MANAGER,<br>ROLE:VALIDATOR, ROLE:VIEWER|
|validation.decide|PROJECT|YES|Analysis/Editor|OPERATE|ROLE:CLEARANCE_AUTHORITY,<br>ROLE:VALIDATOR|
|evidence.view|PROJECT|YES|Analysis/Editor|READ|ROLE:ADVERTISING_MANAGER,<br>ROLE:ANALYST,<br>ROLE:CLEARANCE_AUTHORITY,<br>ROLE:CLEARANCE_REVIEWER,<br>ROLE:CONTENT_EDITOR,<br>ROLE:ORGANIZATION_ADMIN,<br>ROLE:PP_INTERACTIVE_EDITOR,<br>ROLE:PROJECT_MANAGER,<br>ROLE:VALIDATOR, ROLE:VIEWER|
|evidence.create|PROJECT|YES|Analysis/Editor|OPERATE|ROLE:ANALYST,|



Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

|**CAPABILITY**|**SCOPE**|**DELEGABLE**|**SURFACE**|**LEVEL**|**CONSUMERS**|
|---|---|---|---|---|---|
||||||ROLE:CLEARANCE_AUTHORITY,<br>ROLE:CLEARANCE_REVIEWER,<br>ROLE:CONTENT_EDITOR,<br>ROLE:PP_INTERACTIVE_EDITOR,<br>ROLE:PROJECT_MANAGER,<br>ROLE:VALIDATOR|
|catalog.view|PROJECT|YES|Catalog/Analysis|READ|CNT-05, ROLE:ADVERTISING_MANAGER,<br>ROLE:ANALYST,<br>ROLE:CLEARANCE_AUTHORITY,<br>ROLE:CLEARANCE_REVIEWER,<br>ROLE:CONTENT_EDITOR,<br>ROLE:ORGANIZATION_ADMIN,<br>ROLE:PP_INTERACTIVE_EDITOR,<br>ROLE:PROJECT_MANAGER,<br>ROLE:VALIDATOR, ROLE:VIEWER|
|catalog.link|PROJECT|YES|Analysis/Editor|OPERATE|ROLE:ADVERTISING_MANAGER,<br>ROLE:ANALYST, ROLE:CONTENT_EDITOR,<br>ROLE:PP_INTERACTIVE_EDITOR,<br>ROLE:PROJECT_MANAGER,<br>ROLE:VALIDATOR|
|operations.view|PROJECT|YES|Operations/embedded|READ|HOME-01C, OPS-01, OPS-02,<br>ROLE:ADVERTISING_MANAGER,<br>ROLE:ANALYST,<br>ROLE:CLEARANCE_AUTHORITY,<br>ROLE:CLEARANCE_REVIEWER,<br>ROLE:CONTENT_EDITOR,<br>ROLE:INTEGRATION_ADMIN,<br>ROLE:ORGANIZATION_ADMIN,<br>ROLE:PP_INTERACTIVE_EDITOR,<br>ROLE:PROJECT_MANAGER,<br>ROLE:VALIDATOR|
|operations.retry|PROJECT|YES|Operations/embedded|OPERATE|ROLE:ORGANIZATION_ADMIN,<br>ROLE:PROJECT_MANAGER|
|operations.cancel|PROJECT|YES|Operations/embedded|OPERATE|ROLE:ORGANIZATION_ADMIN,<br>ROLE:PROJECT_MANAGER|
|project.settings.general.view|PROJECT|YES|Settings|READ|ROLE:INTEGRATION_ADMIN,<br>ROLE:ORGANIZATION_ADMIN,<br>ROLE:PROJECT_MANAGER, SET-01|
|project.settings.general.manage|PROJECT|YES|Settings|MANAGE|ROLE:ORGANIZATION_ADMIN,<br>ROLE:PROJECT_MANAGER|
|project.settings.audit.view|PROJECT|YES|Settings/Audit|READ|ROLE:INTEGRATION_ADMIN,<br>ROLE:ORGANIZATION_ADMIN,<br>ROLE:PROJECT_MANAGER, SET-07|
|internal.platform.view|INTERNAL_PLATFORM|NO|Administration|READ|ROLE:INTERNAL_ADMIN|
|internal.platform.manage|INTERNAL_PLATFORM|NO|Administration|MANAGE|ROLE:INTERNAL_ADMIN|
|support.access.view|INTERNAL_PLATFORM|NO|Administration|READ|ROLE:INTERNAL_ADMIN|
|support.access.issue|INTERNAL_PLATFORM|NO|Administration|MANAGE|ROLE:INTERNAL_ADMIN|
|support.access.revoke|INTERNAL_PLATFORM|NO|Administration|MANAGE|ROLE:INTERNAL_ADMIN|
|support.access.use|INTERNAL_PLATFORM|NO|Customer support|OPERATE|ROLE:INTERNAL_ADMIN|
|pilot.entitlement.issue|INTERNAL_PLATFORM|NO|Administration|MANAGE|ROLE:INTERNAL_ADMIN|
|user.account.suspend|INTERNAL_PLATFORM|NO|Administration|MANAGE|ROLE:INTERNAL_ADMIN|



Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

## **8. Test/Gate Evidence Catalogue** 

|**GATE FAMILY**|**TEST IDs**|**EVIDENCE PURPOSE**|
|---|---|---|
|AUTH-GATE|AUTH-001..015|Identity verification, invitation, bootstrap, session invalidation, return-to and no<br>autojoin.|
|IAM-GATE|TC-M001-*|Org isolation, project least privilege, collaboration ceiling, need-to-know,<br>lifecycle revocation.|
|MEDIA-GATE|TC-M03003..007-*|Source fidelity, temporal integrity, browser operability, derivative isolation,<br>secure access.|
|OPS-GATE|OPS-*|Durable, idempotent, domain-safe, authorization-safe, observable operations.|
|CORE-GATE|TC-M005/M006-*|Inventory/Appearance/Scene exactness, split/merge lineage, concurrency and<br>taxonomy versioning.|
|ANALYSIS-GATE|TC-M007-*|Frozen manifests, candidate proposals, snapshot currentness, teaser/detail<br>and vertical relevance.|
|VALIDATION-GATE|TC-M008-*|Dimensional validation, impact/revalidation, evidence source integrity,<br>immutable history.|
|PRODUCT-GATE|E2E-01..12|Canonical studio/agency/media/recovery/entitlement/re-entry product journeys.|
|A11Y-GATE|A11Y-*|WCAG 2.2 AA keyboard, focus, semantics, errors, contrast, player/timecode<br>and dynamic status.|
|RECOVERY-GATE|DR-*|Backup restore, RPO/RTO, queue/outbox recovery and missing object<br>reconciliation.|



## **9. Orphan checks required before implementation freeze** 

- Every REQUIRED/FOUNDATION screen in the Screen Contract Matrix has at least one owning packet and acceptance-test family. 

- Every M0 capability has a RolePackage and/or explicit internal/screen consumer; no permission exists solely as dead configuration. 

- Every governed write command declares authorization, optimistic concurrency when applicable, validation impact and audit/event behavior. 

- Every packet has a Guide/decision basis and a Wave gate. 

- Every P0 correction is visible in at least one normative artifact; no correction exists only in conversation history. 

- Any future orphan found by CI/document lint is a freeze regression until classified as intentional/deferred. 

Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

## **10. Status at FRZ-15 completion** 

|**MEASURE**|**RESULT**|
|---|---|
|Original external-audit P0 findings|18|
|P0 decisions defined|18 / 18|
|P1 findings|13|
|P1 scoped/resolved for freeze|13 / 13|
|New normative decisions|D199-D218|
|Strategic decisions open|0|
|FRZ-16 status|PASS - P0=0; specification set frozen for implementation|



Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

## **11. FRZ-16 final status** 

### **FRZ-16 PASS - P0 = 0** 

Independent re-audit completed on the remediated artifacts. No new specification P0 was found. The Guide, Greenfield, Screen Contract Matrix and this closure record may now be frozen for implementation. 

|**MEASURE**|**FINAL RESULT**|
|---|---|
|Original external-audit P0 findings|18|
|Original P0 represented and re-tested|18 / 18 PASS|
|P1 findings|13 / 13 resolved, scoped or explicitly deferred|
|Automated cross-artifact verification|138 / 138 PASS|
|New P0 discovered by FRZ-16|0|
|Strategic decisions open|0|
|FRZ-16 verdict|READY TO FREEZE|
|Specification status|FROZEN FOR IMPLEMENTATION|



- Structural changes to frozen Sources of Truth, authorization, exact-version/temporal semantics, Operations, Validation/Evidence or entitlement boundaries require explicit ADR/change control. 

- Production evidence still required before go-live: WorkOS DPA/security/residency review, production InitialAnalysisPolicy values, retention/legal reconciliation, backup/restore proof, performance proof and WCAG 2.2 AA audit. 

- These production conditions do not authorize implementation shortcuts and do not reopen the frozen product contract. 

Confidential - IwantIt 

