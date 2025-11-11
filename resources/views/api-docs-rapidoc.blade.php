<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>I Want It API - Interactive Docs</title>
    <script type="module" src="https://unpkg.com/rapidoc/dist/rapidoc-min.js"></script>
</head>
<body>
    <rapi-doc
        spec-url="{{ asset('docs/openapi.yaml') }}"
        theme="dark"
        bg-color="#1e1e1e"
        text-color="#f0f0f0"
        primary-color="#00d4aa"
        render-style="read"
        layout="row"
        show-header="false"
        allow-authentication="false"
        allow-server-selection="false"
        allow-try="true"
    ></rapi-doc>
</body>
</html>