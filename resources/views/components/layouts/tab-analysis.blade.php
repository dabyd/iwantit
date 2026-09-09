@props(['data'])

@php
    $currentCount = \App\Helpers\TabCounter::incrementAndGet();
    $overviewUrl = url("/projects/{$data->id}/analysis/overview");
    $opportunitiesUrl = url("/projects/{$data->id}/advertising-opportunities");
@endphp

<div class="tab-{{ $currentCount }}">
    <h2>Analysis</h2>

    {{-- In-tab sub-navigation: Overview / Advertising. Not a global .tab-N entry. --}}
    <div class="nav nav-tabs analysis-subnav" role="tablist" aria-label="Analysis views">
        <button type="button" class="nav-link active" id="analysis-overview-tab"
                role="tab" aria-controls="analysis-overview-pane" aria-selected="true"
                data-analysis-view="overview">
            Overview
        </button>
        <button type="button" class="nav-link" id="analysis-advertising-tab"
                role="tab" aria-controls="analysis-advertising-pane" aria-selected="false"
                data-analysis-view="advertising">
            Advertising
        </button>
    </div>

    <div class="analysis-panes mt-3">
        {{-- Overview subview (default) --}}
        <section class="analysis-pane" id="analysis-overview-pane"
                 role="tabpanel" aria-labelledby="analysis-overview-tab">
            <div id="overview-loading" class="text-muted py-4" role="status" aria-live="polite">Loading Overview…</div>
            <div id="overview-error" class="alert alert-danger" role="alert" hidden></div>

            <div id="overview-content" hidden>
                <section class="mb-4">
                    <h3>Content Intelligence</h3>
                    <div class="row g-3" id="content-intelligence"></div>
                </section>

                <section class="mb-4">
                    <h3>Business Opportunities</h3>
                    <div class="row g-3" id="business-opportunities"></div>
                </section>

                <section class="mb-4">
                    <h3>Key Contexts</h3>
                    <ul id="key-contexts" class="list-group"></ul>
                </section>
            </div>
        </section>

        {{-- Advertising subview --}}
        <section class="analysis-pane" id="analysis-advertising-pane"
                 role="tabpanel" aria-labelledby="analysis-advertising-tab" hidden>
            <div id="advertising-loading" class="text-muted py-4" role="status" aria-live="polite">Loading Advertising…</div>
            <div id="advertising-error" class="alert alert-danger" role="alert" hidden></div>

            <div id="advertising-content" hidden>
                <div class="btn-group mb-3" role="group" aria-label="Advertising opportunity level filter">
                    <button type="button" class="btn btn-outline-primary active" data-level="all">All</button>
                    <button type="button" class="btn btn-outline-primary" data-level="high">High</button>
                    <button type="button" class="btn btn-outline-primary" data-level="medium">Medium</button>
                    <button type="button" class="btn btn-outline-primary" data-level="low">Low</button>
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
                        <tbody id="opportunities-body"></tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>

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
            loading: root.querySelector('#overview-loading'),
            error: root.querySelector('#overview-error'),
            content: root.querySelector('#overview-content')
        },
        advertising: {
            loading: root.querySelector('#advertising-loading'),
            error: root.querySelector('#advertising-error'),
            content: root.querySelector('#advertising-content')
        }
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
            pane.hidden = pane.id !== 'analysis-' + view + '-pane';
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
            btn.classList.toggle('active', btn.dataset.level === level);
        });
        ensureAdvertisingLoaded();
    }

    filterButtons.forEach(function (btn) {
        btn.addEventListener('click', function () { selectLevel(btn.dataset.level); });
    });
    })();
    </script>
</div>
