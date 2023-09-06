<?php


declare(strict_types=1);

namespace Lyrica0954\Monsters\search\algo\distance;

use Lyrica0954\Monsters\timings\MonstersTimings;
use Lyrica0954\Monsters\utils\Utils;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;

abstract class DistanceAlgo {

	public function distanceBoundingBox(AxisAlignedBB $bb, Vector3 $pos): float {
		MonstersTimings::$boundingBoxDistance->startTiming();
		$point = Utils::getNearestPoint($bb, $pos);

		$dist = $this->distance($point, $pos);

		MonstersTimings::$boundingBoxDistance->stopTiming();

		return $dist;
	}

	abstract public function distance(Vector3 $a, Vector3 $b): float;

	public function distanceSquaredBoundingBoxIfSupported(AxisAlignedBB $bb, Vector3 $pos): float {
		MonstersTimings::$boundingBoxDistance->startTiming();
		$point = Utils::getNearestPoint($bb, $pos);

		$dist = $this->distanceSquaredIfSupported($point, $pos);

		MonstersTimings::$boundingBoxDistance->stopTiming();

		return $dist;
	}

	abstract public function distanceSquaredIfSupported(Vector3 $a, Vector3 $b): float;

	abstract public function hasSupportSquared(): bool;
}
