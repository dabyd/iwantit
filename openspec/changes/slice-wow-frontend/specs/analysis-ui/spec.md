# Analysis UI Specification

## MODIFIED Requirements

### Requirement: The Analysis project tab provides scoped sub-navigation

The existing Analysis parent tab in `resources/views/components/layouts/tab-analysis.blade.php` SHALL provide two in-tab views named **Overview** and **Advertising**. The sub-navigation SHALL use Bootstrap 5-native presentation and semantic button controls, while preserving the existing `TabCounter`, `.tab-N` wrapper, and `h2` contract used by the global project tab navigation.

#### Scenario: An authorised user opens the Analysis parent tab

- **GIVEN** a user has `analysis-screen` capability and read access to a project
- **WHEN** the user activates the existing Analysis parent tab
- **THEN** the user sees Bootstrap-styled Overview and Advertising sub-navigation controls inside that parent tab
- **AND** Overview is the initially selected subview
- **AND** no additional global `.tab-N` entry is created

#### Scenario: A user switches between Analysis subviews

- **GIVEN** the Analysis parent tab is active
- **WHEN** the user selects Advertising or Overview
- **THEN** only the selected subview is visible
- **AND** the selected control exposes its selected state to assistive technology
- **AND** the global project tab navigation continues to operate unchanged

### Requirement: Analysis data loads lazily and independently

The Analysis UI SHALL not request Analysis API data while its parent project tab is inactive. It SHALL load `GET /projects/{project}/analysis/overview` after the parent tab first becomes active. It SHALL load `GET /projects/{project}/advertising-opportunities` only after the Advertising subview is selected, appending `?level=high`, `?level=medium`, or `?level=low` only for an explicit level filter. Overview and Advertising SHALL maintain independent loading, success, empty, error, and retry states.

#### Scenario: The project edit page opens on another parent tab

- **GIVEN** the project edit page is loaded with a parent tab other than Analysis active
- **WHEN** the page reaches its ready state
- **THEN** neither the Overview endpoint nor the Advertising opportunities endpoint has been requested

#### Scenario: A user activates Analysis but does not open Advertising

- **GIVEN** the Analysis parent tab was previously inactive
- **WHEN** the user activates Analysis while Overview is selected
- **THEN** the Overview endpoint is requested once
- **AND** the Advertising opportunities endpoint is not requested

#### Scenario: A user selects an Advertising level filter

- **GIVEN** the Analysis parent tab is active and the user selects Advertising
- **WHEN** the user selects the High filter
- **THEN** the UI requests the Advertising opportunities endpoint with `level=high`
- **AND** renders only the returned High-level opportunities
- **AND** does not re-request the Overview endpoint solely because the filter changed

#### Scenario: One endpoint fails while the other succeeds

- **GIVEN** the Analysis parent tab is active
- **WHEN** either the Overview request or the Advertising request fails
- **THEN** only the affected subview displays its error state and retry control
- **AND** a successfully loaded other subview remains available

### Requirement: Analysis API values render as safe text

The Analysis UI SHALL treat all dynamic values returned by the Overview and Advertising endpoints as untrusted presentation data. It SHALL construct dynamic content with DOM APIs and assign API-provided strings through `textContent` or an equivalent escaping DOM API. It SHALL not interpolate API-provided values through `innerHTML`. CSS classes for value-level badges SHALL be selected only from a fixed `high`, `medium`, and `low` mapping.

#### Scenario: An opportunity rationale contains markup-like text

- **GIVEN** an Advertising opportunity rationale returned by the API contains `<img src=x onerror=alert(1)>`
- **WHEN** the Advertising table renders that opportunity
- **THEN** the rationale is displayed as literal text
- **AND** no image element, event handler, or executable markup is created from the rationale

#### Scenario: A context name contains markup-like text

- **GIVEN** a Key Context name returned by the Overview API contains markup-like text
- **WHEN** the Key Contexts list renders
- **THEN** the value is rendered as literal text in the list
- **AND** the surrounding Bootstrap list structure remains intact

#### Scenario: An API response supplies an unexpected value level

- **GIVEN** an Advertising item has a `value_level` outside `high`, `medium`, or `low`
- **WHEN** the item is rendered
- **THEN** the UI uses only its neutral, predefined badge presentation
- **AND** no API-provided value is used to construct a CSS class or HTML markup
