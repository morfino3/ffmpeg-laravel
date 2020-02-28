<?php

namespace Laboratory\Covid;

use Illuminate\Support\Arr;
use Psr\Log\LoggerInterface;
use FFMpeg\FFMpeg as BaseFFMpeg;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Filesystem\Factory as Filesystems;
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

		$ffmpegConfig = $config->get('covid');

		$this->covid = BaseFFMpeg::create([
			'ffmpeg.binaries' 	=> Arr::get($ffmpegConfig,'ffmpeg.binaries'),
			'ffmpeg.threads' 	=> Arr::get($ffmpegConfig, 'ffmpeg.threads'),
			'ffprobe.binaries'  => Arr::get($ffmpegConfig, 'ffprobe.binaries'),
			'timeout' 			=> Arr::get($ffmpegConfig, 'timeout'),
		], $logger);

		$this->fromDisk(
			Arr::get($ffmpegConfig, 'default_disk', $config->get('filesystems.default'))
		);
	}

	public static function getFilesystems(): Filesystems
	{
        return static::$filesystems;
    }

	public static function newTemporaryFile(): string
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
			$ffmpegPathFile = $file->getFullPath();
		} else {
			$ffmpegPathFile = static::newTemporaryFile();

			stream_copy_to_stream(
				$this->disk->getDriver()->readStream($path),
				fopen($ffmpegPathFile, 'w')
			);
		}

		$ffmpegMedia = $this->covid->open($ffmpegPathFile);

		return new Media($file, $ffmpegMedia);
	}
}