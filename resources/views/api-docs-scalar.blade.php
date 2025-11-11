<!DOCTYPE html>
<html>
<head>
    <title>I Want It API - Documentation</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #2c3e50;
        }
        
        /* Personalización de colores IwantIt */
        :root {
            --scalar-color-1: #2c3e50;
            --scalar-color-2: #34495e;
            --scalar-color-3: #5da3e8;
            --scalar-color-accent: #5da3e8;
            --scalar-background-1: #2c3e50;
            --scalar-background-2: #34495e;
            --scalar-background-3: #1a252f;
        }
        
        /* Estilos adicionales */
        .scalar-api-client {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        
        /* Sidebar */
        .scalar-api-client__sidebar {
            background: #2c3e50 !important;
            border-right: 1px solid #34495e !important;
        }
        
        /* Header */
        .scalar-api-client__header {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%) !important;
            border-bottom: 2px solid #5da3e8 !important;
        }
        
        /* Links y botones principales */
        .scalar-button,
        a.scalar-button {
            background: #5da3e8 !important;
            color: white !important;
            border: none !important;
            transition: all 0.3s ease;
        }
        
        .scalar-button:hover,
        a.scalar-button:hover {
            background: #4a90e2 !important;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(93, 163, 232, 0.3);
        }
        
        /* Badges de métodos HTTP */
        .scalar-badge--get {
            background: #5da3e8 !important;
        }
        
        .scalar-badge--post {
            background: #f39c12 !important;
        }
        
        .scalar-badge--put,
        .scalar-badge--patch {
            background: #5da3e8 !important;
        }
        
        .scalar-badge--delete {
            background: #e74c3c !important;
        }
        
        /* Cards */
        .scalar-card {
            background: #34495e !important;
            border: 1px solid #5da3e8 !important;
            border-radius: 8px;
        }
        
        /* Code blocks */
        .scalar-code-block {
            background: #1a252f !important;
            border: 1px solid #34495e !important;
        }
        
        /* Tablas */
        table {
            border-color: #34495e !important;
        }
        
        th {
            background: #34495e !important;
            color: #ecf0f1 !important;
        }
        
        /* Scrollbar personalizado */
        ::-webkit-scrollbar {
            width: 10px;
        }
        
        ::-webkit-scrollbar-track {
            background: #2c3e50;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #5da3e8;
            border-radius: 5px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #4a90e2;
        }
    </style>
</head>
<body>
    <script
        id="api-reference"
        data-url="{{ asset('docs/openapi.yaml') }}"
        data-configuration='{
            "theme": "default",
            "layout": "modern",
            "defaultOpenAllTags": true,
            "showSidebar": true,
            "searchHotKey": "k",
            "darkMode": true,
            "hideDownloadButton": false,
            "withDefaultFonts": true,
            "customCss": "body { font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, sans-serif; }",
            "metaData": {
                "title": "IwantIt API Documentation",
                "description": "API REST para consultar productos detectados en videos"
            }
        }'
    ></script>
    <script src="https://cdn.jsdelivr.net/npm/@scalar/api-reference"></script>
</body>
</html>