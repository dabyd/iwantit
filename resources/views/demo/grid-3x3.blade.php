<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IWI Video.js Player 1.0.0 - CSS Grid 3x3 (20% | 60% | 20%) - {{ $project->name }}</title>

    <link href="{{ asset('demo-assets/iwi-player.css') }}" rel="stylesheet" />
    <script src="{{ asset('demo-assets/iwi-player.js') }}" defer></script>

    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #1a1a1a;
            color: white;
            font-family: sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .grid-layout {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            grid-template-rows: 1fr 1fr 1fr;

            width: 100vw;
            height: 100vh;

            gap: 10px;
        }

        .cell {
            background-color: #333;
            border: 2px dashed #555;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: #777;
        }

        .cell-video {
            min-width: 0;
            min-height: 0;
            position: relative;
            background: #000;
            min-width: 360px;
            min-height: 200px;
        }
    </style>
</head>

<body>

    <div class="grid-layout">
        <div class="cell">1</div>
        <div class="cell">2</div>
        <div class="cell">3</div>

        <div class="cell">4</div>

        <div class="cell-video">
            <video id="my-video" class="video-js iwi-player vjs-default-skin" controls preload="auto" playsinline
                webkit-playsinline poster="{{ $cover_url ?? 'https://vjs.zencdn.net/v/oceans.png' }}" data-iwi-vid="{{ $project->id }}"
                data-iwi-key="{{ $license_key }}">
                <source src="{{ $video_url }}" type="video/mp4" />
                <track kind="captions"
                    src="data:text/vtt;charset=utf-8,WEBVTT%0A%0A00%3A00%3A01.000%20--%3E%2000%3A00%3A05.000%0AModo%20Grid%20Activo.%0A"
                    srclang="es" label="Español" default>
            </video>
        </div>

        <div class="cell">5</div>
        <div class="cell">6</div>

        <div class="cell">7</div>
        <div class="cell">8</div>
        <div class="cell">9</div>
    </div>

</body>

</html>
