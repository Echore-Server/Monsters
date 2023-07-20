<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\search\shape;

use pocketmine\math\Vector2;
use pocketmine\math\Vector3;

class CircularSectorShape implements IShape {

	public Vector3 $center;

	public float $rangeDegree;

	public float $radius;

	public Vector2 $direction;

	public function __construct(Vector3 $center, float $rangeDegree, float $radius, Vector2 $direction) {
		$this->center = $center;
		$this->rangeDegree = $rangeDegree;
		$this->radius = $radius;
		$this->direction = $direction;
	}

	public function getSumOfInteriorAngles(): float {
		return 360;
	}
}
