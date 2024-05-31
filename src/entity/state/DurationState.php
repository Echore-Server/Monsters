<?php

declare(strict_types=1);

namespace Lyrica0954\Monsters\entity\state;

use pocketmine\entity\Living;
use RuntimeException;

abstract class DurationState extends SchedulingState {

	protected int $duration;

	public function __construct(Living $entity, int $duration) {
		parent::__construct($entity, $entity->getWorld()->getServer()->getTick() + $duration, null);
		$this->duration = $duration;
	}

	public function onNotify(int $currentTick): void {
	}

	/**
	 * @return int
	 */
	public function getDuration(): int {
		return $this->duration;
	}

	/**
	 * @param int $duration
	 */
	public function setDuration(int $duration): void {
		$this->duration = $duration;
	}

	public function getRemainTick(?int $currentTick = null): int {
		return $this->nextRunTick - ($currentTick ?? $this->entity->getWorld()->getServer()->getTick());
	}

	public function setRemainTick(int $remainTick, ?int $currentTick = null): void {
		$this->setNextRunTick(($currentTick ?? $this->entity->getWorld()->getServer()->getTick()) + $remainTick);
	}

	public function flagForRemove(): void {
		if (!$this->applied){
			throw new RuntimeException("Not applied");
		}
		$this->repeatingTick = null;
		$this->setNextRunTick(0);
	}
}
