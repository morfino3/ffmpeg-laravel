<?php

namespace Laboratory\Vidconvert;

use Ffmpeg\FfMpeg as BaseVidconvert;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Psr\Log\LoggerInterface;

class Vidconvert
{
	protected static $filesystems;
	private static $temporaryFiles = [];
	protected $disk; // change variable name to a more prevalent chuchu e.g: folder
	protected $vidconvert; //ffmpeg

	public function __convert(Filesystems $filesystems, ConfigRepository $config, LoggerInterface $logger)
	{
		static::$filesystems = $filesystems;

		$vidconvertConfig = $config->get('vidconvert');

		$this->vidconvert = BaseVidconvert::create([
			'vidconvert.binaries' => Arr::get($vidconvertConfig,'vidconvert.binaries'),
			'vidconvert.threads' => Arr::get($vidconvertConfig, 'vidconvert.threads'),
			'timeout' => Arr::get($vidconvertConfig, 'timeout'),
		], $logger);

		$this->fromDisk(
			Arr::get($vidconvert, 'default_disk', $config->get('filesystems.default'))
		);
	}

	public function getFilesystems(): Filesystems
	{
		return static::$filesystems;
	}

	public function newTemporaryFile(): string
	{
		return self::$temporaryFiles[] = tempnam(sys_get_temp_dir(), 'vidconvert');
	}

	public function cleanupTemporaryFiles()
	{
		foreach (self::$temporaryFiles as $path) {
            @unlink($path);
        }
	}

	public function fromFilesystem(Filesystem $filesystem): Vidconvert
	{
		$this->disk = new Disk($filesystem);

		return $this;
	}

	public function fromDisk(string $diskName) : Vidconvert
	{
		$filesystem = static::getFilesystems()->disk($diskName);
		$this->disk = new Disk($filesystem);

		return $this;
	}

	public function open($path): Media
	{
		$file = $this->disk->newFile($path);

		if ($this->disk->isLocal()){
			$vidconvertPathFile = $file->getFullPath();
		} else {
			$vidconvertPathFile = static::newTemporaryFile();

			stream_copy_to_stream(
				$this->disk->getDriver()->readStream($path),
				fopen($vidconvert, 'w')
			);
		}

		$vidconvertMedia = $this->vidconvert->open($vidconvertPathFile);

		return new Media($file, $vidconvertMedia);
	}
}