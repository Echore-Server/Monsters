<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\timings;

use pocketmine\timings\TimingsHandler;

class MonstersTimings {

	public static TimingsHandler $search;

	public static TimingsHandler $searchAreaEntities;

	public static TimingsHandler $boundingBoxDistance;

	public static TimingsHandler $filtering;

	public static function init(): void {
		$group = "Monsters";
		self::$search = new TimingsHandler("Entity Searching System", group: $group);
		self::$searchAreaEntities = new TimingsHandler("Area Entities", self::$search, $group);
		self::$boundingBoxDistance = new TimingsHandler("Calculate Bounding Box Distance", group: $group);

		self::$filtering = new TimingsHandler("Entity Filtering", self::$search, $group);

	}

}
