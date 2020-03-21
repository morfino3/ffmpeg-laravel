<?php

namespace Laboratory\Covid;

use Exception;
use FFMpeg\Media\Frame;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg as BaseFFMpeg;
use FFMpeg\Format\Audio\Mp3 as Mp3;
use FFMpeg\Format\Video\X264 as X264;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

class Covid
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

    public function __construct(ConfigRepository $config)
    {
        $ffmpegConfig = $config->get('covid');

        $this->ffmpeg = BaseFFMpeg::create();
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
     * Generates thumbnail/frame from 10 second mark of the video
     * otherwise generate from the parameters passed
     * @param Float
     * @return Frame object
     * @return RuntimeException - In case the files
     * length is lower than 10 secs
     * @return InvalidArgumentException - In case the parameter passed
     * exceeds file's duration
     **/
    public function getThumbnail($path, float $quantity = null)
    {
        if (is_null($quantity)) {
            if ($this->getDuration() > 9) {
                $quantity = 10;
            } else {
                throw new Exception('File should be atleast 10 seconds in length.');
            }
        } else {
            if ($this->getDuration() < $quantity) {
                throw new Exception("Parameter passed exceeds file's duration.");
            }
        }

        $this->ffmpegMedia->frame(
            TimeCode::fromSeconds($quantity)->save($path)
        );

        return $this;
    }

    /**
     * Get video resolution, width x height
     *
     * @return string
     **/
    public function getResolution() : string
    {
        $dimensions = $this->getFirstStream()->getDimensions();

        $width = $dimensions->getWidth();
        $height = $dimensions->getHeight();

        return $width . ' x ' . $height;
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
       [$filename, $extension] = explode('.', $file);

        $this->setVideoOptions($options);

        switch ($extension) {
            case 'mp4':
                $format = $this->encode(new X264('libmp3lame', 'libx264'), $file);
                $this->file_extension = 'mp4';
                break;
            case 'webm':
                $format = $this->encode(new X264('libmp3lame', 'libx264'), $file);
                $this->file_extension = 'webm';
                break;
            case 'mp3':
                $format = $this->encode(new Mp3(), $file);
                $this->file_extension = 'mp3';
                break;

            default:
                throw new Exception('Format isn\'t supported at the moment');
                break;
        }

        return $this->ffmpegMedia->save($format, $file . '.' . $this->file_extension);
    }
}