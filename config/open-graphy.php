<?php

// config for SaaSykit/OpenGraphy
return [
    'chrome_binary' => null,
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
            'background' => '#0a0a0a',
            'stroke_color' => '#3b82f6',
            'stroke_width' => '2',
            'text_color' => '#ffffff',
        ],
        'stripes' => [
            'start_color' => '#0a0a0a',
            'end_color' => '#1f2937',
            'text_color' => '#ffffff',
        ],
        'sunny' => [
            'start_color' => '#0a0a0a',
            'end_color' => '#1f2937',
            'text_color' => '#ffffff',
        ],
        'verticals' => [
            'start_color' => '#0a0a0a',
            'mid_color' => '#111827',
            'end_color' => '#1f2937',
            'text_color' => '#ffffff',
        ],
        'nodes' => [
            'background' => '#0a0a0a',
            'node_color' => '#1f2937',
            'edge_color' => '#374151',
            'text_color' => '#ffffff',
        ],
    ],
];
