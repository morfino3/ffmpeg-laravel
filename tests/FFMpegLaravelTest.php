<?php

namespace FFMpegLaravelTest\FFMpegLaravel\Tests;

use Exception;
use Mockery as m;
use Monolog\Logger;
use FFMpegLaravel\FFMpegLaravel\FFMpegLaravel;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Illuminate\Config\Repository as ConfigRepository;

class FFMpegLaravelTest extends PHPUnitTestCase
{
    public function setUp(): void
    {
        $this->srcDir = __DIR__ . '/src';

        $this->remoteFilesystem = false;
    }

    // Change the config base on environment
    public function getDefaultConfig()
    {
        return require __DIR__ . '/../config/ffmpeglaravel-mac.php';
    }

    public function getService(): FFMpegLaravel
    {
        $config = m::mock(ConfigRepository::class);

        $config->shouldReceive('get')->once()->with('ffmpeglaravel')->andReturn($this->getDefaultConfig());

        return new FFMpegLaravel(\FFMpeg\FFMpeg::create(), $config);
    }

    public function getVideoMedia()
    {
        $ffmpeglaravel = $this->getService();

        return $ffmpeglaravel->open(__DIR__ . '/src/egg.mp4');
    }

    public function testGetResolution()
    {
        $getResolution = $this->getVideoMedia()->getResolution();

        $resolution = $getResolution['width'] .' x '.$getResolution['height'];

        $this->assertEquals('1280 x 720', $resolution);
    }

    public function testGetCodec()
    {
        $getCodec = $this->getVideoMedia()->getCodec();

        $this->assertEquals('h264', $getCodec);

    }

    public function testGetDuration()
    {
        $getDuration = $this->getVideoMedia()->getDuration();

        $this->assertEquals(36, $getDuration);

    }

    public function testConvertVideo()
    {
        $this->getVideoMedia()->save(__DIR__ . '/output/Newegg.mp4', [
            'bitrate' => 1200,
        ]);

        $this->assertFileExists((__DIR__ . '/output/Newegg.mp4'));
    }

    public function testConvertSmallVideo()
    {
        $ffmpeglaravel = $this->getService();

        $ffmpeglaravelInstance = $ffmpeglaravel->open(__DIR__ . '/src/small.mkv');

        $ffmpeglaravelInstance->save(__DIR__ . '/output/newSmall.mp4', [
            'bitrate' => 1200,
            'audio' => 512
        ]);
        $this->assertFileExists((__DIR__ . '/output/newSmall.mp4'));
    }

    public function testConvertAudio()
    {
        $this->getVideoMedia()->save(__DIR__ . '/output/egg.mp3');

        $this->assertFileExists((__DIR__ . '/output/egg.mp3'));
    }


    public function testResizeVideo()
    {
        $this->getVideoMedia()->resize(640, 480, false)
                    ->save(__DIR__ . '/output/resized_egg.mp4', [
                        'bitrate' => 500,
                        'audio' => 256
                    ]);

        $ffmpeglaravel = $this->getService();

        $ffmpeglaravelInstance = $ffmpeglaravel->open(__DIR__ . '/output/resized_egg.mp4');

        $getResolution = $ffmpeglaravelInstance->getResolution();

        $resolution = $getResolution['width'] .' x '.$getResolution['height'];

        $this->assertEquals('853 x 480', $resolution);
    }

    public function testVideoMute()
    {
        $this->getVideoMedia()->mute()
            ->save(__DIR__ . '/output/muted_egg.mp4');

        $this->assertFileExists((__DIR__ . '/output/muted_egg.mp4'));
    }

    public function testGenerateGif()
    {
        $this->expectException(Exception::class);

        //since GIF duration should not exceeds files duration

        $this->getVideoMedia()->generateGif(__DIR__ . '/output/sample.gif', 300, 2);

    }

    public function testGenerateSmallGif()
    {
        $ffmpeglaravel = $this->getService();

        $ffmpeglaravelInstance = $ffmpeglaravel->open(__DIR__ . '/src/small.mkv');
        $ffmpeglaravelInstance->generateGif(__DIR__ . '/output/newSmall.gif', 4);

        $this->assertFileExists((__DIR__ . '/output/newSmall.gif'));
    }

    public function testGenerateThumbnail()
    {
        $ffmpeglaravel = $this->getService();

        $ffmpeglaravelInstance = $ffmpeglaravel->open(__DIR__ . '/src/small.mkv');
        $ffmpeglaravelInstance->getThumbnail(__DIR__ . '/output/thumbnail.jpg');

        $this->assertFileExists((__DIR__ . '/output/thumbnail.jpg'));
    }
}