@props(['data'])

@php
    $currentCount = \App\Helpers\TabCounter::incrementAndGet();
    $overviewUrl = url("/projects/{$data->id}/analysis/overview");
    $opportunitiesUrl = url("/projects/{$data->id}/advertising-opportunities");
@endphp

<div class="tab-{{ $currentCount }}">
    <h2>Analysis</h2>

    <div id="analysis-loading" class="text-muted py-4">Loading analysis…</div>
    <div id="analysis-error" class="alert alert-danger" style="display:none;"></div>

    <div id="analysis-content" style="display:none;">
        <section>
            <h3>Content Intelligence</h3>
            <div class="row" id="content-intelligence"></div>
        </section>

        <section class="mt-4">
            <h3>Business Opportunities</h3>
            <div class="row" id="business-opportunities"></div>
        </section>

        <section class="mt-4">
            <h3>Key Contexts</h3>
            <ul id="key-contexts" class="list-group"></ul>
        </section>

        <section class="mt-4">
            <h3>Advertising Opportunities</h3>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Level</th>
                            <th>Scene</th>
                            <th>Elements</th>
                            <th>Contexts</th>
                            <th>Time</th>
                            <th>Rationale</th>
                        </tr>
                    </thead>
                    <tbody id="opportunities-body"></tbody>
                </table>
            </div>
        </section>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const loading = document.getElementById('analysis-loading');
        const errorEl = document.getElementById('analysis-error');
        const content = document.getElementById('analysis-content');
        const overviewUrl = @json($overviewUrl);
        const opportunitiesUrl = @json($opportunitiesUrl);

        function fmtMs(ms) {
            if (ms == null) return '—';
            return Math.round(ms / 1000) + 's';
        }

        function badge(level) {
            const map = { high: 'success', medium: 'warning', low: 'secondary' };
            return '<span class="badge bg-' + (map[level] || 'secondary') + '">' + level + '</span>';
        }

        Promise.all([
            fetch(overviewUrl).then(r => { if (!r.ok) throw new Error('Overview HTTP ' + r.status); return r.json(); }),
            fetch(opportunitiesUrl).then(r => { if (!r.ok) throw new Error('Opportunities HTTP ' + r.status); return r.json(); })
        ]).then(function ([overview, opps]) {
            loading.style.display = 'none';
            content.style.display = 'block';

            const ci = overview.content_intelligence;
            const bo = overview.business_opportunities;

            document.getElementById('content-intelligence').innerHTML = [
                ['Scenes', ci.scenes],
                ['Elements', ci.elements],
                ['Appearances', ci.appearances],
                ['Relationships', ci.relationships]
            ].map(function (pair) {
                return '<div class="col-3"><div class="card"><div class="card-body text-center"><div class="h3 mb-0">' + pair[1] + '</div><div class="text-muted">' + pair[0] + '</div></div></div></div>';
            }).join('');

            document.getElementById('business-opportunities').innerHTML =
                '<div class="col-6"><div class="card"><div class="card-body">' +
                '<div class="text-muted mb-2">Advertising</div>' +
                '<span class="badge bg-success">High ' + bo.advertising.high + '</span> ' +
                '<span class="badge bg-warning">Medium ' + bo.advertising.medium + '</span> ' +
                '<span class="badge bg-secondary">Low ' + bo.advertising.low + '</span>' +
                '</div></div></div>' +
                '<div class="col-6"><div class="card"><div class="card-body text-center">' +
                '<div class="h3 mb-0">' + bo.clearance_relevant + '</div><div class="text-muted">Clearance relevant</div>' +
                '</div></div></div>';

            document.getElementById('key-contexts').innerHTML = overview.key_contexts.length
                ? overview.key_contexts.map(function (c) {
                    return '<li class="list-group-item d-flex justify-content-between"><span>' + c.name + '</span><span class="badge bg-primary">' + c.scenes + ' scenes</span></li>';
                }).join('')
                : '<li class="list-group-item text-muted">No key contexts yet.</li>';

            document.getElementById('opportunities-body').innerHTML = opps.items.length
                ? opps.items.map(function (o) {
                    const elements = o.elements.map(function (e) { return e.name + ' <span class="text-muted small">(' + fmtMs(e.time_on_screen_ms) + ')</span>'; }).join(', ');
                    const scene = o.scene ? o.scene.name : '—';
                    const contexts = o.contexts.join(', ') || '—';
                    const time = (o.start_ms != null) ? (fmtMs(o.start_ms) + ' – ' + fmtMs(o.end_ms)) : '—';
                    return '<tr><td>' + badge(o.value_level) + '</td><td>' + scene + '</td><td>' + elements + '</td><td>' + contexts + '</td><td>' + time + '</td><td>' + (o.rationale || '') + '</td></tr>';
                }).join('')
                : '<tr><td colspan="6" class="text-muted">No advertising opportunities yet.</td></tr>';
        }).catch(function (err) {
            loading.style.display = 'none';
            errorEl.style.display = 'block';
            errorEl.textContent = 'Error loading analysis: ' + err.message;
        });
    });
    </script>
</div>
