<?php

return [

    'show_warnings' => false,
    'public_path' => null,
    'convert_entities' => true,

    'options' => [
        'font_dir' => storage_path('fonts'),
        'font_cache' => storage_path('fonts'),
        'temp_dir' => sys_get_temp_dir(),
        'chroot' => realpath(base_path()),
        'allowed_protocols' => [
            'data://' => ['rules' => []],
            'file://' => ['rules' => []],
            'http://' => ['rules' => []],
            'https://' => ['rules' => []],
        ],
        'artifactPathValidation' => null,
        'log_output_file' => null,
        'enable_font_subsetting' => true,
        'pdf_backend' => 'CPDF',
        'default_media_type' => 'screen',
        'default_paper_size' => 'a4',
        'default_paper_orientation' => 'portrait',
        'default_font' => 'Amiri-Regular',
        'dpi' => 96,
        'enable_php' => true,
        'enable_javascript' => true,
        'enable_remote' => true,
        'allowed_remote_hosts' => null,
        'font_height_ratio' => 1.1,
        'enable_html5_parser' => true,
    ],

    'fonts' => [
        'Amiri-Regular' => [
            'normal' => storage_path('fonts/Amiri-Regular.ttf'),
            'bold' => storage_path('fonts/Amiri-Bold.ttf'),
            'italic' => storage_path('fonts/Amiri-Italic.ttf'),
            'bold_italic' => storage_path('fonts/Amiri-BoldItalic.ttf'),
        ],
    ],

];