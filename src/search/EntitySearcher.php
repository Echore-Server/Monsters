<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\search;

use Lyrica0954\Monsters\search\shape\CircularSectorShape;
use Lyrica0954\Monsters\search\shape\TriangleShape;
use Lyrica0954\Monsters\utils\RayTraceEntityResult;
use pocketmine\entity\Entity;
use pocketmine\math\Vector2;
use pocketmine\math\Vector3;
use pocketmine\world\Position;
use pocketmine\world\World;

interface EntitySearcher {

	/**
	 * @param Position $position
	 * @param float $rangeMin
	 * @param float $rangeMax
	 * @param EntitySearchOption|null $option
	 *
	 * @return Entity[]
	 */
	public function getWithinSpecifyRange(Position $position, float $rangeMin, float $rangeMax, EntitySearchOption $option = null): array;

	/**
	 * @param Position $position
	 * @param float $range
	 * @param EntitySearchOption|null $option
	 *
	 * @return Entity[]
	 */
	public function getWithinRange(Position $position, float $range, EntitySearchOption $option = null): array;

	/**
	 * @param Vector2 $position
	 * @param World $world
	 * @param float $rangeMin
	 * @param float $rangeMax
	 * @param EntitySearchOption|null $option
	 *
	 * @return Entity[]
	 */
	public function getWithinSpecifyRangePlane(Vector2 $position, World $world, float $rangeMin, float $rangeMax, EntitySearchOption $option = null): array;

	/**
	 * @param Vector2 $position
	 * @param World $world
	 * @param float $range
	 * @param EntitySearchOption|null $option
	 *
	 * @return Entity[]
	 */
	public function getWithinRangePlane(Vector2 $position, World $world, float $range, EntitySearchOption $option = null): array;

	/**
	 * @param Vector2 $position
	 * @param float $distance
	 * @return ChunkSize
	 */
	public function getChunkSize(Vector2 $position, float $distance): ChunkSize;

	/**
	 * @param Position $position
	 * @param float $distance
	 * @param EntitySearchOption|null $option
	 * @return Entity[]
	 */
	public function getAreaEntities(Position $position, float $distance, EntitySearchOption $option = null): array;


	/**
	 * @param Position $position
	 * @param float $maxDistance
	 * @param EntitySearchOption|null $option
	 * @return EntitySearchResult|null
	 */
	public function getNearest(Position $position, float $maxDistance = PHP_INT_MAX, EntitySearchOption $option = null): ?EntitySearchResult;

	/**
	 * @param Position $position
	 * @param Vector3 $direction
	 * @param float $length
	 * @param Vector3|null $expand
	 * @param EntitySearchOption|null $option
	 * @return RayTraceEntityResult[]
	 */
	public function getLineOfSight(Position $position, Vector3 $direction, float $length, ?Vector3 $expand = null, EntitySearchOption $option = null): array;

	/**
	 * @param TriangleShape $shape
	 * @param World $world
	 * @param EntitySearchOption|null $option
	 *
	 * @return Entity[]
	 *
	 * @experimental
	 */
	public function getInsideOfTriangle(TriangleShape $shape, World $world, EntitySearchOption $option = null): array;

	/**
	 * @param CircularSectorShape $shape
	 * @param World $world
	 * @param float $height
	 * @param EntitySearchOption|null $option
	 *
	 * @return Entity[]
	 */
	public function getInsideOfCircularSector(CircularSectorShape $shape, World $world, float $height, EntitySearchOption $option = null): array;
}
