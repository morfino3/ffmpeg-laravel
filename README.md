# Covid
A simplified Laravel wrapper of PHP-FFMpeg: for simple video conversion, thumbnail generation, resizing etc., utilizing FFMpeg's powerful open source library/tool that can decode and encode any video format to one another.
## Requirements

* PHP 7.4
* Apache 2.2+

## Configuration
Install the ffmpeg

	$ sudo apt update
	$ sudo apt install ffmpeg

Add this to your composer.json as dependency

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
Convert video -change quality

```php

$covidInstance = Covid::open(public_path() . '/egg.mp4');

$covidInstance->save(public_path() . '/NewEgg.mp4', [
    'bitrate' => 500,
    'audio' => 256
]);

```

Convert video to mp3

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


## Testing

``` bash
$
```


## Credits
Credits to PHP-FFMpeg Team, Protone Media, and the creator/s of
`laravel-ffmpeg` - laravel wrapper of php-ffmpeg and `php-ffmpeg` - object oriented library of FFmpeg: 
- [PHP-FFMpeg](https://github.com/PHP-FFMpeg/PHP-FFMpeg)
- [The PHP-FFMpeg constributors](https://github.com/PHP-FFMpeg/PHP-FFMpeg/graphs/contributors)
- [Pascal Baljet](https://github.com/pascalbaljet)
- [laravel-ffmpeg](https://github.com/pascalbaljetmedia/laravel-ffmpeg)
- Pexels Videos 2541964.mp4 - [Video by KML from Pexels](https://www.pexels.com/video/a-sparkle-of-liquid-in-a-black-background-2541964/)
- mixaund-success.mp3 - [mixaund](https://www.free-stock-music.com/artist.mixaund.html)