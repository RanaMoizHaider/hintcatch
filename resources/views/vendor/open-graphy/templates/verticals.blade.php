<html>
<head>
    <title>Open Graph Image</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">

    @include('open-graphy::partials.font')
    @include('open-graphy::partials.styles')
    @include('open-graphy::partials.js')

    @php
        $bgColor = '#101010';
        $accentColor = '#00bc7d';
        $textColor = '#ffffff';
    @endphp

    <style>
        html, body {
            width: 1200px;
            height: 630px;
            margin: 0;
            padding: 0;
            overflow: hidden;
        }

        body {
            background-color: {{ $bgColor }};
            background-image: none;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 60px;
            box-sizing: border-box;
        }

        .brand-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            gap: 40px;
        }

        .brand-header {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .brand-logo {
            width: 80px;
            height: 80px;
        }

        .brand-name {
            font-size: 64px;
            font-weight: 700;
            color: {{ $textColor }};
            margin: 0;
        }

        .headline {
            color: {{ $textColor }};
            font-size: 72px;
            font-weight: 700;
            text-shadow: none;
            margin: 0;
            line-height: 1.2;
        }

        .accent-line {
            width: 200px;
            height: 6px;
            background-color: {{ $accentColor }};
            border-radius: 3px;
        }

        .tagline {
            font-size: 32px;
            color: {{ $accentColor }};
            font-weight: 500;
            margin: 0;
        }
    </style>

</head>
<body>
    <div class="brand-container">
        <div class="brand-header">
            @if (isset($logo))
                <img class="brand-logo" src="{{ $logo }}" alt="Logo">
            @endif
            <h2 class="brand-name">Hint Catch</h2>
        </div>

        <div class="accent-line"></div>

        <h1 class="headline">{!! $title !!}</h1>

        <p class="tagline">The directory for AI agent configurations</p>
    </div>
</body>

</html>
