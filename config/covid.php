<?php

return [
    'default_disk' => 'videos_disk',

    'ffmpeg' => [
        'binaries' => env('FFMPEG_BINARIES', 'ffmpeg'),
        'threads' => 36,
    ],

    'ffprobe' => [
        'binaries' => env('FFPROBE_BINARIES', 'ffprobe'),
    ],

    'timeout' => 3600,
];

