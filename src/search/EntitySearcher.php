<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\search;

use Generator;
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
	 * @return Generator|Entity[]
	 */
	public function getWithinSpecifyRange(Position $position, float $rangeMin, float $rangeMax, EntitySearchOption $option = null): Generator;

	/**
	 * @param Position $position
	 * @param float $range
	 * @param EntitySearchOption|null $option
	 *
	 * @return Generator|Entity[]
	 */
	public function getWithinRange(Position $position, float $range, EntitySearchOption $option = null): Generator;

	/**
	 * @param Vector2 $position
	 * @param World $world
	 * @param float $rangeMin
	 * @param float $rangeMax
	 * @param EntitySearchOption|null $option
	 *
	 * @return Generator|Entity[]
	 */
	public function getWithinSpecifyRangePlane(Vector2 $position, World $world, float $rangeMin, float $rangeMax, EntitySearchOption $option = null): Generator;

	/**
	 * @param Vector2 $position
	 * @param World $world
	 * @param float $range
	 * @param EntitySearchOption|null $option
	 *
	 * @return Generator|Entity[]
	 */
	public function getWithinRangePlane(Vector2 $position, World $world, float $range, EntitySearchOption $option = null): Generator;

	/**
	 * @param Vector2 $position
	 * @param float $range
	 *
	 * @return ChunkSize
	 */
	public function getChunkSize(Vector2 $position, float $distance): ChunkSize;

	/**
	 * @param Position $position
	 * @param float $distance
	 *
	 * @return Generator|Entity[]
	 */
	public function getAreaEntities(Position $position, float $distance, EntitySearchOption $option = null): Generator;


	/**
	 * @param Position $position
	 * @param float $maxDistance
	 *
	 * @return EntitySearchResult|null
	 */
	public function getNearest(Position $position, float $maxDistance = PHP_INT_MAX, EntitySearchOption $option = null): ?EntitySearchResult;

	/**
	 * @param Position $position
	 * @param Vector3 $direction
	 * @param float $length
	 * @param Vector3|null $expand
	 *
	 * @return Generator|RayTraceEntityResult[]
	 */
	public function getLineOfSight(Position $position, Vector3 $direction, float $length, ?Vector3 $expand = null, EntitySearchOption $option = null): Generator;

	/**
	 * @param TriangleShape $shape
	 * @param World $world
	 * @param EntitySearchOption|null $option
	 *
	 * @return Generator|Entity[]
	 *
	 * @experimental
	 */
	public function getInsideOfTriangle(TriangleShape $shape, World $world, EntitySearchOption $option = null): Generator;

	/**
	 * @param CircularSectorShape $shape
	 * @param World $world
	 * @param float $height
	 * @param EntitySearchOption|null $option
	 *
	 * @return Generator|Entity[]
	 */
	public function getInsideOfCircularSector(CircularSectorShape $shape, World $world, float $height, EntitySearchOption $option = null): Generator;
}
