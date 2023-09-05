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
		return new Vector3(
			max($bb->minX, min($pos->x, $bb->maxX)),
			max($bb->minY, min($pos->y, $bb->maxY)),
			max($bb->minZ, min($pos->z, $bb->maxZ)),
		);
	}
}
