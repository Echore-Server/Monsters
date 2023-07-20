<?php


declare(strict_types=1);

namespace Lyrica0954\Monsters\search\algo\distance;

use Lyrica0954\Monsters\utils\Utils;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;

abstract class DistanceAlgo {

	public function distanceBoundingBox(AxisAlignedBB $bb, Vector3 $pos): float {
		$point = Utils::getNearestPoint($bb, $pos);

		return $this->distance($point, $pos);
	}

	abstract public function distance(Vector3 $a, Vector3 $b): float;
}
