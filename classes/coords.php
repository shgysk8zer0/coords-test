<?php

final class Coords implements JSONSerializable
{
	public $longitude = 0;
	public $latitude  = 0;

	public function __construct(Float $longitude, Float $latitude)
	{
		$this->longitude = $longitude;
		$this->latitude  = $latitude;
	}

	public function jsonSerialize() {
		return [
			'longitude' => $this->longitude,
			'latitude'  => $this->latitude,
		];
	}
}
