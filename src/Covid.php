<?php

namespace Laboratory\Covid;

use Exception;
use FFMpeg\Media\Frame;
use Illuminate\Support\Arr;
use Psr\Log\LoggerInterface;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg as BaseFFMpeg;
use FFMpeg\Format\Video\X264 as X264;
use FFMpeg\Format\Audio\Mp3 as MP3Codec;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

class Covid
{
    protected $ffmpeg;
    protected $encoder;
    protected $filepath;
    protected $formats = [
        'mp4' => FFMpeg\Format\Video\X264:class;
    ];
    protected $dimensions = [

    ];

    public function __construct(ConfigRepository $config, LoggerInterface $logger, Media $media = null, $encoder)
    {
        $ffmpegConfig = $config->get('covid');

        $this->ffmpeg = BaseFFMpeg::create([
            'ffmpeg.binaries'   => Arr::get($ffmpegConfig,'ffmpeg.binaries'),
            'ffmpeg.threads'    => Arr::get($ffmpegConfig, 'ffmpeg.threads'),
            'ffprobe.binaries'  => Arr::get($ffmpegConfig, 'ffprobe.binaries'),
            'timeout'           => Arr::get($ffmpegConfig, 'timeout'),
        ], $logger);

        $this->media = $media;
        $this->encoder = $encoder;
    }

    public function open($filepath): Media
    {
        if (!file_exists($filepath)) {
            throw new Exception(sprintf('%s doesn\'t exist.', $filepath));
        }

        $this->filepath = $filepath;

        $ffmpegMedia = $this->ffmpeg->open($filepath);

        // return new Media($file, $ffmpegMedia);
        return $ffmpegMedia;
    }

    /**
     * Generates thumbnail/frame from 10 second mark of the video
     * otherwise generate from the parameters passed
     * @param Float
     * @return Frame object
     * @return RuntimeException - In case the files
     * length is lower than 10 secs
     * @return InvalidArgumentException - In case the parameter passed
     * exceeds file's duration
     **/
    public function getFrame(float $quantity = null): Frame
    {
        if (is_null($quantity)) {
            if ($this->getDuration() > 9) {
                $quantity = 10;
            } else {
                throw new \RuntimeException('File should be atleast 10 seconds in length.');
            }
        } else {
            if ($this->getDuration() < $quantity) {
                throw new \InvalidArgumentException("Parameter passed exceeds file's duration.");
            }
        }

        return $this->getFrameFromTimecode(
            TimeCode::fromSeconds($quantity)
        );
    }

    public function getFrameFromTimecode(TimeCode $timecode): Frame
    {
        $frame = $this->media->frame($timecode);

        return new Frame($this->getFile(), $frame);
    }

    public function getFirstStream()
    {
        return $this->media->getStreams()->first();
    }

    public function getDurationInMiliseconds(): float
    {
        $stream = $this->getFirstStream();

        if ($stream->has('duration')) {
            return $stream->get('duration') * 1000;
        }

        $format = $this->media->getFormat();

        if ($format->has('duration')) {
            return $format->get('duration') * 1000;
        }

    }

    protected function encode($filepath, $options)
    {
        $video = $this->encoder->open($filepath);

        [$filename, $extension] = explode('.', $filepath);

        if (in_array($extension, $this->formats)) {
            $format = new $this->formats[$extension]('libmp3lame', 'libx264');
        }
    }

    /**
     * To do: move in Some class that implements MediaInterface class
     */
    public function save($filename, $options = [])
    {
        foreach($options as $key => $value) {
            $function = sprintf('set%s', ucwords($key));

            if (!method_exists($this, $function)) {
                throw new Exception(sprintf('[%s] option doesn\'t exists', $key));
            }

            $this->encode($this->filename, $options);
        }
    }
}

-- ----------------------------------------------------------------------
require 'vendor/autoload.php';

$ffmpeg = FFMpeg\FFMpeg::create();
$video = $ffmpeg->open('video.mpg');
$video
    ->filters()
    ->resize(new FFMpeg\Coordinate\Dimension(320, 240))
    ->synchronize();
$video
    ->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(10))
    ->save('frame.jpg');
$video
    ->save(new FFMpeg\Format\Video\X264(), 'export-x264.mp4')
    ->save(new FFMpeg\Format\Video\WMV(), 'export-wmv.wmv')
    ->save(new FFMpeg\Format\Video\WebM(), 'export-webm.webm');

$format = new X264('libmp3lame', 'libx264');
$lowBitrateFormat = $format->setKiloBitrate(5000);

$covidInstance = Covid::open($this->video->path)
    ->export()
    ->inFormat($lowBitrateFormat)  ->save(uniqid('5000kiloBit-NoResizeW-Audio') . '.mp4');

 -- ----------------------------------------------------------------------------------
$covid = Covid::open('sample.mkv');

$covid->getThumbnail() <-- return the first 10 seconds of the thumbnail
$covid->getLength(); // return 300

$covid->getThubmnail(300) <-- get the thubmanil at 5 minute mark
$covid->save('egg.mp4'); // save 100% quality
$covid->save('egg.mp4', 80); // save 80% quality

$covid->save('egg.mp4', [
    'quality' => 80,
    'bitrate' => 5000,
    'audio' => false
]);