<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\search\algo\distance;

use pocketmine\math\Vector3;

class EuclideanAlgo extends DistanceAlgo {

	public function distance(Vector3 $a, Vector3 $b): float {
		return $a->distance($b); // support squared
	}
}
