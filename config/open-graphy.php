<?php

// config for SaaSykit/OpenGraphy
return [
    'chrome_binary' => '/Applications/Comet.app/Contents/MacOS/Comet',
    'generate_with_command' => false,

    'open_graph_image' => [
        'width' => 1200,
        'height' => 630,
        'type' => 'png',
    ],

    'image' => null,

    'fallback_open_graph_image' => 'og-default.png',

    'logo' => [
        'enabled' => true,
        'location' => 'logo-icon.png',
    ],

    'render_timeout' => 10000,

    'screenshot' => [
        'enabled' => false,
        'render_width' => 1100,
        'render_height' => 1000,
    ],

    'storage' => [
        'disk' => 'public',
        'path' => 'open-graphy',
    ],

    'template' => 'verticals',

    'template_settings' => [
        'strings' => [
            'background' => '#101010',
            'stroke_color' => '#00bc7d',
            'stroke_width' => '2',
            'text_color' => '#ffffff',
        ],
        'stripes' => [
            'start_color' => '#101010',
            'end_color' => '#171717',
            'text_color' => '#ffffff',
        ],
        'sunny' => [
            'start_color' => '#101010',
            'end_color' => '#171717',
            'text_color' => '#ffffff',
        ],
        'verticals' => [
            'start_color' => '#101010',
            'mid_color' => '#141414',
            'end_color' => '#171717',
            'text_color' => '#ffffff',
        ],
        'nodes' => [
            'background' => '#101010',
            'node_color' => '#171717',
            'edge_color' => '#00bc7d',
            'text_color' => '#ffffff',
        ],
    ],
];
