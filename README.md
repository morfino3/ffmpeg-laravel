# Covid
A simplified Laravel wrapper of PHP-FFMpeg based on Pascal Baljet's laravel-ffmpeg: for simple video conversion, thumbnail generation, resizing etc., utilizing FFMpeg's powerful open source library/tool that can decode and encode any video format to one another.
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

Go to /config/filesystems.php and setup disk directory and create it as well. This is where the file will temporarily be stored when converting; specify the driver to local for /storage (default is videos_disk) e.g:

	'videos_disk' => [
            'driver' => 'local',
            'root' => storage_path('videos_disk'),
        ]

## Usage
Resize video (pass closure to addFilter() function):

```php
// Set the appropriate format and video/audio codec e.g: X264 for MPEG-4 conversion; MP3 for mp3 conversion
$format = new \FFMpeg\Format\Video\X264('libmp3lame','libx264');

// We can ommit the fromDisk() method as long as there is a value in the
// default_disk in the project's /config/covid.php
Covid::open('videos.mp4')
      ->addFilter(function ($filters) {
        $filters->resize(new \FFMpeg\Coordinate\Dimension(640, 480));
      })
      ->export()
      //toDisk() can be ommited, it will save in the directory of video by default
      ->toDisk('downloadable_videos')
      ->save('filename.mp4');

```
You can add a listener to the format and then broadcast an Event (with Laravel). The format classes implement the FFMpeg\Format\ProgressableInterface interface. First make the event with the Artisan CLI and make sure the event implements the ShouldBroadcast interface.
```bash
php artisan make:event TranscodingProgressUpdated
```
Then dispatch an event in the progress listener:
```php
$format = new \FFMpeg\Format\Video\X264;
$format->on('progress', function($video, $format, $percentage) {
    event(new TranscodingProgressUpdated($percentage));
});
```

Generate thumbnail:
```php
// getThumbnail() , generates thumbnail at 10 secs mark, when no params passed
Covid::fromDisk('videos')
            ->open('videos.mp4')
            ->getThumbnail()
            ->export()
            ->toDisk('thumbnails')
            ->save('test.png');
```

Check codec use by the file:
```php
$checkCodec = Covid::fromDisk('videos')
            ->open('Clock_Face_2Videvo.mov')
            ->getCodec();

echo $checkCodec;
```

Get duration of the file:
```php
$checkDuration = Covid::fromDisk('videos')
            ->open('Clock_Face_2Videvo.mov')
            ->getDuration();

echo $checkDuration;
```

Get the resolution of the video:
```php
$width = Covid::fromDisk('videos')
            ->open('Clock_Face_2Videvo.mov')
            ->getResolution();

echo $width;
```

## Testing

``` bash
$
```

## Other notes
It is better to use a Queued Job in laravel to implement video convertion etc. "Laravel queues provide a unified API across a variety of different queue backends, such as Beanstalk, Amazon SQS, Redis, or even a relational database. Queues allow you to defer the processing of a time consuming task, such as sending an email, until a later time. Deferring these time consuming tasks drastically speeds up web requests to your application.

So, what Laravel Queues basically does is, it stacks up time consuming tasks, create their jobs and dispatches them when they are intended to be. This way the user won’t notice lag in their overall experience when performing such time consuming operation.

The queue configuration file is stored in config/queue.php. In this file you will find connection configurations for each of the queue drivers that are included with Laravel, which includes a database, Beanstalkd, Amazon SQS, Redis, and a synchronous driver that will execute jobs immediately (for local use). A null queue driver is also included which simply discards queued jobs."
References: https://www.amitmerchant.com/why-you-should-use-laravel-queues


## Credits
Credits to Protone Media, and the creator/s of
`laravel-ffmpeg` - laravel wrapper of php-ffmpeg and `php-ffmpeg` - object oriented library of FFmpeg:
- [laravel-ffmpeg](https://github.com/pascalbaljetmedia/laravel-ffmpeg)
- [PHP-FFMpeg](https://github.com/PHP-FFMpeg/PHP-FFMpeg)
- [Pascal Baljet](https://github.com/pascalbaljet)
- [The PHP-FFMpeg constributors](https://github.com/PHP-FFMpeg/PHP-FFMpeg/graphs/contributors)
- Pexels Videos 2541964.mp4 - [Video by KML from Pexels](https://www.pexels.com/video/a-sparkle-of-liquid-in-a-black-background-2541964/)
- mixaund-success.mp3 - [mixaund](https://www.free-stock-music.com/artist.mixaund.html)