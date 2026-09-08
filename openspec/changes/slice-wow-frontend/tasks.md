# Tasks: slice-wow-frontend

## 1. Establish the Analysis subview shell

- [ ] Preserve the existing `TabCounter`, `.tab-N` wrapper, and `h2` contract.
- [ ] Replace the single mixed layout with Bootstrap-native Overview and Advertising panels.
- [ ] Add accessible, button-based sub-navigation for Overview and Advertising without adding global project tabs.
- [ ] Add separate status containers for each panel.

## 2. Implement activation-aware, independent data loading

- [ ] Detect when the parent Analysis pane becomes active through its existing `.active` class.
- [ ] Do not request either endpoint while that parent pane is inactive.
- [ ] Fetch Overview only when Analysis first becomes active.
- [ ] Fetch Advertising only when its subview is selected.
- [ ] Maintain independent loading, ready, error, cache, and retry states.
- [ ] Build the validated `level` query parameter for All, High, Medium, and Low without using `Promise.all`.
- [ ] Handle non-JSON and non-success responses as local, actionable errors.

## 3. Render API data safely and faithfully

- [ ] Render Overview KPI cards from the four content-intelligence values.
- [ ] Render the Advertising H/M/L and Clearance-total cards using only returned values.
- [ ] Render Key Contexts ordered by the API, with a client-derived percentage only when total scenes is non-zero.
- [ ] Render the Advertising table from the documented item shape, including nullable values and time formatting.
- [ ] Replace dynamic `innerHTML` with DOM creation, `textContent`, and `replaceChildren`.
- [ ] Restrict badge classes to the known value-level map.

## 4. Integrate with the existing backoffice visual system

- [ ] Use Bootstrap grid, cards, badges, alerts, buttons, list groups, progress bars, and responsive tables.
- [ ] Add only scoped CSS necessary for subview presentation and preserve existing global tab behaviour.
- [ ] Verify desktop density and small-screen stacking/scrolling.
- [ ] Verify visible keyboard focus, semantic table headers, and accessible status feedback.

## 5. Verify the slice

- [ ] Run the relevant Laravel feature tests.
- [ ] Import the WOW demo data into project 12 and exercise both panels as an authorised user.
- [ ] Verify lazy network timing, independent error/retry behaviour, all Advertising filter values, empty states, and safe literal rendering of HTML-like strings.
- [ ] Confirm no unrelated files or legacy tabs regress.
