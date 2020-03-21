<?php

namespace Laboratory\Covid\Tests;

use Exception;
use Mockery as m;
use Monolog\Logger;
use Laboratory\Covid\Covid;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Illuminate\Config\Repository as ConfigRepository;

class CovidTest extends PHPUnitTestCase
{
    public function setUp(): void
    {
        $this->srcDir = __DIR__ . '/src';

        $this->remoteFilesystem = false;
    }

    public function getDefaultConfig()
    {
        return require __DIR__ . '/../config/covid-ubuntu.php';
    }

    public function getService(): Covid
    {
        $config = m::mock(ConfigRepository::class);

        $config->shouldReceive('get')->once()->with('covid')->andReturn($this->getDefaultConfig());

        return new Covid($config);
    }

    public function getVideoMedia()
    {
        $covid = $this->getService();

        return $covid->open(__DIR__ . '/src/egg.mp4');
    }

    public function testGetResolution()
    {
        $getResolution = $this->getVideoMedia()->getResolution();

        $this->assertEquals('1920 x 1080', $getResolution);

    }

    public function testGetCodec()
    {
        $getCodec = $this->getVideoMedia()->getCodec();

        $this->assertEquals('h264', $getCodec);

    }

    public function testGetDuration()
    {
        $getDuration = $this->getVideoMedia()->getDuration();

        $this->assertEquals(14, $getDuration);

    }

    public function testConvertVideo()
    {
        $this->expectException(Exception::class);

        //since converting to mov file is not supported
        $this->getVideoMedia()->save(__DIR__ . '/src/Newegg.mov', [
            'bitrate' => 5000,
            'audio' => 512
        ]);
    }
}