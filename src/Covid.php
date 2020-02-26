<?php

namespace Laboratory\Covid;

use Illuminate\Support\Arr;
use Psr\Log\LoggerInterface;
use Ffmpeg\FfMpeg as BaseVidconvert;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

class Covid
{
	protected static $filesystems;
	private static $temporaryFiles = [];
	protected $disk; // change variable name to a more prevalent chuchu e.g: folder
	protected $covid; //ffmpeg

	public function __convert(Filesystems $filesystems, ConfigRepository $config, LoggerInterface $logger)
	{
		static::$filesystems = $filesystems;

		$vidconvertConfig = $config->get('vidconvert');

		$this->covid = BaseCovid::create([
			'ffmpeg.binaries' => Arr::get($covidConfig,'ffmpeg.binaries'),
			'ffmpeg.threads' => Arr::get($covidConfig, 'ffmpeg.threads'),
			'ffprobe.binaries' => Arr::get($ffmpegConfig, 'ffprobe.binaries'),
			'timeout' => Arr::get($covidConfig, 'timeout'),
		], $logger);

		$this->fromDisk(
			Arr::get($covid, 'default_disk', $config->get('filesystems.default'))
		);
	}

	public function getFilesystems(): Filesystems
	{
		return static::$filesystems;
	}

	public function newTemporaryFile(): string
	{
		return self::$temporaryFiles[] = tempnam(sys_get_temp_dir(), 'covid');
	}

	public function cleanupTemporaryFiles()
	{
		foreach (self::$temporaryFiles as $path) {
            @unlink($path);
        }
	}

	public function fromFilesystem(Filesystem $filesystem): Covid
	{
		$this->disk = new Disk($filesystem);

		return $this;
	}

	public function fromDisk(string $diskName) : Covid
	{
		$filesystem = static::getFilesystems()->disk($diskName);
		$this->disk = new Disk($filesystem);

		return $this;
	}

	public function open($path): Media
	{
		$file = $this->disk->newFile($path);

		if ($this->disk->isLocal()){
			$covidPathFile = $file->getFullPath();
		} else {
			$covidPathFile = static::newTemporaryFile();

			stream_copy_to_stream(
				$this->disk->getDriver()->readStream($path),
				fopen($covid, 'w')
			);
		}

		$covidMedia = $this->covid->open($covidPathFile);

		return new Media($file, $covidMedia);
	}
}