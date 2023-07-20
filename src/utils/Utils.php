<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\utils;

use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;

class Utils {

	/**
	 * @param AxisAlignedBB $bb
	 * @param Vector3 $pos
	 *
	 * @return Vector3 point
	 *
	 * O(3)
	 */
	public static function getNearestPoint(AxisAlignedBB $bb, Vector3 $pos): Vector3 {
		$point = Vector3::zero();

		$min = new Vector3($bb->minX, $bb->minY, $bb->minZ);
		$max = new Vector3($bb->maxX, $bb->maxY, $bb->maxZ);

		$minDist = $pos->subtractVector($min)->abs();
		$maxDist = $pos->subtractVector($max)->abs();

		foreach (["x", "y", "z"] as $o) {
			if ($minDist->$o > $maxDist->$o) {
				$point->$o = $max->$o;
			} else {
				$point->$o = $min->$o;
			}
		}

		return $point;
	}
}
