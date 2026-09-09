@props(['data'])

@php
    $currentCount = \App\Helpers\TabCounter::incrementAndGet();
    $overviewUrl = url("/projects/{$data->id}/analysis/overview");
    $opportunitiesUrl = url("/projects/{$data->id}/advertising-opportunities");
@endphp

<div class="tab-{{ $currentCount }}">
    <h2>Analysis</h2>

    {{-- In-tab sub-navigation: Overview / Advertising --}}
    <div class="nav nav-tabs analysis-subnav" role="tablist" aria-label="Analysis views">
        <button type="button" class="nav-link active" id="analysis-overview-tab-{{ $currentCount }}"
                role="tab" aria-controls="analysis-overview-pane-{{ $currentCount }}" aria-selected="true"
                data-analysis-view="overview">
            Overview
        </button>
        <button type="button" class="nav-link" id="analysis-advertising-tab-{{ $currentCount }}"
                role="tab" aria-controls="analysis-advertising-pane-{{ $currentCount }}" aria-selected="false"
                data-analysis-view="advertising">
            Advertising
        </button>
    </div>

    <div class="analysis-panes mt-3">
        {{-- Overview subview (default) --}}
        <section class="analysis-pane" id="analysis-overview-pane-{{ $currentCount }}"
                 role="tabpanel" aria-labelledby="analysis-overview-tab-{{ $currentCount }}">
            <div class="overview-loading text-muted py-4" role="status" aria-live="polite">Loading Overview…</div>
            <div class="overview-error alert alert-danger" role="alert" hidden></div>

            <div class="overview-content" hidden>
                <section class="mb-4">
                    <h3>Content Intelligence</h3>
                    <div class="row g-3 content-intelligence"></div>
                </section>

                <section class="mb-4">
                    <h3>Business Opportunities</h3>
                    <div class="row g-3 business-opportunities"></div>
                </section>

                <section class="mb-4">
                    <h3>Key Contexts</h3>
                    <ul class="key-contexts list-group"></ul>
                </section>
            </div>
        </section>

        {{-- Advertising subview --}}
        <section class="analysis-pane" id="analysis-advertising-pane-{{ $currentCount }}"
                 role="tabpanel" aria-labelledby="analysis-advertising-tab-{{ $currentCount }}" hidden>
            <div class="advertising-loading text-muted py-4" role="status" aria-live="polite">Loading Advertising…</div>
            <div class="advertising-error alert alert-danger" role="alert" hidden></div>

            <div class="advertising-content" hidden>
                <div class="btn-group mb-3" role="group" aria-label="Advertising opportunity level filter">
                    <button type="button" class="btn btn-primary active" data-level="all" aria-pressed="true">All</button>
                    <button type="button" class="btn btn-outline-primary" data-level="high" aria-pressed="false">High</button>
                    <button type="button" class="btn btn-outline-primary" data-level="medium" aria-pressed="false">Medium</button>
                    <button type="button" class="btn btn-outline-primary" data-level="low" aria-pressed="false">Low</button>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th scope="col">Level</th>
                                <th scope="col">Scene</th>
                                <th scope="col">Elements</th>
                                <th scope="col">Contexts</th>
                                <th scope="col">Time</th>
                                <th scope="col">Rationale</th>
                            </tr>
                        </thead>
                        <tbody class="opportunities-body"></tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

    <style>
        /* Scoped to this Analysis component instance; restores visible keyboard
           focus that the global `button { outline: none }` otherwise removes. */
        .tab-{{ $currentCount }} .analysis-subnav .nav-link:focus-visible,
        .tab-{{ $currentCount }} .advertising-content [data-level]:focus-visible {
            outline: 2px solid #0d6efd;
            outline-offset: 2px;
        }

        /* KPI cards: distribute into 4 columns when space allows and drop to 2 or 1
           as the container narrows, independent of Bootstrap breakpoints. */
        .tab-{{ $currentCount }} .content-intelligence,
        .tab-{{ $currentCount }} .business-opportunities {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        /* Advertising table: keep columns legible on small screens by enforcing a
           minimum table width (horizontal scroll via .table-responsive) instead of
           compressing them before the scroll behaviour activates. */
        .tab-{{ $currentCount }} .table-responsive table {
            min-width: 850px;
        }
        .tab-{{ $currentCount }} .table th,
        .tab-{{ $currentCount }} .table td {
            white-space: normal;
            word-break: break-word;
        }
    </style>

    <script>
    (function () {
        const root = document.querySelector('.tab-{{ $currentCount }}');
        if (!root) return;

        const overviewUrl = @json($overviewUrl);
        const opportunitiesUrl = @json($opportunitiesUrl);
        const LEVELS = ['all', 'high', 'medium', 'low'];
        const overview = { status: 'idle', data: null };
        const advertising = { status: 'idle', data: null, level: 'all', cache: {} };
        let advertisingRequestId = 0;

        const viewButtons = root.querySelectorAll('[data-analysis-view]');
        const panes = root.querySelectorAll('.analysis-pane');
        const filterButtons = root.querySelectorAll('[data-level]');
        const surfaces = {
            overview: {
                loading: root.querySelector('.overview-loading'),
                error: root.querySelector('.overview-error'),
                content: root.querySelector('.overview-content')
            },
            advertising: {
                loading: root.querySelector('.advertising-loading'),
                error: root.querySelector('.advertising-error'),
                content: root.querySelector('.advertising-content')
            }
        };

        const LEVEL_CONFIG = {
            high: { class: 'bg-success', label: 'High' },
            medium: { class: 'bg-warning text-dark', label: 'Medium' },
            low: { class: 'bg-secondary', label: 'Low' }
        };

        async function fetchJson(url) {
            let resp;
            try {
                resp = await fetch(url, { headers: { Accept: 'application/json' } });
            } catch (e) {
                throw new Error('Network error.');
            }
            if (!resp.ok) throw new Error('Request failed (HTTP ' + resp.status + ').');
            const type = (resp.headers.get('content-type') || '').toLowerCase();
            if (type.indexOf('application/json') === -1) throw new Error('Non-JSON response.');
            try {
                return await resp.json();
            } catch (e) {
                throw new Error('Invalid JSON response.');
            }
        }

        function advertisingUrlFor(level) {
            const url = new URL(opportunitiesUrl, window.location.origin);
            if (level !== 'all' && LEVELS.indexOf(level) !== -1) url.searchParams.set('level', level);
            return url.toString();
        }

        function renderError(container, message, retry) {
            container.replaceChildren();
            const text = document.createElement('span');
            text.textContent = message;
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'btn btn-outline-danger btn-sm ms-3';
            button.textContent = 'Retry';
            button.addEventListener('click', retry);
            container.append(text, button);
        }

        function setSurface(name, status, message) {
            const surface = surfaces[name];
            surface.loading.hidden = status !== 'loading';
            surface.error.hidden = status !== 'error';
            surface.content.hidden = status !== 'ready';
            if (status === 'error') {
                renderError(surface.error, message || 'Something went wrong.',
                    name === 'overview' ? loadOverview : loadAdvertising);
            }
        }

        async function loadOverview() {
            if (overview.status === 'loading') return;
            overview.status = 'loading';
            setSurface('overview', 'loading');
            try {
                overview.data = await fetchJson(overviewUrl);
                overview.status = 'ready';
                renderOverview(overview.data);
                setSurface('overview', 'ready');
            } catch (err) {
                overview.status = 'error';
                setSurface('overview', 'error', err.message);
            }
        }

        async function loadAdvertising() {
            const level = advertising.level;
            const requestId = ++advertisingRequestId;
            advertising.status = 'loading';
            setSurface('advertising', 'loading');
            try {
                const data = await fetchJson(advertisingUrlFor(level));
                if (requestId !== advertisingRequestId) return;
                advertising.data = data;
                advertising.cache[level] = data;
                advertising.status = 'ready';
                renderAdvertising(data);
                setSurface('advertising', 'ready');
            } catch (err) {
                if (requestId !== advertisingRequestId) return;
                advertising.status = 'error';
                setSurface('advertising', 'error', err.message);
            }
        }

        function onAnalysisActivated() {
            if (overview.status === 'idle') loadOverview();
        }

        const observer = new MutationObserver(function () {
            if (root.classList.contains('active')) onAnalysisActivated();
        });
        observer.observe(root, { attributes: true, attributeFilter: ['class'] });
        if (root.classList.contains('active')) onAnalysisActivated();

        function ensureAdvertisingLoaded() {
            const level = advertising.level;
            if (advertising.cache[level]) {
                advertising.data = advertising.cache[level];
                advertising.status = 'ready';
                renderAdvertising(advertising.data);
                setSurface('advertising', 'ready');
                return;
            }
            if (advertising.status !== 'loading') loadAdvertising();
        }

        function activate(view) {
            viewButtons.forEach(function (btn) {
                const selected = btn.dataset.analysisView === view;
                btn.classList.toggle('active', selected);
                btn.setAttribute('aria-selected', selected ? 'true' : 'false');
            });
            panes.forEach(function (pane) {
                pane.hidden = !pane.id.startsWith('analysis-' + view + '-pane-');
            });
            if (view === 'advertising') ensureAdvertisingLoaded();
        }

        viewButtons.forEach(function (btn) {
            btn.addEventListener('click', function () { activate(btn.dataset.analysisView); });
        });

        function selectLevel(level) {
            if (LEVELS.indexOf(level) === -1) return;
            advertising.level = level;
            filterButtons.forEach(function (btn) {
                const selected = btn.dataset.level === level;
                btn.classList.toggle('active', selected);
                btn.classList.toggle('btn-primary', selected);
                btn.classList.toggle('btn-outline-primary', !selected);
                btn.setAttribute('aria-pressed', selected ? 'true' : 'false');
            });
            ensureAdvertisingLoaded();
        }

        filterButtons.forEach(function (btn) {
            btn.addEventListener('click', function () { selectLevel(btn.dataset.level); });
        });

        // --- Render Helpers & Functions ---

        function el(tag, className, text) {
            const element = document.createElement(tag);
            if (className) element.className = className;
            if (text !== undefined && text !== null) element.textContent = text;
            return element;
        }

        function formatTimestamp(ms) {
            if (ms == null || typeof ms !== 'number' || isNaN(ms)) return '—';
            const totalSeconds = Math.floor(ms / 1000);
            const hours = Math.floor(totalSeconds / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;
            const pad = (num) => String(num).padStart(2, '0');

            if (hours > 0) {
                return `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;
            }
            return `${pad(minutes)}:${pad(seconds)}`;
        }

        function makeLevelBadge(level) {
            const lvl = String(level || '').toLowerCase();
            const config = LEVEL_CONFIG[lvl] || { class: 'bg-light text-dark', label: level || 'N/A' };
            const span = el('span', `badge ${config.class}`);
            span.textContent = config.label;
            return span;
        }

        function kpiCard(value, label, badgeClass) {
            const col = el('div');
            const card = el('div', 'card h-100 text-center');
            const body = el('div', 'card-body');
            body.appendChild(el('div', 'h3 mb-0', String(value == null ? 0 : value)));
            const labelEl = el('div');
            if (badgeClass) {
                labelEl.appendChild(el('span', 'badge ' + badgeClass, label));
            } else {
                labelEl.className = 'text-muted';
                labelEl.textContent = label;
            }
            body.appendChild(labelEl);
            card.appendChild(body);
            col.appendChild(card);
            return col;
        }

        function renderOverview(data) {
            const ci = (data && data.content_intelligence) || {};
            const bo = (data && data.business_opportunities) || {};
            const adv = bo.advertising || {};
            const clearance = bo.clearance_relevant;
            const contexts = (data && data.key_contexts) || [];
            const totalScenes = Number(ci.scenes) || 0;

            const ciRow = root.querySelector('.content-intelligence');
            const boRow = root.querySelector('.business-opportunities');
            const contextsList = root.querySelector('.key-contexts');

            ciRow.replaceChildren();
            [['Scenes', ci.scenes], ['Elements', ci.elements], ['Appearances', ci.appearances], ['Relationships', ci.relationships]].forEach(function (pair) {
                ciRow.appendChild(kpiCard(pair[1], pair[0]));
            });

            boRow.replaceChildren();
            boRow.appendChild(kpiCard(adv.high, 'High', LEVEL_CONFIG.high.class));
            boRow.appendChild(kpiCard(adv.medium, 'Medium', LEVEL_CONFIG.medium.class));
            boRow.appendChild(kpiCard(adv.low, 'Low', LEVEL_CONFIG.low.class));
            boRow.appendChild(kpiCard(clearance, 'Clearance'));

            contextsList.replaceChildren();
            if (!contexts.length) {
                contextsList.appendChild(el('li', 'list-group-item text-muted', 'No key contexts yet.'));
            } else {
                contexts.forEach(function (ctx) {
                    const scenes = Number(ctx.scenes) || 0;
                    const li = el('li', 'list-group-item d-flex justify-content-between align-items-center');
                    li.appendChild(el('span', null, ctx.name || '—'));
                    const right = el('span', 'd-flex align-items-center gap-2');
                    right.appendChild(el('span', 'badge bg-primary', scenes + (scenes === 1 ? ' scene' : ' scenes')));
                    if (totalScenes > 0) {
                        right.appendChild(el('span', 'text-muted small', Math.round((scenes / totalScenes) * 100) + '%'));
                    }
                    li.appendChild(right);
                    contextsList.appendChild(li);
                });
            }
        }

        function renderAdvertising(data) {
            const body = root.querySelector('.opportunities-body');
            body.replaceChildren();

            const items = data && Array.isArray(data.items) ? data.items : [];

            if (items.length === 0) {
                const tr = el('tr');
                const td = el('td', 'text-muted text-center py-3', 'No opportunities found.');
                td.colSpan = 6;
                tr.append(td);
                body.append(tr);
                return;
            }

            items.forEach(item => {
                const tr = el('tr');

                // Level (fallback value_level -> level)
                const tdLevel = el('td');
                const levelValue = item.value_level || item.level;
                tdLevel.append(makeLevelBadge(levelValue));

                // Scene (soporta objeto, string directo o scene_name)
                const sceneName = (typeof item.scene === 'object' ? item.scene?.name : item.scene) || item.scene_name || '—';
                const tdScene = el('td', '', sceneName);

                // Elements (soporta array de objetos o array de strings)
                const tdElements = el('td');
                const elementNames = Array.isArray(item.elements)
                    ? item.elements.map(e => (typeof e === 'object' ? e?.name : e)).filter(Boolean).join(', ')
                    : '';
                tdElements.textContent = elementNames || '—';

                // Contexts
                const tdContexts = el('td');
                tdContexts.textContent = Array.isArray(item.contexts) ? item.contexts.join(', ') : '—';

                // Time
                const start = formatTimestamp(item.start_ms);
                const end = item.end_ms != null ? ` - ${formatTimestamp(item.end_ms)}` : '';
                const tdTime = el('td', 'text-nowrap', `${start}${end}`);

                // Rationale
                const tdRationale = el('td', '', item.rationale || '—');

                tr.append(tdLevel, tdScene, tdElements, tdContexts, tdTime, tdRationale);
                body.append(tr);
            });
        }
    })();
    </script>
</div>
