# Covid
A simplified Laravel wrapper of PHP-FFmpeg based on Pascal Baljet's laravel-ffmpeg: for simple video conversion, thumbnail generation, resizing etc., utilizing FFMpeg's powerful open source library/tool that can decode and encode any video format to one another.
## Requirements

* PHP 7.4
* Apache 2.2+

## Configuration
Install the ffmpeg

	$ sudo apt update
	$ sudo apt install ffmpeg
Publish vendor

	$ php artisan vendor:publish

Go to /config/filesystems.php and setup disk directory and create it as well. This is where the file will temporarily be stored when converting; specify the driver to local for /storage (default is videos_disk) e.g:

	``` php
	'videos_disk' => [
            'driver' => 'local',
            'root' => storage_path('videos_disk'),
        ]
	```
## Usage



## Testing

``` bash
$
```

## Other notes
It is better to use a Queued Job in laravel to implement video convertion etc. "Laravel queues provide a unified API across a variety of different queue backends, such as Beanstalk, Amazon SQS, Redis, or even a relational database. Queues allow you to defer the processing of a time consuming task, such as sending an email, until a later time. Deferring these time consuming tasks drastically speeds up web requests to your application.

So, what Laravel Queues basically does is, it stacks up time consuming tasks, create their jobs and dispatches them when they are intended to be. This way the user won’t notice lag in their overall experience when performing such time consuming operation.

The queue configuration file is stored in config/queue.php. In this file you will find connection configurations for each of the queue drivers that are included with Laravel, which includes a database, Beanstalkd, Amazon SQS, Redis, and a synchronous driver that will execute jobs immediately (for local use). A null queue driver is also included which simply discards queued jobs."
References: [Why should use laravel queues?] (https://www.amitmerchant.com/why-you-should-use-laravel-queues)


## Credits
Credits to Protone Media, and the creator/s of
`laravel-ffmpeg` - laravel wrapper of php-ffmpeg and `php-ffmpeg` - object oriented library of FFmpeg:
- [laravel-ffmpeg](https://github.com/pascalbaljetmedia/laravel-ffmpeg)
- [PHP-FFMpeg](https://github.com/PHP-FFMpeg/PHP-FFMpeg)
- [Pascal Baljet](https://github.com/pascalbaljet)
- [The PHP-FFMpeg constributors](https://github.com/PHP-FFMpeg/PHP-FFMpeg/graphs/contributors)
- Pexels Videos 2541964.mp4 - [Video by KML from Pexels](https://www.pexels.com/video/a-sparkle-of-liquid-in-a-black-background-2541964/)
- mixaund-success.mp3 - [mixaund](https://www.free-stock-music.com/artist.mixaund.html)