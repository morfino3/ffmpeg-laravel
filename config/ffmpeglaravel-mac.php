<?php

return [
    'default_disk' => 'videos_disk',

    'ffmpeg' => [
        'binaries' => '/usr/local/bin/ffmpeg',
        'threads' => 4,
    ],

    'ffprobe' => [
        'binaries' => '/usr/local/bin/ffprobe',
    ],

    'timeout' => 3600,
];