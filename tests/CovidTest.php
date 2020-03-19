<?php

namespace Laboratory\Covid\Tests;

use Mockery;
use Exception;
use Monolog\Logger;
use League\Flysystem\Adapter\Ftp;
use Illuminate\Log\Logger as Writer;
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

    public function getService(): \Laboratory\Covid
    {
        $config = Mockery::mock(ConfigRepository::class);

        $config->shouldReceive('get')->once()->with('covid')->andReturn($this->getDefaultConfig());

        return new \Laboratory\Covid($config);
    }

    public function getVideoMedia()
    {
        $covid = $this->getService();

        return $covid->open(base_path() . '/tests/src/egg.mov');
    }

    public function testSave()
    {
        $this->expectException(Exception::class);

        $this->getVideoMedia()->save(base_path() . '/tests/src/newEgg.mp4', [
            'bitrate' => 5000,
            'audio' => 512
        ]);
    }
}
