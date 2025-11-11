<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <title>I Want It API Documentation</title>

    <!-- Claude-style fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Base Scribe theme -->
    <link rel="stylesheet" href="{{ asset('/vendor/scribe/css/theme-default.style.css') }}" media="screen">
    <link rel="stylesheet" href="{{ asset('/vendor/scribe/css/theme-default.print.css') }}" media="print">

    <!-- Claude-style overrides -->
    <link rel="stylesheet" href="{{ asset('docs/custom/claude-style.css') }}">

    <!-- Highlight.js (dark syntax) -->
    <link rel="stylesheet"
          href="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/styles/obsidian.min.css">
    <script src="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/highlight.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jets/0.14.1/jets.min.js"></script>

    <script>
        var tryItOutBaseUrl = "{{ config('app.url') }}";
        var useCsrf = Boolean();
        var csrfUrl = "/sanctum/csrf-cookie";
    </script>

    <script src="{{ asset('/vendor/scribe/js/tryitout-5.3.0.js') }}"></script>
    <script src="{{ asset('/vendor/scribe/js/theme-default-5.3.0.js') }}"></script>

    <link rel="stylesheet" href="{{ asset('docs/custom/claude-style.css') }}">
    <script src="{{ asset('docs/custom/claude-behavior.js') }}" defer></script>
</head>

<body class="claude-docs" data-languages="[&quot;bash&quot;,&quot;javascript&quot;,&quot;php&quot;,&quot;python&quot;]">

<a href="#" id="nav-button">
    <span>☰ MENU</span>
</a>

<div class="tocify-wrapper">
    <div class="logo-area">
        <img src="{{ asset('img/logo_iwantit.png') }}" alt="I Want It" class="logo">
        <h2>I Want It API</h2>
    </div>

    <div class="lang-selector">
        <button type="button" class="lang-button" data-language-name="bash">Bash</button>
        <button type="button" class="lang-button" data-language-name="javascript">JS</button>
        <button type="button" class="lang-button" data-language-name="php">PHP</button>
        <button type="button" class="lang-button" data-language-name="python">Python</button>
    </div>

    <div class="search">
        <input type="text" class="search" id="input-search" placeholder="Search">
    </div>

    <div id="toc"></div>

    <ul class="toc-footer" id="toc-footer">
        <li><a href="{{ route('scribe.postman') }}">Postman Collection</a></li>
        <li><a href="{{ route('scribe.openapi') }}">OpenAPI Spec</a></li>
        <li><a href="https://github.com/knuckleswtf/scribe">Powered by Scribe ✍</a></li>
    </ul>

    <ul class="toc-footer" id="last-updated">
        <li>{{ config('scribe.last_updated') }}</li>
    </ul>
</div>

<div class="page-wrapper">
    <div class="dark-box"></div>
    <div class="content">
        {!! $html !!}
    </div>
    <div class="dark-box"></div>
</div>

<!-- Claude-style behavior -->
<script src="{{ asset('docs/custom/claude-behavior.js') }}"></script>

</body>
</html>
