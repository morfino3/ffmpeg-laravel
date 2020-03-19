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

        return $covid->open(__DIR__ . '/src/egg.mov');
    }

    public function testSave()
    {
        $this->expectException(Exception::class);

        $this->getVideoMedia()->save(__DIR__ . '/src/Newegg.mp4', [
            'bitrate' => 5000,
            'audio' => 512
        ]);
    }
}
