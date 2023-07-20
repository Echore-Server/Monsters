<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\search\algo\distance;

use pocketmine\math\Vector3;

class ManhattanAlgo extends DistanceAlgo {

	public function distance(Vector3 $a, Vector3 $b): float {
		$d = $b->subtractVector($a)->abs();

		return $d->x + $d->y + $d->z;
	}
}
