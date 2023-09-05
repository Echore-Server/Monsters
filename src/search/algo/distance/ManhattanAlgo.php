<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\search\algo\distance;

use pocketmine\math\Vector3;

class ManhattanAlgo extends DistanceAlgo {

	public function distanceSquaredIfSupported(Vector3 $a, Vector3 $b): float {
		return $this->distance($a, $b);
	}

	public function distance(Vector3 $a, Vector3 $b): float {
		$d = $b->subtractVector($a)->abs();

		return $d->x + $d->y + $d->z;
	}

	public function hasSupportSquared(): bool {
		return false;
	}
}
