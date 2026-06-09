<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IWI Video.js Player 1.0.0 - CSS Grid (auto | 1fr | auto) - {{ $project->name }}</title>

    <link href="{{ asset('demo-assets/iwi-player.css') }}" rel="stylesheet" />
    <script src="{{ asset('demo-assets/iwi-player.js') }}" defer></script>

    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #1a1a1a;
            color: #eee;
            font-family: sans-serif;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .layout-grid {
            display: grid;
            grid-template-columns: 20fr 60fr 20fr;
            gap: 20px;
            height: 100%;
            padding: 20px;
            box-sizing: border-box;
        }

        .sidebar {
            background-color: #222;
            border: 1px dashed #444;
            border-radius: 8px;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: #555;
            font-size: 14px;
        }

        .main-content {
            background-color: #000;
            border-radius: 8px;
            overflow: hidden;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            min-width: 360px;
            min-height: 200px;
        }

        @media (max-width: 768px) {
            .layout-grid {
                grid-template-columns: 1fr;
                grid-template-rows: auto 1fr auto;
            }
        }
    </style>
</head>

<body>

    <div class="layout-grid">
        <aside class="sidebar">1</aside>

        <main class="main-content">
            <video id="my-video-grid" class="video-js iwi-player vjs-big-play-centered" controls preload="auto"
                playsinline webkit-playsinline poster="{{ $cover_url ?? 'https://vjs.zencdn.net/v/oceans.png' }}"
                data-iwi-vid="{{ $project->id }}" data-iwi-key="{{ $license_key }}">
                <source src="{{ $video_url }}" type="video/mp4" />
                <track kind="captions"
                    src="data:text/vtt;charset=utf-8,WEBVTT%0A%0A00%3A00%3A01.000%20--%3E%2000%3A00%3A05.000%0AModo%20Grid%20Activo.%0A"
                    srclang="es" label="Español" default>
            </video>
        </main>

        <aside class="sidebar">2</aside>
    </div>

</body>

</html>
