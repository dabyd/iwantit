# lwantit 

~~EE~~ 



IwantIt  |  Content Intelligence Infrastructure 

## **Document control** 

|**FIELD**|**VALUE**|
|---|---|
|Artifact|IwantIt M0 Screen Contract Matrix|
|Version|v1.0|
|Status|Frozen M0 implementation baseline|
|Date|24 August 2026|
|Normative basis|Guía Maestra v2.22 FROZEN + Greenfield Execution Pack v1.1 + approved wireframe baseline|
|Purpose|Define exactly what is contractual in each M0 required/foundation screen and prevent incidental<br>raster data from becoming domain truth.|
|Change control|Structural changes after freeze require ADR and owner approval.|



#### **Normative rule** 

For implementation, written screen contract controls purpose, data, commands, authorization, entitlements, exact-version behavior and non-happy states. Approved wireframes control visual reference and composition. Incidental sample values/copy in a raster are not domain truth. 

## **1. Status vocabulary** 

|**STATUS**|**MEANING**|
|---|---|
|REQUIRED|Must be implemented and pass M0 exit gates.|
|FOUNDATION|Underlying capability/read model is required in M0; full richer workspace may be deferred.|
|DEFERRED|Not an M0 implementation gate; must not accidentally appear as half-authorized functionality.|



## **2. M0 screen coverage summary** 

|**SCREEN**|**M0**|**ROUTE**|**PURPOSE**|**OWNING PACKETS**|
|---|---|---|---|---|
|AUTH-01|REQUIRED|/auth/*|Authenticate or register a verified human through<br>hosted AuthKit.|AUTH-00-001/002/003|
|AUTH-02|REQUIRED|/onboarding/organization|Bootstrap first Organization for a verified User<br>with zero active memberships.|AUTH-00-004|



Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

|**SCREEN**|**M0**|**ROUTE**|**PURPOSE**|**OWNING PACKETS**|
|---|---|---|---|---|
|AUTH-03|REQUIRED|/organizations/choose|Select explicit Acting Organization when multiple<br>active memberships exist.|AUTH-00-002; M0-01-004|
|AUTH-04|REQUIRED|/invitations/:id|Continue invitation securely through identity<br>verification and atomic Membership acceptance.|AUTH-00-005; M0-01-005|
|HOME-01A|REQUIRED|/home|Authorized re-entry when useful projects/work<br>exist.|M0-09-001|
|HOME-01B|REQUIRED|/home|Home empty/new state.|M0-09-001|
|HOME-01C|REQUIRED|/home|Home attention/processing state without leakage.|M0-09-001|
|PRJ-01|REQUIRED|/projects|Authorized project portfolio table with<br>search/filter/counts over same universe.|M0-09-002|
|NPW-01|REQUIRED|/projects/new|Minimal project creation and optional media<br>upload.|M0-02-007/005/010|
|NPW-02|REQUIRED|/projects/:id/preparing|Durable Initial Analysis preparation/re-entry<br>projection.|M0-02-008; M0-04|
|PRO-01A|REQUIRED|/projects/:id|Project overview while preparing.|M0-09-003|
|PRO-01B|REQUIRED|/projects/:id|Primary normal Project Overview reference state.|M0-09-003|
|PRO-01C|REQUIRED|/projects/:id|Project Overview with attention/degraded<br>readiness.|M0-09-003|
|CNT-01|FOUNDATION|/projects/:id/content|Content/CV entry and exact-version awareness.|M0-02-002; M0-09|
|CNT-04|FOUNDATION|/projects/:id/content/versions/:cv|Inspect exact ContentVersion and media<br>readiness.|M0-03-006/008|
|CNT-05|FOUNDATION|/projects/:id/cast|Minimum cast/Person-to-Character resolution.|M0-05-004|
|EDT-01|REQUIRED|/projects/:id/editor|Temporal Core authoring with normalized media<br>clock and one track per InventoryItem.|M0-06-*|
|EDT-02|REQUIRED|/projects/:id/editor?inspector=appearance|Appearance inspector and governed<br>correction/validation/evidence.|M0-06; M0-08|
|EDT-03|REQUIRED|/projects/:id/editor?mode=scenes|SceneStructure authoring and validation.|M0-06-002; FRZ-05|
|VIS-003|REQUIRED|/projects/:id/analysis|Analysis Overview: Business Opportunities<br>above Key Contexts.|M0-07-007/008/009|
|VIS-020|REQUIRED|/projects/:id/analysis/workspace|Appearance-oriented Analysis workspace for<br>review and materialization.|M0-07-009|
|VIS-021|REQUIRED|/projects/:id/analysis/workspace?inspector=1|Inspector for candidate/item/appearance<br>provenance and review.|M0-07; M0-08|
|VIS-022|REQUIRED|/projects/:id/analysis/elements/:item|Extended element view with derived metrics and<br>appearances.|M0-05/06/07|
|IAM-01|REQUIRED|/team-access|Organization members/invitations/client-org<br>projection.|M0-01-009|



Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

|**SCREEN**|**M0**|**ROUTE**|**PURPOSE**|**OWNING PACKETS**|
|---|---|---|---|---|
|IAM-02|REQUIRED|/team-access/:membership|Member overview and effective project<br>access/activity.|M0-01-009|
|IAM-03|REQUIRED|/team-access/invite|Invite to Organization with immutable<br>RolePackageVersion selection.|M0-01-005/009|
|SET-01|REQUIRED|/projects/:id/settings/general|Basic project identity/lifecycle settings.|M0-09-004|
|SET-02|REQUIRED|/projects/:id/settings/access|Project access projection over<br>ProjectAccessGrant.|M0-01-006/007; M0-09-004|
|SET-07|REQUIRED|/projects/:id/settings/audit|Basic authorized project/IAM audit trail.|M0-09-004|
|OPS-01|FOUNDATION|/operations|Authorized operations list/projection; backend<br>runtime is mandatory.|M0-04|
|OPS-02|FOUNDATION|/operations/:id|Operation detail with real<br>steps/attempts/log/result references.|M0-04|



## **3. Deferred screen register** 

|**SCREEN / FAMILY**|**REASON**|**GATE**|
|---|---|---|
|CNT-02|Rich multi-version management UX|M1+ / trigger-based|
|CNT-03|Advanced version comparison/reconform|M1+|
|IAM-04|Standalone access inspector|Later|
|SET-03|Modules & Licenses settings|M1|
|SET-04|Integrations settings|M1|
|SET-05|Delivery settings|M1|
|SET-06|Advanced project settings|Later|
|CAT-01/02/03|Full Catalog workspaces|Later than M0 foundation|
|PP-*|Project Passport workspaces|Trigger-based/later|
|INT-*|Full Integrations workspaces|M1|
|ADM-*|Full internal Administration suite|M1/M2|
|Advertising/Interactive/Clearance workspaces|Product gates M1/M2|M1/M2|



Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

## **4. Global screen invariants** 

- All read models are authorization-before-composition. Hidden resources/surfaces are omitted from serialization, search, counts, facets, activity and notifications. 

- Every Project-temporal action binds an explicit ContentVersion ID. No screen command may resolve "current/latest video" as an authority shortcut. 

- Acting Organization is explicit and validated. Switching it invalidates/recomputes Home, Projects, navigation, counts and WorkContext. 

- Entitlement and IAM remain separate. A screen may be authorized as a surface while detailed exploitable data is still gated by UsageRight/ProductAccessPolicy. 

- 403 is used only where revealing resource existence is safe; otherwise no-existence-safe denial is 404. 

- All writable governed screens use optimistic concurrency where the owning aggregate declares revision. 

- All deep links are reauthorized server-side. WorkContext/return_context never grants authority. 

- Loading/empty/processing/error/forbidden/outdated/conflict are product states and must not be filled with fabricated data. 

- Accessibility target: WCAG 2.2 AA across all REQUIRED/FOUNDATION screens. 

Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

## **1. AUTH-01** 

|**FIELD**|**CONTRACT**|
|---|---|
|M0 status|REQUIRED|
|Route|/auth/*|
|Purpose|Authenticate or register a verified human through hosted AuthKit.|
|Primary actors|Prospect, invited user, existing user|
|Queries / read models|Provider session state; IwantIt identity resolution|
|Commands|Provider login/signup/verify/recover; create/resolve User/AuthIdentity|
|Capabilities / authority|None before authentication|
|Entitlement / product access|None|
|Exact version behavior|N/A|
|Non-happy states|loading, provider error, verification required, suspended|
|Deep-link / return behavior|validated return_to only|
|Owning packets|AUTH-00-001/002/003|
|Acceptance tests|AUTH-001,002,013|



### **Implementation obligations** 

- This screen is part of the M0 canonical product journey and must be present in staging before M0-GATE-6 can pass. 

- Server returns only fields and actions currently authorized for the actor/context. 

- UI action visibility is derived from server authorization/product policy, not from static role-name checks. 

- A stale or conflicting writable resource cannot be silently overwritten; show a reload/review conflict state where applicable. 

- Approved wireframe composition may be refined for consistency, but content semantics and commands must follow this contract. 

- Authentication state must never become a proxy for Organization/Project authority. 

### **Reference UI interpretation** 

The approved raster is a visual reference. Sample labels, names, counts, scores, timestamps or statuses are illustrative unless explicitly present in the written Guide/Greenfield contract. 

Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

## **2. AUTH-02** 

|**FIELD**|**CONTRACT**|
|---|---|
|M0 status|REQUIRED|
|Route|/onboarding/organization|
|Purpose|Bootstrap first Organization for a verified User with zero active memberships.|
|Primary actors|Verified prospect|
|Queries / read models|GET /me/context|
|Commands|BootstrapOrganization|
|Capabilities / authority|Authenticated verified User; zero active memberships|
|Entitlement / product access|None|
|Exact version behavior|N/A|
|Non-happy states|empty, submitting, conflict, forbidden|
|Deep-link / return behavior|to Quick Create after commit|
|Owning packets|AUTH-00-004|
|Acceptance tests|AUTH-001,014,015|



### **Implementation obligations** 

- This screen is part of the M0 canonical product journey and must be present in staging before M0-GATE-6 can pass. 

- Server returns only fields and actions currently authorized for the actor/context. 

- UI action visibility is derived from server authorization/product policy, not from static role-name checks. 

- A stale or conflicting writable resource cannot be silently overwritten; show a reload/review conflict state where applicable. 

- Approved wireframe composition may be refined for consistency, but content semantics and commands must follow this contract. 

- Authentication state must never become a proxy for Organization/Project authority. 

### **Reference UI interpretation** 

The approved raster is a visual reference. Sample labels, names, counts, scores, timestamps or statuses are illustrative unless explicitly present in the written Guide/Greenfield contract. 

Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

## **3. AUTH-03** 

|**FIELD**|**CONTRACT**|
|---|---|
|M0 status|REQUIRED|
|Route|/organizations/choose|
|Purpose|Select explicit Acting Organization when multiple active memberships exist.|
|Primary actors|Multi-org user|
|Queries / read models|GET /me/context|
|Commands|SetActingOrganization|
|Capabilities / authority|Active Membership in selected Organization|
|Entitlement / product access|None|
|Exact version behavior|N/A|
|Non-happy states|loading, none active, stale membership|
|Deep-link / return behavior|reauthorize intended route|
|Owning packets|AUTH-00-002; M0-01-004|
|Acceptance tests|AUTH-003,010|



### **Implementation obligations** 

- This screen is part of the M0 canonical product journey and must be present in staging before M0-GATE-6 can pass. 

- Server returns only fields and actions currently authorized for the actor/context. 

- UI action visibility is derived from server authorization/product policy, not from static role-name checks. 

- A stale or conflicting writable resource cannot be silently overwritten; show a reload/review conflict state where applicable. 

- Approved wireframe composition may be refined for consistency, but content semantics and commands must follow this contract. 

- Authentication state must never become a proxy for Organization/Project authority. 

### **Reference UI interpretation** 

The approved raster is a visual reference. Sample labels, names, counts, scores, timestamps or statuses are illustrative unless explicitly present in the written Guide/Greenfield contract. 

Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

## **4. AUTH-04** 

|**FIELD**|**CONTRACT**|
|---|---|
|M0 status|REQUIRED|
|Route|/invitations/:id|
|Purpose|Continue invitation securely through identity verification and atomic Membership acceptance.|
|Primary actors|Invited user|
|Queries / read models|Invitation safe preview|
|Commands|AcceptInvitation|
|Capabilities / authority|Verified invited identity|
|Entitlement / product access|None|
|Exact version behavior|N/A|
|Non-happy states|pending, expired, revoked, wrong identity, already satisfied|
|Deep-link / return behavior|no silent org switch|
|Owning packets|AUTH-00-005; M0-01-005|
|Acceptance tests|AUTH-004..007|



### **Implementation obligations** 

- This screen is part of the M0 canonical product journey and must be present in staging before M0-GATE-6 can pass. 

- Server returns only fields and actions currently authorized for the actor/context. 

- UI action visibility is derived from server authorization/product policy, not from static role-name checks. 

- A stale or conflicting writable resource cannot be silently overwritten; show a reload/review conflict state where applicable. 

- Approved wireframe composition may be refined for consistency, but content semantics and commands must follow this contract. 

- Authentication state must never become a proxy for Organization/Project authority. 

### **Reference UI interpretation** 

The approved raster is a visual reference. Sample labels, names, counts, scores, timestamps or statuses are illustrative unless explicitly present in the written Guide/Greenfield contract. 

Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

## **5. HOME-01A** 

|**FIELD**|**CONTRACT**|
|---|---|
|M0 status|REQUIRED|
|Route|/home|
|Purpose|Authorized re-entry when useful projects/work exist.|
|Primary actors|All product users|
|Queries / read models|AuthorizedHomeQuery|
|Commands|New Project / resume|
|Capabilities / authority|project.portfolio.view as applicable|
|Entitlement / product access|Surface-specific|
|Exact version behavior|Exact IDs in resume targets|
|Non-happy states|loading, healthy, attention|
|Deep-link / return behavior|WorkContext reauthorized|
|Owning packets|M0-09-001|
|Acceptance tests|E2E-01,02,12|



### **Implementation obligations** 

- This screen is part of the M0 canonical product journey and must be present in staging before M0-GATE-6 can pass. 

- Server returns only fields and actions currently authorized for the actor/context. 

- UI action visibility is derived from server authorization/product policy, not from static role-name checks. 

- A stale or conflicting writable resource cannot be silently overwritten; show a reload/review conflict state where applicable. 

- Approved wireframe composition may be refined for consistency, but content semantics and commands must follow this contract. 

### **Reference UI interpretation** 

These are authorized Home variants (work available, empty/new, attention/processing). They must be generated from the same server-authorized composition contract. 

Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

## **6. HOME-01B** 

|**FIELD**|**CONTRACT**|
|---|---|
|M0 status|REQUIRED|
|Route|/home|
|Purpose|Home empty/new state.|
|Primary actors|New org user|
|Queries / read models|AuthorizedHomeQuery|
|Commands|New Project|
|Capabilities / authority|project.create|
|Entitlement / product access|None|
|Exact version behavior|N/A|
|Non-happy states|empty|
|Deep-link / return behavior|to NPW-01|
|Owning packets|M0-09-001|
|Acceptance tests|E2E-01|



### **Implementation obligations** 

- This screen is part of the M0 canonical product journey and must be present in staging before M0-GATE-6 can pass. 

- Server returns only fields and actions currently authorized for the actor/context. 

- UI action visibility is derived from server authorization/product policy, not from static role-name checks. 

- A stale or conflicting writable resource cannot be silently overwritten; show a reload/review conflict state where applicable. 

- Approved wireframe composition may be refined for consistency, but content semantics and commands must follow this contract. 

### **Reference UI interpretation** 

These are authorized Home variants (work available, empty/new, attention/processing). They must be generated from the same server-authorized composition contract. 

Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

## **7. HOME-01C** 

|**FIELD**|**CONTRACT**|
|---|---|
|M0 status|REQUIRED|
|Route|/home|
|Purpose|Home attention/processing state without leakage.|
|Primary actors|Authorized operators|
|Queries / read models|AuthorizedHomeQuery + operation summaries|
|Commands|Resume / retry where authorized|
|Capabilities / authority|operations.view + target visibility|
|Entitlement / product access|Surface-specific|
|Exact version behavior|Exact operation target|
|Non-happy states|processing, needs attention, partial|
|Deep-link / return behavior|Project/operation routes reauthorize|
|Owning packets|M0-09-001|
|Acceptance tests|E2E-07,08,12|



### **Implementation obligations** 

- This screen is part of the M0 canonical product journey and must be present in staging before M0-GATE-6 can pass. 

- Server returns only fields and actions currently authorized for the actor/context. 

- UI action visibility is derived from server authorization/product policy, not from static role-name checks. 

- A stale or conflicting writable resource cannot be silently overwritten; show a reload/review conflict state where applicable. 

- Approved wireframe composition may be refined for consistency, but content semantics and commands must follow this contract. 

### **Reference UI interpretation** 

These are authorized Home variants (work available, empty/new, attention/processing). They must be generated from the same server-authorized composition contract. 

Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

## **8. PRJ-01** 

|**FIELD**|**CONTRACT**|
|---|---|
|M0 status|REQUIRED|
|Route|/projects|
|Purpose|Authorized project portfolio table with search/filter/counts over same universe.|
|Primary actors|All product users|
|Queries / read models|AuthorizedProjectsQuery|
|Commands|New Project; archive where authorized|
|Capabilities / authority|project.portfolio.view / project.create / project.archive|
|Entitlement / product access|None for project shell|
|Exact version behavior|Project row binds exact project|
|Non-happy states|loading, empty, error|
|Deep-link / return behavior|row route server-authorized|
|Owning packets|M0-09-002|
|Acceptance tests|E2E-02,03,12|



### **Implementation obligations** 

- This screen is part of the M0 canonical product journey and must be present in staging before M0-GATE-6 can pass. 

- Server returns only fields and actions currently authorized for the actor/context. 

- UI action visibility is derived from server authorization/product policy, not from static role-name checks. 

- A stale or conflicting writable resource cannot be silently overwritten; show a reload/review conflict state where applicable. 

- Approved wireframe composition may be refined for consistency, but content semantics and commands must follow this contract. 

### **Reference UI interpretation** 

The approved raster is a visual reference. Sample labels, names, counts, scores, timestamps or statuses are illustrative unless explicitly present in the written Guide/Greenfield contract. 

Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

## **9. NPW-01** 

|**FIELD**|**CONTRACT**|
|---|---|
|M0 status|REQUIRED|
|Route|/projects/new|
|Purpose|Minimal project creation and optional media upload.|
|Primary actors|Org Admin, Project Manager|
|Queries / read models|Upload policy + current acting org|
|Commands|CreateProjectWithoutVideo; QuickCreate|
|Capabilities / authority|project.create|
|Entitlement / product access|Initial scan policy, not license bypass|
|Exact version behavior|Creates CV-001 explicitly|
|Non-happy states|uploading, invalid, conflict, retry|
|Deep-link / return behavior|owner from Acting Org|
|Owning packets|M0-02-007/005/010|
|Acceptance tests|E2E-01,05|



### **Implementation obligations** 

- This screen is part of the M0 canonical product journey and must be present in staging before M0-GATE-6 can pass. 

- Server returns only fields and actions currently authorized for the actor/context. 

- UI action visibility is derived from server authorization/product policy, not from static role-name checks. 

- A stale or conflicting writable resource cannot be silently overwritten; show a reload/review conflict state where applicable. 

- Approved wireframe composition may be refined for consistency, but content semantics and commands must follow this contract. 

### **Reference UI interpretation** 

The approved raster is a visual reference. Sample labels, names, counts, scores, timestamps or statuses are illustrative unless explicitly present in the written Guide/Greenfield contract. 

Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

## **10. NPW-02** 

|**FIELD**|**CONTRACT**|
|---|---|
|M0 status|REQUIRED|
|Route|/projects/:id/preparing|
|Purpose|Durable Initial Analysis preparation/re-entry projection.|
|Primary actors|Project user with applicable access|
|Queries / read models|PreparingProjectProjection|
|Commands|Retry/cancel only if authorized|
|Capabilities / authority|project.view + target-specific operation/analysis caps|
|Entitlement / product access|Teaser/detail policy|
|Exact version behavior|Exact CV and AnalysisRun|
|Non-happy states|preparing, understanding, ready, needs attention|
|Deep-link / return behavior|resume via exact project/CV|
|Owning packets|M0-02-008; M0-04|
|Acceptance tests|E2E-07,08,12|



### **Implementation obligations** 

- This screen is part of the M0 canonical product journey and must be present in staging before M0-GATE-6 can pass. 

- Server returns only fields and actions currently authorized for the actor/context. 

- UI action visibility is derived from server authorization/product policy, not from static role-name checks. 

- A stale or conflicting writable resource cannot be silently overwritten; show a reload/review conflict state where applicable. 

- Approved wireframe composition may be refined for consistency, but content semantics and commands must follow this contract. 

### **Reference UI interpretation** 

The approved raster is a visual reference. Sample labels, names, counts, scores, timestamps or statuses are illustrative unless explicitly present in the written Guide/Greenfield contract. 

Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

## **11. PRO-01A** 

|**FIELD**|**CONTRACT**|
|---|---|
|M0 status|REQUIRED|
|Route|/projects/:id|
|Purpose|Project overview while preparing.|
|Primary actors|Project users|
|Queries / read models|ProjectOverviewProjection|
|Commands|Authorized next actions|
|Capabilities / authority|project.view|
|Entitlement / product access|Surface-specific|
|Exact version behavior|Exact CV shown|
|Non-happy states|preparing|
|Deep-link / return behavior|to preparing/analysis/editor|
|Owning packets|M0-09-003|
|Acceptance tests|E2E-05,08|



### **Implementation obligations** 

- This screen is part of the M0 canonical product journey and must be present in staging before M0-GATE-6 can pass. 

- Server returns only fields and actions currently authorized for the actor/context. 

- UI action visibility is derived from server authorization/product policy, not from static role-name checks. 

- A stale or conflicting writable resource cannot be silently overwritten; show a reload/review conflict state where applicable. 

- Approved wireframe composition may be refined for consistency, but content semantics and commands must follow this contract. 

### **Reference UI interpretation** 

These are state variants of one Project Overview contract. PRO-01B is the normal reference state; A/C express preparing/attention behavior without creating separate domain models. 

Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

## **12. PRO-01B** 

|**FIELD**|**CONTRACT**|
|---|---|
|M0 status|REQUIRED|
|Route|/projects/:id|
|Purpose|Primary normal Project Overview reference state.|
|Primary actors|Project users|
|Queries / read models|ProjectOverviewProjection|
|Commands|Authorized next actions|
|Capabilities / authority|project.view|
|Entitlement / product access|Surface-specific|
|Exact version behavior|Exact CV shown|
|Non-happy states|ready/healthy|
|Deep-link / return behavior|module deep links reauthorize|
|Owning packets|M0-09-003|
|Acceptance tests|E2E-01,12|



### **Implementation obligations** 

- This screen is part of the M0 canonical product journey and must be present in staging before M0-GATE-6 can pass. 

- Server returns only fields and actions currently authorized for the actor/context. 

- UI action visibility is derived from server authorization/product policy, not from static role-name checks. 

- A stale or conflicting writable resource cannot be silently overwritten; show a reload/review conflict state where applicable. 

- Approved wireframe composition may be refined for consistency, but content semantics and commands must follow this contract. 

### **Reference UI interpretation** 

These are state variants of one Project Overview contract. PRO-01B is the normal reference state; A/C express preparing/attention behavior without creating separate domain models. 

Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

## **13. PRO-01C** 

|**FIELD**|**CONTRACT**|
|---|---|
|M0 status|REQUIRED|
|Route|/projects/:id|
|Purpose|Project Overview with attention/degraded readiness.|
|Primary actors|Project users|
|Queries / read models|ProjectOverviewProjection|
|Commands|Retry/fix actions by policy|
|Capabilities / authority|project.view + action-specific|
|Entitlement / product access|Surface-specific|
|Exact version behavior|Exact CV shown|
|Non-happy states|needs attention|
|Deep-link / return behavior|to owner surface|
|Owning packets|M0-09-003|
|Acceptance tests|E2E-05,08|



### **Implementation obligations** 

- This screen is part of the M0 canonical product journey and must be present in staging before M0-GATE-6 can pass. 

- Server returns only fields and actions currently authorized for the actor/context. 

- UI action visibility is derived from server authorization/product policy, not from static role-name checks. 

- A stale or conflicting writable resource cannot be silently overwritten; show a reload/review conflict state where applicable. 

- Approved wireframe composition may be refined for consistency, but content semantics and commands must follow this contract. 

### **Reference UI interpretation** 

These are state variants of one Project Overview contract. PRO-01B is the normal reference state; A/C express preparing/attention behavior without creating separate domain models. 

Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

## **14. CNT-01** 

|**FIELD**|**CONTRACT**|
|---|---|
|M0 status|FOUNDATION|
|Route|/projects/:id/content|
|Purpose|Content/CV entry and exact-version awareness.|
|Primary actors|Content-capable roles|
|Queries / read models|ContentDetailQuery|
|Commands|Upload/manage where authorized|
|Capabilities / authority|content.view|
|Entitlement / product access|None for metadata shell|
|Exact version behavior|Explicit ContentVersion IDs|
|Non-happy states|empty/no media/loading|
|Deep-link / return behavior|exact CV routes|
|Owning packets|M0-02-002; M0-09|
|Acceptance tests|TC-CONTENT-*|



### **Implementation obligations** 

- The underlying contract/read model must exist in M0 even if the richer future UI remains deferred. 

- Server returns only fields and actions currently authorized for the actor/context. 

- UI action visibility is derived from server authorization/product policy, not from static role-name checks. 

- A stale or conflicting writable resource cannot be silently overwritten; show a reload/review conflict state where applicable. 

- Approved wireframe composition may be refined for consistency, but content semantics and commands must follow this contract. 

### **Reference UI interpretation** 

The approved raster is a visual reference. Sample labels, names, counts, scores, timestamps or statuses are illustrative unless explicitly present in the written Guide/Greenfield contract. 

Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

## **15. CNT-04** 

|**FIELD**|**CONTRACT**|
|---|---|
|M0 status|FOUNDATION|
|Route|/projects/:id/content/versions/:cv|
|Purpose|Inspect exact ContentVersion and media readiness.|
|Primary actors|Content-capable roles|
|Queries / read models|ContentVersionDetailQuery + MediaReadiness|
|Commands|Technical replacement assessment where allowed|
|Capabilities / authority|content.view + asset.view/manage|
|Entitlement / product access|None|
|Exact version behavior|Exact route CV|
|Non-happy states|validating/invalid/ready|
|Deep-link / return behavior|no latest fallback|
|Owning packets|M0-03-006/008|
|Acceptance tests|MEDIA-GATE-*|



### **Implementation obligations** 

- The underlying contract/read model must exist in M0 even if the richer future UI remains deferred. 

- Server returns only fields and actions currently authorized for the actor/context. 

- UI action visibility is derived from server authorization/product policy, not from static role-name checks. 

- A stale or conflicting writable resource cannot be silently overwritten; show a reload/review conflict state where applicable. 

- Approved wireframe composition may be refined for consistency, but content semantics and commands must follow this contract. 

### **Reference UI interpretation** 

The approved raster is a visual reference. Sample labels, names, counts, scores, timestamps or statuses are illustrative unless explicitly present in the written Guide/Greenfield contract. 

Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

## **16. CNT-05** 

|**FIELD**|**CONTRACT**|
|---|---|
|M0 status|FOUNDATION|
|Route|/projects/:id/cast|
|Purpose|Minimum cast/Person-to-Character resolution.|
|Primary actors|Editor/Analyst/Validator|
|Queries / read models|CastQuery|
|Commands|Resolve Person candidate to Character|
|Capabilities / authority|catalog.view/link + inventory edit as applicable|
|Entitlement / product access|Analysis detail where candidate-derived|
|Exact version behavior|Project-scoped|
|Non-happy states|empty, unresolved, conflict|
|Deep-link / return behavior|return to candidate/analysis|
|Owning packets|M0-05-004|
|Acceptance tests|TC-CAST-*|



### **Implementation obligations** 

- The underlying contract/read model must exist in M0 even if the richer future UI remains deferred. 

- Server returns only fields and actions currently authorized for the actor/context. 

- UI action visibility is derived from server authorization/product policy, not from static role-name checks. 

- A stale or conflicting writable resource cannot be silently overwritten; show a reload/review conflict state where applicable. 

- Approved wireframe composition may be refined for consistency, but content semantics and commands must follow this contract. 

### **Reference UI interpretation** 

The approved raster is a visual reference. Sample labels, names, counts, scores, timestamps or statuses are illustrative unless explicitly present in the written Guide/Greenfield contract. 

Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

## **17. EDT-01** 

|**FIELD**|**CONTRACT**|
|---|---|
|M0 status|REQUIRED|
|Route|/projects/:id/editor|
|Purpose|Temporal Core authoring with normalized media clock and one track per InventoryItem.|
|Primary actors|Content Editor, Validator, Analyst as allowed|
|Queries / read models|EditorWorkspaceQuery|
|Commands|Create/adjust/archive Appearance; Scene commands; item operations|
|Capabilities / authority|editor.view + operation-specific core caps|
|Entitlement / product access|Analysis detail only for AI proposal detail|
|Exact version behavior|Exact CV required|
|Non-happy states|preparing, ready, conflict, forbidden|
|Deep-link / return behavior|WorkContext + exact CV/time|
|Owning packets|M0-06-*|
|Acceptance tests|E2E-01,06,09|



### **Implementation obligations** 

- This screen is part of the M0 canonical product journey and must be present in staging before M0-GATE-6 can pass. 

- Server returns only fields and actions currently authorized for the actor/context. 

- UI action visibility is derived from server authorization/product policy, not from static role-name checks. 

- A stale or conflicting writable resource cannot be silently overwritten; show a reload/review conflict state where applicable. 

- Approved wireframe composition may be refined for consistency, but content semantics and commands must follow this contract. 

- Open/return workflows preserve exact ContentVersion, subject and temporal context using WorkContext/return_context_id. 

### **Reference UI interpretation** 

The approved raster is a visual reference. Sample labels, names, counts, scores, timestamps or statuses are illustrative unless explicitly present in the written Guide/Greenfield contract. 

Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

## **18. EDT-02** 

|**FIELD**|**CONTRACT**|
|---|---|
|M0 status|REQUIRED|
|Route|/projects/:id/editor?inspector=appearance|
|Purpose|Appearance inspector and governed correction/validation/evidence.|
|Primary actors|Editor/Validator|
|Queries / read models|AppearanceInspectorQuery|
|Commands|Adjust; validate; evidence; taxonomy/link|
|Capabilities / authority|appearance.view/edit + validation/evidence as applicable|
|Entitlement / product access|Analysis detail if proposal provenance shown|
|Exact version behavior|Exact CV + subject revision|
|Non-happy states|stale/conflict/revalidation|
|Deep-link / return behavior|return_context_id|
|Owning packets|M0-06; M0-08|
|Acceptance tests|E2E-09,10|



### **Implementation obligations** 

- This screen is part of the M0 canonical product journey and must be present in staging before M0-GATE-6 can pass. 

- Server returns only fields and actions currently authorized for the actor/context. 

- UI action visibility is derived from server authorization/product policy, not from static role-name checks. 

- A stale or conflicting writable resource cannot be silently overwritten; show a reload/review conflict state where applicable. 

- Approved wireframe composition may be refined for consistency, but content semantics and commands must follow this contract. 

- Open/return workflows preserve exact ContentVersion, subject and temporal context using WorkContext/return_context_id. 

### **Reference UI interpretation** 

The approved raster is a visual reference. Sample labels, names, counts, scores, timestamps or statuses are illustrative unless explicitly present in the written Guide/Greenfield contract. 

Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

## **19. EDT-03** 

|**FIELD**|**CONTRACT**|
|---|---|
|M0 status|REQUIRED|
|Route|/projects/:id/editor?mode=scenes|
|Purpose|SceneStructure authoring and validation.|
|Primary actors|Content Editor, Validator|
|Queries / read models|SceneStructureQuery|
|Commands|Create/Adjust/Split/Merge/ValidateSceneStructure|
|Capabilities / authority|scene.view/create/edit/split/merge|
|Entitlement / product access|None|
|Exact version behavior|Exact CV + expected_revision|
|Non-happy states|incomplete, invalid, conflict|
|Deep-link / return behavior|preserve playhead/context|
|Owning packets|M0-06-002; FRZ-05|
|Acceptance tests|TC-SCENE-*|



### **Implementation obligations** 

- This screen is part of the M0 canonical product journey and must be present in staging before M0-GATE-6 can pass. 

- Server returns only fields and actions currently authorized for the actor/context. 

- UI action visibility is derived from server authorization/product policy, not from static role-name checks. 

- A stale or conflicting writable resource cannot be silently overwritten; show a reload/review conflict state where applicable. 

- Approved wireframe composition may be refined for consistency, but content semantics and commands must follow this contract. 

- Open/return workflows preserve exact ContentVersion, subject and temporal context using WorkContext/return_context_id. 

### **Reference UI interpretation** 

The approved raster is a visual reference. Sample labels, names, counts, scores, timestamps or statuses are illustrative unless explicitly present in the written Guide/Greenfield contract. 

Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

## **20. VIS-003** 

|**FIELD**|**CONTRACT**|
|---|---|
|M0 status|REQUIRED|
|Route|/projects/:id/analysis|
|Purpose|Analysis Overview: Business Opportunities above Key Contexts.|
|Primary actors|Analysis-authorized users|
|Queries / read models|AnalysisOverviewQuery|
|Commands|Open workspace/editor|
|Capabilities / authority|analysis.view|
|Entitlement / product access|Teaser or ANALYSIS_DETAIL|
|Exact version behavior|Snapshot exact CV/run|
|Non-happy states|processing, current, outdated, teaser|
|Deep-link / return behavior|to workspace/context|
|Owning packets|M0-07-007/008/009|
|Acceptance tests|E2E-01,11|



### **Implementation obligations** 

- This screen is part of the M0 canonical product journey and must be present in staging before M0-GATE-6 can pass. 

- Server returns only fields and actions currently authorized for the actor/context. 

- UI action visibility is derived from server authorization/product policy, not from static role-name checks. 

- A stale or conflicting writable resource cannot be silently overwritten; show a reload/review conflict state where applicable. 

- Approved wireframe composition may be refined for consistency, but content semantics and commands must follow this contract. 

- Aggregate teaser and exploitable detail are distinct projections; no alternate query may reconstruct licensed detail without entitlement. 

### **Reference UI interpretation** 

The approved raster is a visual reference. Sample labels, names, counts, scores, timestamps or statuses are illustrative unless explicitly present in the written Guide/Greenfield contract. 

Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

## **21. VIS-020** 

|**FIELD**|**CONTRACT**|
|---|---|
|M0 status|REQUIRED|
|Route|/projects/:id/analysis/workspace|
|Purpose|Appearance-oriented Analysis workspace for review and materialization.|
|Primary actors|Analyst, Editor, Validator|
|Queries / read models|AuthorizedAnalysisWorkspaceQuery|
|Commands|Bulk review, candidate bridges, taxonomy, relevance, relationship|
|Capabilities / authority|analysis.view/review + specific core caps|
|Entitlement / product access|ANALYSIS_DETAIL for exploitable detail|
|Exact version behavior|Exact CV + snapshot/run|
|Non-happy states|loading, empty, outdated, conflict|
|Deep-link / return behavior|Open in Editor with return_context|
|Owning packets|M0-07-009|
|Acceptance tests|E2E-09,11|



### **Implementation obligations** 

- This screen is part of the M0 canonical product journey and must be present in staging before M0-GATE-6 can pass. 

- Server returns only fields and actions currently authorized for the actor/context. 

- UI action visibility is derived from server authorization/product policy, not from static role-name checks. 

- A stale or conflicting writable resource cannot be silently overwritten; show a reload/review conflict state where applicable. 

- Approved wireframe composition may be refined for consistency, but content semantics and commands must follow this contract. 

- Aggregate teaser and exploitable detail are distinct projections; no alternate query may reconstruct licensed detail without entitlement. 

- Open/return workflows preserve exact ContentVersion, subject and temporal context using WorkContext/return_context_id. 

### **Reference UI interpretation** 

The approved raster is a visual reference. Sample labels, names, counts, scores, timestamps or statuses are illustrative unless explicitly present in the written Guide/Greenfield contract. 

Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

## **22. VIS-021** 

|**FIELD**|**CONTRACT**|
|---|---|
|M0 status|REQUIRED|
|Route|/projects/:id/analysis/workspace?inspector=1|
|Purpose|Inspector for candidate/item/appearance provenance and review.|
|Primary actors|Analyst, Editor, Validator|
|Queries / read models|AnalysisInspectorQuery|
|Commands|Accept/correct/reject candidate; validate/evidence|
|Capabilities / authority|analysis.review + specific capabilities|
|Entitlement / product access|ANALYSIS_DETAIL|
|Exact version behavior|Exact subject/CV/revision|
|Non-happy states|stale, conflict, no entitlement|
|Deep-link / return behavior|return_context|
|Owning packets|M0-07; M0-08|
|Acceptance tests|E2E-09,10,11|



### **Implementation obligations** 

- This screen is part of the M0 canonical product journey and must be present in staging before M0-GATE-6 can pass. 

- Server returns only fields and actions currently authorized for the actor/context. 

- UI action visibility is derived from server authorization/product policy, not from static role-name checks. 

- A stale or conflicting writable resource cannot be silently overwritten; show a reload/review conflict state where applicable. 

- Approved wireframe composition may be refined for consistency, but content semantics and commands must follow this contract. 

- Aggregate teaser and exploitable detail are distinct projections; no alternate query may reconstruct licensed detail without entitlement. 

- Open/return workflows preserve exact ContentVersion, subject and temporal context using WorkContext/return_context_id. 

### **Reference UI interpretation** 

The approved raster is a visual reference. Sample labels, names, counts, scores, timestamps or statuses are illustrative unless explicitly present in the written Guide/Greenfield contract. 

Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

## **23. VIS-022** 

|**FIELD**|**CONTRACT**|
|---|---|
|M0 status|REQUIRED|
|Route|/projects/:id/analysis/elements/:item|
|Purpose|Extended element view with derived metrics and appearances.|
|Primary actors|Analysis/Core users|
|Queries / read models|ExtendedElementQuery|
|Commands|Navigate/edit according to capability|
|Capabilities / authority|inventory.view + appearance.view|
|Entitlement / product access|ANALYSIS_DETAIL for exploitative detail|
|Exact version behavior|Exact project/CV|
|Non-happy states|empty appearances, stale|
|Deep-link / return behavior|Open in Editor|
|Owning packets|M0-05/06/07|
|Acceptance tests|TC-EXTENDED-*|



### **Implementation obligations** 

- This screen is part of the M0 canonical product journey and must be present in staging before M0-GATE-6 can pass. 

- Server returns only fields and actions currently authorized for the actor/context. 

- UI action visibility is derived from server authorization/product policy, not from static role-name checks. 

- A stale or conflicting writable resource cannot be silently overwritten; show a reload/review conflict state where applicable. 

- Approved wireframe composition may be refined for consistency, but content semantics and commands must follow this contract. 

- Aggregate teaser and exploitable detail are distinct projections; no alternate query may reconstruct licensed detail without entitlement. 

- Open/return workflows preserve exact ContentVersion, subject and temporal context using WorkContext/return_context_id. 

### **Reference UI interpretation** 

The approved raster is a visual reference. Sample labels, names, counts, scores, timestamps or statuses are illustrative unless explicitly present in the written Guide/Greenfield contract. 

Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

## **24. IAM-01** 

|**FIELD**|**CONTRACT**|
|---|---|
|M0 status|REQUIRED|
|Route|/team-access|
|Purpose|Organization members/invitations/client-org projection.|
|Primary actors|Org Admin/Project Manager read as scoped|
|Queries / read models|TeamAccessOverviewQuery|
|Commands|Invite/revoke/change role according policy|
|Capabilities / authority|organization.members.view/manage; invitations manage|
|Entitlement / product access|None|
|Exact version behavior|Org context|
|Non-happy states|empty, pending, conflict|
|Deep-link / return behavior|acting org scoped|
|Owning packets|M0-01-009|
|Acceptance tests|IAM-GATES|



### **Implementation obligations** 

- This screen is part of the M0 canonical product journey and must be present in staging before M0-GATE-6 can pass. 

- Server returns only fields and actions currently authorized for the actor/context. 

- UI action visibility is derived from server authorization/product policy, not from static role-name checks. 

- A stale or conflicting writable resource cannot be silently overwritten; show a reload/review conflict state where applicable. 

- Approved wireframe composition may be refined for consistency, but content semantics and commands must follow this contract. 

### **Reference UI interpretation** 

The approved raster is a visual reference. Sample labels, names, counts, scores, timestamps or statuses are illustrative unless explicitly present in the written Guide/Greenfield contract. 

Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

## **25. IAM-02** 

|**FIELD**|**CONTRACT**|
|---|---|
|M0 status|REQUIRED|
|Route|/team-access/:membership|
|Purpose|Member overview and effective project access/activity.|
|Primary actors|Org/IAM managers|
|Queries / read models|MemberAccessDetailQuery|
|Commands|Change role/suspend/reactivate/assign projects|
|Capabilities / authority|organization.members.* + project assignments as applicable|
|Entitlement / product access|None|
|Exact version behavior|Membership exact|
|Non-happy states|suspended, stale, no access|
|Deep-link / return behavior|project links reauthorize|
|Owning packets|M0-01-009|
|Acceptance tests|IAM-GATES|



### **Implementation obligations** 

- This screen is part of the M0 canonical product journey and must be present in staging before M0-GATE-6 can pass. 

- Server returns only fields and actions currently authorized for the actor/context. 

- UI action visibility is derived from server authorization/product policy, not from static role-name checks. 

- A stale or conflicting writable resource cannot be silently overwritten; show a reload/review conflict state where applicable. 

- Approved wireframe composition may be refined for consistency, but content semantics and commands must follow this contract. 

### **Reference UI interpretation** 

The approved raster is a visual reference. Sample labels, names, counts, scores, timestamps or statuses are illustrative unless explicitly present in the written Guide/Greenfield contract. 

Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

## **26. IAM-03** 

|**FIELD**|**CONTRACT**|
|---|---|
|M0 status|REQUIRED|
|Route|/team-access/invite|
|Purpose|Invite to Organization with immutable RolePackageVersion selection.|
|Primary actors|Org Admin|
|Queries / read models|RolePackage registry + invitation policy|
|Commands|IssueInvitation|
|Capabilities / authority|organization.invitations.manage|
|Entitlement / product access|None|
|Exact version behavior|Org context|
|Non-happy states|pending duplicate, invalid email|
|Deep-link / return behavior|return IAM-01|
|Owning packets|M0-01-005/009|
|Acceptance tests|AUTH-004..007|



### **Implementation obligations** 

- This screen is part of the M0 canonical product journey and must be present in staging before M0-GATE-6 can pass. 

- Server returns only fields and actions currently authorized for the actor/context. 

- UI action visibility is derived from server authorization/product policy, not from static role-name checks. 

- A stale or conflicting writable resource cannot be silently overwritten; show a reload/review conflict state where applicable. 

- Approved wireframe composition may be refined for consistency, but content semantics and commands must follow this contract. 

### **Reference UI interpretation** 

The approved raster is a visual reference. Sample labels, names, counts, scores, timestamps or statuses are illustrative unless explicitly present in the written Guide/Greenfield contract. 

Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

## **27. SET-01** 

|**FIELD**|**CONTRACT**|
|---|---|
|M0 status|REQUIRED|
|Route|/projects/:id/settings/general|
|Purpose|Basic project identity/lifecycle settings.|
|Primary actors|Project managers/admins|
|Queries / read models|ProjectSettingsGeneralQuery|
|Commands|Update permitted identity; archive/restore|
|Capabilities / authority|project.settings.general.view/manage + project.archive|
|Entitlement / product access|None|
|Exact version behavior|Project exact|
|Non-happy states|archived/conflict|
|Deep-link / return behavior|ownership transfer hidden M0|
|Owning packets|M0-09-004|
|Acceptance tests|TC-SET01-*|



### **Implementation obligations** 

- This screen is part of the M0 canonical product journey and must be present in staging before M0-GATE-6 can pass. 

- Server returns only fields and actions currently authorized for the actor/context. 

- UI action visibility is derived from server authorization/product policy, not from static role-name checks. 

- A stale or conflicting writable resource cannot be silently overwritten; show a reload/review conflict state where applicable. 

- Approved wireframe composition may be refined for consistency, but content semantics and commands must follow this contract. 

### **Reference UI interpretation** 

The approved raster is a visual reference. Sample labels, names, counts, scores, timestamps or statuses are illustrative unless explicitly present in the written Guide/Greenfield contract. 

Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

## **28. SET-02** 

|**FIELD**|**CONTRACT**|
|---|---|
|M0 status|REQUIRED|
|Route|/projects/:id/settings/access|
|Purpose|Project access projection over ProjectAccessGrant.|
|Primary actors|Org Admin/Project Manager|
|Queries / read models|ProjectAccessQuery|
|Commands|Grant/change/revoke assignments/collaboration within authority|
|Capabilities / authority|project.assignments/access view/manage|
|Entitlement / product access|None|
|Exact version behavior|Project exact|
|Non-happy states|conflict, ceiling exceeded|
|Deep-link / return behavior|IAM reciprocal projection|
|Owning packets|M0-01-006/007; M0-09-004|
|Acceptance tests|IAM-GATES|



### **Implementation obligations** 

- This screen is part of the M0 canonical product journey and must be present in staging before M0-GATE-6 can pass. 

- Server returns only fields and actions currently authorized for the actor/context. 

- UI action visibility is derived from server authorization/product policy, not from static role-name checks. 

- A stale or conflicting writable resource cannot be silently overwritten; show a reload/review conflict state where applicable. 

- Approved wireframe composition may be refined for consistency, but content semantics and commands must follow this contract. 

### **Reference UI interpretation** 

The approved raster is a visual reference. Sample labels, names, counts, scores, timestamps or statuses are illustrative unless explicitly present in the written Guide/Greenfield contract. 

Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

## **29. SET-07** 

|**FIELD**|**CONTRACT**|
|---|---|
|M0 status|REQUIRED|
|Route|/projects/:id/settings/audit|
|Purpose|Basic authorized project/IAM audit trail.|
|Primary actors|Admin/PM with audit rights|
|Queries / read models|AuthorizedAuditQuery|
|Commands|None M0|
|Capabilities / authority|project.settings.audit.view|
|Entitlement / product access|None|
|Exact version behavior|Project exact|
|Non-happy states|empty/loading|
|Deep-link / return behavior|links reauthorize|
|Owning packets|M0-09-004|
|Acceptance tests|TC-AUDIT-*|



### **Implementation obligations** 

- This screen is part of the M0 canonical product journey and must be present in staging before M0-GATE-6 can pass. 

- Server returns only fields and actions currently authorized for the actor/context. 

- UI action visibility is derived from server authorization/product policy, not from static role-name checks. 

- A stale or conflicting writable resource cannot be silently overwritten; show a reload/review conflict state where applicable. 

- Approved wireframe composition may be refined for consistency, but content semantics and commands must follow this contract. 

### **Reference UI interpretation** 

The approved raster is a visual reference. Sample labels, names, counts, scores, timestamps or statuses are illustrative unless explicitly present in the written Guide/Greenfield contract. 

Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

## **30. OPS-01** 

|**FIELD**|**CONTRACT**|
|---|---|
|M0 status|FOUNDATION|
|Route|/operations|
|Purpose|Authorized operations list/projection; backend runtime is mandatory.|
|Primary actors|Operational users|
|Queries / read models|AuthorizedOperationsQuery|
|Commands|Retry/cancel if policy allows|
|Capabilities / authority|operations.view/retry/cancel + target visibility|
|Entitlement / product access|Target surface-specific|
|Exact version behavior|Exact target|
|Non-happy states|running, waiting, failed, compensated|
|Deep-link / return behavior|target reauth|
|Owning packets|M0-04|
|Acceptance tests|OPS-GATES|



### **Implementation obligations** 

- The underlying contract/read model must exist in M0 even if the richer future UI remains deferred. 

- Server returns only fields and actions currently authorized for the actor/context. 

- UI action visibility is derived from server authorization/product policy, not from static role-name checks. 

- A stale or conflicting writable resource cannot be silently overwritten; show a reload/review conflict state where applicable. 

- Approved wireframe composition may be refined for consistency, but content semantics and commands must follow this contract. 

### **Reference UI interpretation** 

The approved raster is a visual reference. Sample labels, names, counts, scores, timestamps or statuses are illustrative unless explicitly present in the written Guide/Greenfield contract. 

Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

## **31. OPS-02** 

|**FIELD**|**CONTRACT**|
|---|---|
|M0 status|FOUNDATION|
|Route|/operations/:id|
|Purpose|Operation detail with real steps/attempts/log/result references.|
|Primary actors|Operational users|
|Queries / read models|OperationDetailQuery|
|Commands|Retry/cancel|
|Capabilities / authority|operations.view/retry/cancel + target visibility|
|Entitlement / product access|Target surface-specific|
|Exact version behavior|Exact target|
|Non-happy states|waiting/retrying/failed|
|Deep-link / return behavior|target links reauth|
|Owning packets|M0-04|
|Acceptance tests|OPS-GATES|



### **Implementation obligations** 

- The underlying contract/read model must exist in M0 even if the richer future UI remains deferred. 

- Server returns only fields and actions currently authorized for the actor/context. 

- UI action visibility is derived from server authorization/product policy, not from static role-name checks. 

- A stale or conflicting writable resource cannot be silently overwritten; show a reload/review conflict state where applicable. 

- Approved wireframe composition may be refined for consistency, but content semantics and commands must follow this contract. 

### **Reference UI interpretation** 

The approved raster is a visual reference. Sample labels, names, counts, scores, timestamps or statuses are illustrative unless explicitly present in the written Guide/Greenfield contract. 

Confidential - IwantIt 

IwantIt  |  Content Intelligence Infrastructure 

## **5. Cross-screen consistency checklist** 

|**AREA**|**CONSISTENCY CONTRACT**|
|---|---|
|Global shell|Same product shell, Acting Organization affordance, project context and authorized navigation pattern across<br>screens.|
|Color/status|Status colors never carry meaning alone; same semantic state uses same visual treatment.|
|Project header|Project identity, owner/context and exact version indicators use the same placement vocabulary.|
|Navigation|Module visibility is server-authorized; hidden modules are not rendered as disabled by default.|
|Tables|Selection, bulk actions, pagination, search, filters and empty/loading/error patterns remain consistent.|
|Inspectors|Right-side inspector pattern uses consistent hierarchy, close/back behavior and action footer.|
|Temporal controls|Canonical millisecond time is presented consistently; no screen invents a separate rounding/frame rule.|
|Processing|Operations show real steps/state. No decorative fake percentages.|
|Conflicts|409 concurrent modification uses a consistent reload/review pattern.|
|Entitlement|Teaser/detail boundaries use consistent messaging without exposing hidden data structure.|
|Accessibility|Keyboard order, focus, labels, status announcements and contrast are consistent across modules.|



## **6. Freeze use** 

This matrix is a companion to the Greenfield v1.1. During implementation, a packet may not introduce a required field/action into a screen unless the matrix is updated or the new behavior is demonstrably internal/non-UI. Any contradiction between this matrix and the Guía/Architecture Freeze must be raised as an architecture decision rather than silently resolved. 

Confidential - IwantIt 

