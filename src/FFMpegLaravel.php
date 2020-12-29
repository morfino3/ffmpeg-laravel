<?php

namespace FFMpegLaravel\FFMpegLaravel;

use Exception;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg as BaseFFMpeg;
use FFMpeg\Filters\Audio\SimpleFilter as Filter;
use FFMpeg\Format\Audio\Mp3 as Mp3;
use FFMpeg\Format\Video\WebM;
use FFMpeg\Format\Video\X264 as X264;
use FFMpeg\Media\Frame;

class FFMpegLaravel
{
    protected $ffmpeg;

    protected $file_extension;

    protected $ffmpegMedia;

    protected $filepath;

    protected $options = [
        'channel' => [
            'key' => 'setAudioChannels',
            'value' => 2
        ],
        'bitrate' => [
            'key' => 'setKiloBitrate',
            'value' => 1000
        ],
        'audio' => [
            'key' => 'setAudioKiloBitrate',
            'value' => 256
        ]
    ];

    public function __construct($provider = null)
    {
        $this->ffmpeg = $provider;
    }

    public function open($filepath)
    {
        if (!file_exists($filepath)) {
            throw new Exception(sprintf('%s doesn\'t exist.', $filepath));
        }

        $this->filepath = $filepath;

        $this->ffmpegMedia = $this->ffmpeg->open($filepath);


        return $this;
    }


    public function getFirstStream()
    {
        $firststream = $this->ffmpegMedia->getStreams()->first();

        return $firststream;
    }

    /**
     * Resize video
     * @param width and height of positive integer
     * @return Filter object
     * @return Exception - In case width is not provided, and/or
     * negative or 0 values
     * @return Exception - In case height is not provided, and/or
     * negative or 0 values
     **/
    public function resize($width = null, $height = null)
    {
        if(!($width)) {
            throw new Exception('Provide a valid width in positive integer.');
        }
        if(!($height)) {
            throw new Exception('Provide a valid height in positive integer.');
        }

        $resizedVideo = $this->ffmpegMedia
                    ->filters()
                    ->resize(new Dimension($width, $height))
                    ->synchronize();

        return $this;
    }

    /**
     * Mute
     *
     * removes audio from video
     * @return Filter object
     *
     **/
    public function mute()
    {
        $muteVideo = $this->ffmpegMedia
             ->addFilter(new Filter(['-an']));

        return $this;
    }

    /**
     * Generates thumbnail/frame from 10 second mark of the video
     * otherwise generate from the parameters passed
     * @param Float
     * @return Frame object
     * @return Exception - In case the parameter passed
     * exceeds file's duration
     **/
    public function getThumbnail($path, $duration = null)
    {
        if ($this->getDuration() < $duration) {
            throw new Exception('Parameter passed exceeds file\'s duration.');
        }

        if (!($duration)) {
            $duration = 10;
        }

        if ($this->getDuration() < 10) {
            $duration = floor($this->getDuration() / 2);
        }

        $this->ffmpegMedia->frame(
            TimeCode::fromSeconds($duration)
        )->save($path);

        return $this;
    }

    /**
     * Generates GIF from a video with duration params
     * otherwise generates a static GIF image
     * @param path [string] - filepath of the new gif
     * @param duration [integer]
     * @param fromSeconds [integer]
     * @return Gif object
     * @return Exception - In case the files length is lower
     * than duration passed
     * @return Exception - In case the filepath is not provided
     **/
    public function generateGif($newFilePath, $duration = null, $fromSeconds = null)
    {
        $dimensions = $this->getFirstStream()->getDimensions();

        $width = $dimensions->getWidth();
        $height = $dimensions->getHeight();

        if (!($fromSeconds)) {
            $fromSeconds = 1;
        }

        if ($this->getDuration() < $duration) {
            throw new Exception('Parameter passed exceeds file\'s duration.');
        }

        if ($this->getDuration() < 10) {
            $duration = floor($this->getDuration() / 2);
        }

        if (!($newFilePath)) {
            throw new Exception('No file path provided for the new GIF.');
        }

        $gif = $this->ffmpegMedia
                    ->gif(TimeCode::fromSeconds($fromSeconds), new Dimension($width, $height), $duration);

        $gif->save($newFilePath);

        return $this;
    }

    /**
     * Get video resolution: width & height
     *
     * @return array
     **/
    public function getResolution()
    {
        $dimensions = $this->getFirstStream()->getDimensions();

        $width = $dimensions->getWidth();
        $height = $dimensions->getHeight();

        $resolution = ['width' => $width,'height' => $height];

        return $resolution;
    }

    /**
     * Get duration of file in seconds
     *
     * @return int
     **/
    public function getDuration(): int
    {
        return $this->getDurationInMiliseconds() / 1000;
    }

    /**
     * Get video width
     *
     * @return integer
     **/
    public function getWidth()
    {
        $dimensions = $this->getFirstStream();

        return $dimensions->getWidth();
    }

    /**
     * Get video height
     *
     * @return integer
     **/
    public function getHeight()
    {
        $dimensions = $this->getFirstStream();

        return $dimensions->getHeight();
    }

    /**
     * Get the codec of the file
     *
     * @return string
     **/
    public function getCodec(): string
    {
        $stream = $this->getFirstStream();

        if ($stream->has('codec_name')) {
            return $stream->get('codec_name');
        } else {
            throw new Exception('No codec available');
        }
    }

    public function getDurationInMiliseconds(): float
    {
        $stream = $this->getFirstStream();

        if ($stream->has('duration')) {
            return $stream->get('duration') * 1000;
        }

        $format = $this->ffmpegMedia->getFormat();

        if ($format->has('duration')) {
            return $format->get('duration') * 1000;
        }
    }

    /**
     * [setBitrate description]
     */
    public function setAudio($rate = 256)
    {
        $this->options['audio']['value'] = $rate;
    }

    /**
     * [setVideoOptions description]
     * @param [type] $format [description]
     * @param [type] $file   [description]
     */
    protected function setVideoOptions($options)
    {
        foreach($options as $key => $value) {
            $this->options[$key]['value'] = $value;
        }
    }

    /**
     * Encode
     */
    protected function encode($format, $file)
    {
        foreach($this->options as $option) {
            $format->{$option['key']}($option['value']);
        }

        return $format;
    }

    public function save($filename, $options = [])
    {
       [$filename, $extension] = explode('.', $filename);

        $this->setVideoOptions($options);

        switch ($extension) {
            case 'mp4':
                $format = $this->encode(new X264('libmp3lame', 'libx264'), $filename);
                $this->file_extension = 'mp4';
                break;
            case 'webm':
                $format = new WebM();
                $this->file_extension = 'webm';
                break;
            case 'mp3':
                $format = new Mp3();
                $this->file_extension = 'mp3';
                break;

            default:
                throw new Exception('Format isn\'t supported at the moment');
                break;
        }

        return $this->ffmpegMedia->save($format, $filename . '.' . $this->file_extension);
    }
}