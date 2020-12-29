# FFMpegLaravel
A simplified Laravel wrapper of PHP-FFMpeg: for simple video conversion, thumbnail generation, resizing etc., utilizing FFMpeg's powerful open source library/tool that can decode and encode any video format to one another.
## Requirements

* PHP 7.4
* Apache 2.2+

## Configuration
A working installation of FFMpeg is needed

Install the ffmpeg

    sudo apt update
    sudo apt install ffmpeg / brew install ffmpeg


Add this to your `composer.json` as dependency

    "repositories": [{
        "url": "git@github.com:morfino3/FFMpegLaravel.git",
        "type": "git"
    }],
    "require": {
      "laboratory/FFMpegLaravel": "dev-master",
  }

Or you can add a particular version (See tags)

Publish vendor

    php artisan vendor:publish


## Usage
Add this to your config/app.php, to use it globally

```php
'FFMpegLaravel' => Laboratory\FFMpegLaravel\FFmpegLaravel::class
```

Convert video (change) quality, pass an optional parameter:
'channel', 'bitrate' (video quality) and 'audio'.

```php

use FFMpegLaravel;

$FFMpegLaravelInstance = FFMpegLaravel::open(public_path() . '/egg.mp4');

$FFMpegLaravelInstance->save(public_path() . '/NewEgg.mp4', [
    'bitrate' => 500,
    'audio' => 256
]);

```

Convert video to audio

```php

$mp3 = FFMpegLaravel::open(public_path() . '/egg.mp4');

$mp3->save(public_path() . '/egg.mp3');

```

Resize video

```php
// params> width: integer(required) | height : integer(required) | $forceStandards : boolean(nullable)
// you can pass a boolean value in resize() to force the use of the nearest aspect ratio standard.
$resizedVideo = FFMpegLaravel::open(public_path() . '/egg.mp4')
            ->resize(640, 480)
            ->save(public_path() . '/resized_egg.mp4', [
                        'bitrate' => 500,
                        'audio' => 256
                    ]);
return $resizedVideo;
```

Removes audio from video

```php
$mutedVideo = FFMpegLaravel::open(public_path() . '/egg.mp4')
            ->mute()
            ->save(__DIR__ . '/output/muted_egg.mp4');

return $mutedVideo
```

Generate thumbnail:
```php
// getThumbnail() , generates thumbnail at 10 secs mark, when no params passed
FFMpegLaravel::open('videos.mp4')
      ->getThumbnail(public_path() . '/filename.jpg');
```

Get duration of video in seconds:

```php
// returns a integer of duration in seconds
$duration = FFMpegLaravel::open(public_path() . '/egg.mp4')
      ->getDuration();

echo $duration;
```

Generate GIF from a video:

```php
// parameters: new filepath.gif | duration of GIF file : int(nullable) | from seconds: int(nullable)
$gif = FFMpegLaravel::open(public_path() . '/egg.mp4')
            ->generateGif(public_path() . '/sample.gif', 2 );

return $gif;
```

Get the resolution of video:

```php
// returns an array of resolution of the video: 'width' & 'height'
$resolution = FFMpegLaravel::open(public_path() . '/egg.mp4')
      ->getResolution();

echo $resolution['width'] .' x '.$resolution['height'];
```

You might want to check the codec used by the video:

```php
// returns a string of codec used by the video
$codec = FFMpegLaravel::open(public_path() . '/egg.mp4')
      ->getCodec();

echo $codec;
```


## Testing

``` bash
$phpunit tests/FFMpegLaravelTest
```


## Credits
Credits to PHP-FFMpeg Team and Protone Media:
- [PHP-FFMpeg](https://github.com/PHP-FFMpeg/PHP-FFMpeg)
- [The PHP-FFMpeg constributors](https://github.com/PHP-FFMpeg/PHP-FFMpeg/graphs/contributors)
