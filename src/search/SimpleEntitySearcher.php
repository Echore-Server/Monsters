<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\search;

use Generator;
use Lyrica0954\Monsters\search\algo\Algo;
use Lyrica0954\Monsters\search\shape\CircularSectorShape;
use Lyrica0954\Monsters\search\shape\TriangleShape;
use Lyrica0954\Monsters\utils\RayTraceEntityResult;
use Lyrica0954\Monsters\utils\Utils;
use pocketmine\math\Facing;
use pocketmine\math\RayTraceResult;
use pocketmine\math\Vector2;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\types\ChunkPosition;
use pocketmine\world\format\Chunk;
use pocketmine\world\Position;
use pocketmine\world\World;

class SimpleEntitySearcher implements EntitySearcher {

	protected Algo $algo;

	public function __construct(Algo $algo) {
		$this->algo = $algo;
	}

	public function getWithinSpecifyRange(Position $position, float $rangeMin, float $rangeMax, ?EntitySearchOption $option = null): Generator {
		foreach ($this->getAreaEntities($position, $rangeMax, $option) as $entity) {
			$dist = $this->algo->getDistance()->distanceBoundingBox($entity->getBoundingBox(), $position);
			if ($dist < $rangeMin || $dist > $rangeMax) {
				continue;
			}

			yield $entity;
		}
	}

	public function getAreaEntities(Position $position, float $distance, EntitySearchOption $option = null): Generator {
		$size = $this->getChunkSize(new Vector2($position->x, $position->z), $distance);
		$option = $option ?? EntitySearchOption::default();
		$count = 0;

		// for lightweight
		if ($distance >= PHP_INT_MAX - 1) {
			foreach ($position->getWorld()->getEntities() as $entity) {
				yield $entity;
			}

			return;
		}

		for ($x = $size->min->getX(); $x <= $size->max->getX(); ++$x) {
			for ($z = $size->min->getZ(); $z <= $size->max->getZ(); ++$z) {
				if (!$position->getWorld()->isChunkLoaded($x, $z)) {
					continue;
				}

				foreach ($position->getWorld()->getChunkEntities($x, $z) as $entity) {
					if ($entity->isFlaggedForDespawn() || (!$option->includeDead && !$entity->isAlive())) {
						continue;
					}

					if ($option->isExcluded($entity->getId())) {
						continue;
					}

					if (!($entity instanceof $option->entityType)) {
						continue;
					}

					if ($option->filter !== null && !(($option->filter)($entity))) {
						continue;
					}

					yield $entity;

					$count++;
					if ($count > $option->max && $option->max > 0) {
						break 3;
					}
				}
			}
		}
	}

	public function getChunkSize(Vector2 $position, float $distance): ChunkSize {
		$distance += 0.0001; // epsilon fix
		$distance += 2; // size fix World::getNearbyEntities
		$minX = ((int) floor($position->x - $distance)) >> Chunk::COORD_BIT_SIZE;
		$maxX = ((int) floor($position->x + $distance)) >> Chunk::COORD_BIT_SIZE;
		$minZ = ((int) floor($position->y - $distance)) >> Chunk::COORD_BIT_SIZE;
		$maxZ = ((int) floor($position->y + $distance)) >> Chunk::COORD_BIT_SIZE;

		return new ChunkSize(new ChunkPosition($minX, $minZ), new ChunkPosition($maxX, $maxZ));
	}

	public function getWithinRange(Position $position, float $range, EntitySearchOption $option = null): Generator {
		foreach ($this->getAreaEntities($position, $range, $option) as $entity) {
			if ($this->algo->getDistance()->distanceBoundingBox($entity->getBoundingBox(), $position) > $range) {
				continue;
			}

			yield $entity;
		}
	}

	public function getWithinSpecifyRangePlane(Vector2 $position, World $world, float $rangeMin, float $rangeMax, ?EntitySearchOption $option = null): Generator {
		$pos = Position::fromObject(new Vector3($position->x, 0, $position->y), $world);
		foreach ($this->getAreaEntities($pos, $rangeMax, $option) as $entity) {
			$bb = clone $entity->getBoundingBox();
			$bb->minY = 0;
			$bb->maxY = 0;
			$dist = $this->algo->getDistance()->distanceBoundingBox($bb, $pos);
			if ($dist < $rangeMin || $dist > $rangeMax) {
				continue;
			}

			yield $entity;
		}
	}

	public function getNearest(Position $position, float $maxDistance = PHP_INT_MAX, ?EntitySearchOption $option = null): ?EntitySearchResult {
		$e = null;
		$d = $maxDistance;

		foreach ($this->getAreaEntities($position, $maxDistance, $option) as $entity) {
			$dist = $this->algo->getDistance()->distanceBoundingBox($entity->getBoundingBox(), $position);

			if ($dist < $d) {
				$e = $entity;
				$d = $dist;
			}
		}

		if (is_null($e)) {
			return null;
		}

		return new EntitySearchResult($e, $d);
	}

	public function getLineOfSight(Position $position, Vector3 $direction, float $length, ?Vector3 $expand = null, EntitySearchOption $option = null): Generator {
		$expand = $expand ?? new Vector3(0, 0, 0);
		$min = $position;
		$max = $min->addVector($direction->multiply($length));

		foreach ($this->getAreaEntities($min, $length + 1, $option) as $entity) {
			$bb = $entity->getBoundingBox()->expandedCopy($expand->x, $expand->y, $expand->z);

			$result = null;

			if ($bb->isVectorInside($min)) {
				$result = new RayTraceResult($bb, Facing::DOWN, $min);
			} elseif ($bb->isVectorInside($max)) {
				$result = new RayTraceResult($bb, Facing::DOWN, $max);
			} else {
				$result = $bb->calculateIntercept($min, $max);
			}

			if ($result instanceof RayTraceResult) {
				yield new RayTraceEntityResult($entity, $result->getHitFace(), $result->getHitVector());
			}
		}
	}

	public function getInsideOfTriangle(TriangleShape $shape, World $world, ?EntitySearchOption $option = null): Generator {
		// 外接円を求めてもよかった
		$v = sqrt(max($shape->a->lengthSquared(), $shape->b->lengthSquared(), $shape->c->lengthSquared()));
		$v *= 1.1; // 範囲内になかったら困るから少し拡大したろ (;-;)

		$center = $shape->a->addVector($shape->b)->addVector($shape->c)->divide(3);

		foreach ($this->getAreaEntities(Position::fromObject($center, $world), $v, $option) as $entity) {
			$point = Utils::getNearestPoint($entity->getBoundingBox(), $center);

			$ab = $shape->b->subtractVector($shape->a);
			$bp = $point->subtractVector($shape->b);

			$bc = $shape->c->subtractVector($shape->b);
			$cp = $point->subtractVector($shape->c);

			$ca = $shape->a->subtractVector($shape->c);
			$ap = $point->subtractVector($shape->a);

			$c1 = $ab->cross($bp);
			$c2 = $bc->cross($cp);
			$c3 = $ca->cross($ap);

			$dotA = $c1->dot($c2);
			$dotB = $c1->dot($c3);

			if ($dotA > 0 && $dotB > 0) {
				yield $entity;
			}
		}
	}

	public function getInsideOfCircularSector(CircularSectorShape $shape, World $world, float $height, ?EntitySearchOption $option = null): Generator {
		$cos = cos(deg2rad($shape->rangeDegree / 2));
		$vcen = new Vector2($shape->center->x, $shape->center->z);

		foreach ($this->getWithinRangePlane($vcen, $world, $shape->radius, $option) as $entity) {
			$ep = new Vector2($entity->getPosition()->x, $entity->getPosition()->z);
			$ev = $entity->getPosition();

			$unitDelta = $ep->subtractVector($vcen)->normalize();
			$dot = $unitDelta->dot($shape->direction);

			if ($cos > $dot) {
				continue;
			}

			if ($ev->y + $entity->size->getHeight() < $shape->center->y) {
				continue;
			}

			if ($ev->y > $shape->center->y + $height) {
				continue;
			}

			yield $entity;
		}
	}

	public function getWithinRangePlane(Vector2 $position, World $world, float $range, ?EntitySearchOption $option = null): Generator {
		$pos = Position::fromObject(new Vector3($position->x, 0, $position->y), $world);
		foreach ($this->getAreaEntities($pos, $range, $option) as $entity) {
			$bb = clone $entity->getBoundingBox();
			$bb->minY = 0;
			$bb->maxY = 0;
			if ($this->algo->getDistance()->distanceBoundingBox($bb, $pos) > $range) {
				continue;
			}

			yield $entity;
		}
	}
}
