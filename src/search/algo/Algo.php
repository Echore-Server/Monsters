<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\search\algo;

use Lyrica0954\Monsters\search\algo\distance\DistanceAlgo;

class Algo {

	protected DistanceAlgo $distance;

	public function __construct(DistanceAlgo $distance) {
		$this->distance = $distance;
	}

	/**
	 * Get the value of distance
	 *
	 * @return DistanceAlgo
	 */
	public function getDistance(): DistanceAlgo {
		return $this->distance;
	}

	public function processDistanceSquared(float $distance): float {
		return $this->distance->hasSupportSquared() ? $distance ** 2 : $distance;
	}
}
