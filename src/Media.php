<?php

namespace Laboratory\Covid;

use Closure;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\Filters\Audio\SimpleFilter;
use FFMpeg\Filters\FilterInterface;
use FFMpeg\Media\MediaTypeInterface;

/**
 * @method mixed save(\FFMpeg\Format\FormatInterface $format, $outputPathfile)
 */
class Media
{
	protected $file;

	protected $media;

	public function __construct(File $file, MediaTypeInterface $media)
	{
		$this->file = $file;
		$this->media = $media;
	}

	public function isFrame(): bool
	{
		return $this instanceof Frame;
	}

	public function getFile(): File
	{
		return $this->file;
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

	/**
	 * Get video width
	 *
	 * @return integer
	 **/
	public function getVideoWidth()
	{
		$dimensions = $this->media->getStreams()->first()->getDimensions();

		return $dimensions->getWidth();
	}

	/**
	 * Get video height
	 *
	 * @return integer
	 **/
	public function getVideoHeight()
	{
		$dimensions = $this->media->getStreams()->first()->getDimensions();

		return $dimensions->getHeight();
	}

	/**
	 * Get video resolution, width x height
	 *
	 * @return string
	 **/
	public function getResolution() : string
	{
		$dimensions = $this->media->getStreams()->first()->getDimensions();

		$width = $dimensions->getWidth();
		$height = $dimensions->getHeight();

		return $width . ' x ' . $height;
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

	public function export(): MediaExporter
	{
		return new MediaExporter($this);
	}

	public function exportForHLS(): HLSPlaylistExporter
	{
		return new HLSPlaylistExporter($this);
	}

	public function getThumbnailFromString(string $timecode): Frame
	{
		return $this->getFrameFromTimecode(
			TimeCode::fromString($timecode)
		);
	}

	/**
	 * Generates thumbnail from 10 seconds mark of the video
	 * otherwise generate from seconds mark from the parameters
	 * passed
	 * @param Float
	 * @return Frame object
	 * @return RuntimeException - In case the files
	 * length is lower than 10 secs
	 * @return InvalidArgumentException - In case the parameter passed
	 * exceeds file's duration
	 **/
	public function getThumbnail(float $quantity = null): Frame
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

	public function addFilter(): Media
	{
		$arguments = func_get_args();

		if (isset($arguments[0]) && $arguments[0] instanceof Closure) {
            call_user_func_array($arguments[0], [$this->media->filters()]);
        } else if (isset($arguments[0]) && $arguments[0] instanceof FilterInterface) {
            call_user_func_array([$this->media, 'addFilter'], $arguments);
        } else if (isset($arguments[0]) && is_array($arguments[0])) {
            $this->media->addFilter(new SimpleFilter($arguments[0]));
        } else {
            $this->media->addFilter(new SimpleFilter($arguments));
        }

        return $this;
	}

	public function selfOrArgument($argument)
	{
		return ($argument === $this->media) ? $this: $argument;
	}

	public function __invoke(): MediaTypeInterface
	{
		return $this->media;
	}

	public function __clone()
	{
		if ($this->media) {
            $clonedFilters = clone $this->media->getFiltersCollection();

            $this->media = clone $this->media;

            $this->media->setFiltersCollection($clonedFilters);
        }
	}

	public function __call($method, $parameters)
	{
		return $this->selfOrArgument(
			call_user_func_array([$this->media, $method], $parameters)
		);
	}
}