<?php

return [
    'default_disk' => 'videos_disk',

    'ffmpeg' => [
        'binaries' => '/usr/bin/ffmpeg',
        'threads' => 99,
    ],

    'ffprobe' => [
        'binaries' => '/usr/bin/ffprobe',
    ],

    'timeout' => 3600,
];
