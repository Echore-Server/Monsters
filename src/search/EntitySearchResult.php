<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\search;

use pocketmine\entity\Entity;

class EntitySearchResult {

	public Entity $entity;

	public float $distance;

	public function __construct(Entity $entity, float $distance) {
		$this->entity = $entity;
		$this->distance = $distance;
	}

	/**
	 * Get the value of entity
	 *
	 * @return Entity
	 */
	public function getEntity(): Entity {
		return $this->entity;
	}

	/**
	 * Get the value of distance
	 *
	 * @return float
	 */
	public function getDistance(): float {
		return $this->distance;
	}
}
