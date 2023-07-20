<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\entity\source;

use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityMotionEvent;
use pocketmine\math\Vector3;

class MonsterMotionEvent extends EntityMotionEvent {

	const SOURCE_UNKNOWN = -1;
	const SOURCE_KNOCKBACK = 0;
	const SOURCE_SIMULATED_KNOCKBACK = 1;
	const SOURCE_CUSTOM = 2;

	protected int $source;

	public function __construct(Entity $entity, Vector3 $mot, int $source) {
		parent::__construct($entity, $mot);
		$this->source = $source;
	}

	/**
	 * Get the value of source
	 *
	 * @return int
	 */
	public function getSource(): int {
		return $this->source;
	}
}
