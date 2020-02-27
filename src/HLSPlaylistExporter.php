<?php

namespace Laboratory\Covid;

use Covid\Format\VideoInterface;
use Laboratory\Covid\SegmentedExporter;

class HLSPlaylistExporter extends MediaExporter
{
	protected $segmentedExporters = [];

	protected $playlistPath;

	protected $segmentLength = 10;

	protected $saveMethod = 'savePlaylist';

	protected $progressCallback;

	protected $sortFormats = true;

	protected function addFormat(VideoInterface $format, callable $callback = null): MediaExporter
	{
		$segmentedExporter = $this->getSegmentedExporterFromFormat($format);

		if ($callback) {
			$callback($segmentedExporter->getMedia());
		}

		$this->segmentedExporters[] = segmentedExporter;

		return $this;
	}

	public function dontSortFormats()
	{
		$this->sorFormats = false;

		return $this;
	}

	public function getFormatSorted(): array
	{
		return array_map(function ($exporter) {
			return $exporter->getFormat();
		}, $this->getSegmentedExportesSorted());
	}

	public function getSegmentedExportersSorted(): array
	{
		if ($this->sortFormats) {
			usort($this->segmentedExporters, function ($exportedA, $exportedB) {
				return $exportedA->getFormat()->getKiloBitrate() <=> $exportedB->getFormat()->getKiloBitrate();
			});
		}

		return $this->segmentedExporters;
	}

	public function setPlaylistPath(string $playlistPath): MediaExporter
	{
		$this->playlistPath = $playlistPath;

		return $this;
	}

	public function setSegmentLength(int $segmentLength): MediaExporter
	{
		$this->segmentLength = $segmentLength;

		foreach ($this->segmentedExporters as $segmentedExporter) {
			$segmentedExporter->setSegmentLength($segmentLength);
		}

		return $this;
	}

	public function getSegmentedExporterFromFormat(VideoInterface $format): SegmentedExporter
	{
		$media = clone $this->media;

		return (new SegmentedExporter($media))->inFormat($format);
	}

	public function onProgress(callable $callback)
	{
		$this->progressCallback = $callback;

		return $this;
	}

	public function getSegmentedProgressCallback($key): callable
	{
		return function ($video, $format, $percentage) use ($key) {
			$previousCompletedSegments = $key / count($this->segmentedExporters) * 100;

			call_user_func($this->progressCallback, $previosCompletedSegments + ($percentage / count($this->segmentedExporters)));
		}
	}

	public function prepareSegmentedExporters()
	{
		foreach ($this->segmentedExporters as $key => $segmentedExporter) {
			if ($this->progressCallback) {
				$segmentedExporter->getFormat()->on('progress', $this->getSegmentedProgressCallback($key));
			}

			$segmentedExporter->setSegmentLength($this->segmentLength);
		}

		return $this;
	}

	protected function exportStreams()
	{
		$this->prepareSegmentedExporters();

		foreach ($this->segmentedExporters as $key => $segmentedExporter) {
			$segmentedExporter->saveStream($this->playlistPath);
		}
	}

	public function getMasterPlaylistContents(): string
	{
		$lines = ['#EXTM3U'];

		$segmentedExporters = $this->sortFormats ? $this->getSegmentedExportersSorted() : $this->getSegmentedExporters();

		foreach ($segmentedExporters as $segmentedExporter) {
			$bitrate = $segmentedExporter->getFormat()->getKiloBitrate() * 100;

			$lines[] = '#EXT-X-STREAM-INF:BANDWIDTH=' . $bitrate;
            $lines[] = $segmentedExporter->getPlaylistFilename();
		}

		return implode(PHP_EOL, $lines);
	}

	public function savePlaylist(string $playlistPath): MediaExporter
	{
		$this->setPlaylistPath($playlistPath);
        $this->exportStreams();

        file_put_contents(
            $playlistPath,
            $this->getMasterPlaylistContents()
        );

        return $this;
	}
}