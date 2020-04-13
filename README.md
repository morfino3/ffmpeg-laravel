# Covid
A simplified Laravel wrapper of PHP-FFMpeg: for simple video conversion, thumbnail generation, resizing etc., utilizing FFMpeg's powerful open source library/tool that can decode and encode any video format to one another.
## Requirements

* PHP 7.4
* Apache 2.2+

## Configuration
A working installation of FFMpeg is needed

Install the ffmpeg

    sudo apt update
    sudo apt install ffmpeg


Add this to your `composer.json` as dependency

    "repositories": [{
        "url": "git@github.com:morfino3/covid.git",
        "type": "git"
    }],
    "require": {
      "laboratory/covid": "dev-master",
  }


Publish vendor

    php artisan vendor:publish


## Usage
Add this to your config/app.php, to use it globally

```php
'Covid' => Laboratory\Covid\Covid::class
```

Convert video (change) quality, pass an optional parameter:
'channel', 'bitrate' (video quality) and 'audio'.

```php

use Covid;

$covidInstance = Covid::open(public_path() . '/egg.mp4');

$covidInstance->save(public_path() . '/NewEgg.mp4', [
    'bitrate' => 500,
    'audio' => 256
]);

```

Convert video to audio

```php

$mp3 = Covid::open(public_path() . '/egg.mp4');

$mp3->save(public_path() . '/egg.mp3');

```

Resize video

```php
// params> width: integer(required) | height : integer(required) | $forceStandards : boolean(nullable)
// you can pass a boolean value in resize() to force the use of the nearest aspect ratio standard.
$resizedVideo = Covid::open(public_path() . '/egg.mp4')
            ->resize(640, 480)
            ->save(public_path() . '/resized_egg.mp4', [
                        'bitrate' => 500,
                        'audio' => 256
                    ]);
return $resizedVideo;
```

Removes audio from video

```php
$mutedVideo = Covid::open(public_path() . '/egg.mp4')
            ->mute()
            ->save(__DIR__ . '/output/muted_egg.mp4');

return $mutedVideo
```

Generate thumbnail:
```php
// getThumbnail() , generates thumbnail at 10 secs mark, when no params passed
Covid::open('videos.mp4')
      ->getThumbnail(public_path() . '/filename.jpg');
```

Get duration of video in seconds:

```php
// returns a integer of duration in seconds
$duration = Covid::open(public_path() . '/egg.mp4')
      ->getDuration();

echo $duration;
```

Generate GIF from a video:

```php
// parameters: new filepath.gif | duration of GIF file : int(nullable) | from seconds: int(nullable)
$gif = Covid::open(public_path() . '/egg.mp4')
            ->generateGif(public_path() . '/sample.gif', 2 );

return $gif;
```

Get the resolution of video:

```php
// returns an array of resolution of the video: 'width' & 'height'
$resolution = Covid::open(public_path() . '/egg.mp4')
      ->getResolution();

echo $resolution['width'] .' x '.$resolution['height'];
```

You might want to check the codec used by the video:

```php
// returns a string of codec used by the video
$codec = Covid::open(public_path() . '/egg.mp4')
      ->getCodec();

echo $codec;
```


## Testing

``` bash
$phpunit tests/CovidTest
```


## Credits
Credits to PHP-FFMpeg Team, Protone Media, and the creator of
`laravel-ffmpeg` - laravel wrapper of php-ffmpeg:
- [PHP-FFMpeg](https://github.com/PHP-FFMpeg/PHP-FFMpeg)
- [The PHP-FFMpeg constributors](https://github.com/PHP-FFMpeg/PHP-FFMpeg/graphs/contributors)
- [Pascal Baljet](https://github.com/pascalbaljet)
- [laravel-ffmpeg](https://github.com/pascalbaljetmedia/laravel-ffmpeg)
