# Covid
A simplified Laravel wrapper of PHP-FFMpeg: for simple video conversion, thumbnail generation, resizing etc., utilizing FFMpeg's powerful open source library/tool that can decode and encode any video format to one another.
## Requirements

* PHP 7.4
* Apache 2.2+

## Configuration
Install the ffmpeg

	$ sudo apt update
	$ sudo apt install ffmpeg


Add this to your `composer.json` as dependency

    "repositories": [{
        "url": "git@gitlab.revlv.net:laboratory/covid.git",
        "type": "git"
    }],
    "require": {
      "laboratory/covid": "dev-master",
  }


Publish vendor

	$ php artisan vendor:publish


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

Generate thumbnail:
```php
// getThumbnail() , generates thumbnail at 10 secs mark, when no params passed
Covid::open('videos.mp4')
      ->getThumbnail(public_path() . '/filename.jpg', 12);
```

Get duration of video in seconds:

```php
// returns a string of duration in seconds
$duration = Covid::open(public_path() . '/egg.mp4')
      ->getDuration();

echo $duration;
```

Get the resolution of video:

```php
// returns a string of resolution of the video. e.g(1080 x 720)
$resolution = Covid::open(public_path() . '/egg.mp4')
      ->getResolution();

echo $resolution;
```

You might want to check the codec used by the video:

```php
// returns a string of resolution of the video. e.g(1080 x 720)
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